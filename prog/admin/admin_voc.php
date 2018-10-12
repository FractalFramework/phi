<?php

class admin_voc{
static $private='6';
static $ad='admin_voc';
static $dblg='lang';
static $dbhe='help';
static $dbic='icons';
static $dblb='labels';
static $dbvc='voc';

static function headers(){
	add_head('csscode','');}

//install
static function install(){
	sqlcreate('lang',['ref'=>'var','voc'=>'var','app'=>'var','lang'=>'var']);}

static function equalize($p){
	$r=sql('ref,lang,voc',self::$db,'kkv','where app="'.$p['app'].'"');
	$rb=array_keys($r);
	foreach($rb as $k=>$v)
		if(!isset($r[$v][$p['lang']])){$txt=''; $voc='';
			if($p['lang']!='en' && isset($r[$v]['en'])){$from='en'; $txt=$r[$v]['en'];}
			if($p['lang']!='fr' && isset($r[$v]['fr'])){$from='fr'; $txt=$r[$v]['fr'];}
			if($txt)$voc=yandex::com(['from'=>$from,'to'=>$p['lang'],'txt'=>$txt]);
			sqlsav(self::$db,[$v,$voc,$p['app'],$p['lang']]);}
	return self::com($p);}

static function duplicates($p){
	$r=query('select id,ref,count(*) from lang where lang="'.$p['lang'].'" group by ref having count(*)>1','');
	return mktable($r);}

//create new language
static function create($p){
	$newlng=val($p,'newlng'); $lng='fr';
	$ret=input('newlng',$newlng);
	$ret.=aj('admlng|admin_voc,create||newlng',langp('add language'),'btn');
	if($newlng){
		$r=sql('ref,voc,app',self::$db,'rr','where lang="'.$lng.'" limit 450,50');
		foreach($r as $k=>$v){
			$ex=sql('voc',self::$db,'v','where ref="'.$v['ref'].'" and lang="'.$newlng.'"');
			if(!$ex){$v['lang']=$newlng;
				$v['voc']=yandex::com(['from'=>$lng,'to'=>$newlng,'txt'=>$v['voc']]);
				sqlsav(self::$db,$v); $r[$k]=$v;}
			else $r[$k]['voc']=$ex;}
	$ret.=mktable($r);}
	return $ret;}

static function translate($p){$voc=''; $txt=''; $copy=val($p,'copy');
	$r=sql('lang,voc',self::$db,'kv',['ref'=>$p['ref']]);
	foreach($r as $k=>$v){
		if($p['lang']!='en' && isset($r['en'])){$from='en'; $txt=$r['en'];}
		if($p['lang']!='fr' && isset($r['fr'])){$from='fr'; $txt=$r['fr'];}}
	if($copy)$voc=(($txt));//html_entity_decode//utf8_decode
	elseif($txt)$voc=yandex::com(['from'=>$from,'to'=>$p['lang'],'txt'=>$txt]);
	return $voc;}

//save
static function update($p){$rid=$p['rid'];
	sqlup(self::$db,'voc',$p[$rid],$p['id']);
	sqlup(self::$db,'app',$p['app'.$rid],$p['id']);
	if($p['lang']==lng())sesfunc('lang_com',$p['lang'],1);//update session
	return self::com($p);}

static function del($p){
	if($id=val($p,'id'))$nid=sqldel(self::$db,$id);
	if($ref=val($p,'ref'))$nid=sqldel(self::$db,$ref,'ref');
	if($p['lang']==lng())sesfunc('lang_com',$p['lang'],1);//update session
	return self::com($p);}//self::add($p).br().

static function save($p){
	$nid=sqlsav(self::$db,array($p['ref'],$p['voc'],$p['app'],$p['lang']));
	if($p['lang']==lng())sesfunc('lang_com',$p['lang'],1);//update session
	return self::com($p);}

static function addfrom($p){
	$p['voc']=yandex::com(['from'=>$p['from'],'to'=>$p['lang'],'txt'=>$p['fvoc']]);
	$p['id']=sqlsav(self::$db,array($p['ref'],$p['voc'],$p['app'],$p['lang']));
	if($p['lang']==lng())sesfunc('lang_com',$p['lang'],1);//update session
	return self::edit($p);}

static function edit($p){$rid=randid('voc');//id
	$r=sql('ref,voc,lang,app',self::$db,'ra','where id='.$p['id']);
	$ret=label($rid,$r['ref'].' ('.$r['lang'].')').input($rid,$r['voc'],16);
	$ro=sql('distinct(app)',self::$db,'rv','');
	$ret.=datalist('app'.$rid,$ro,$r['app'],8,'app');
	$ret.=aj('admlng,,x|admin_voc,update|id='.$p['id'].',rid='.$rid.',lang='.$r['lang'].',app='.$r['app'].'|'.$rid.',app'.$rid,langp('save'),'btsav');
	$ret.=aj('input,'.$rid.'|admin_voc,translate|ref='.$r['ref'].',lang='.$r['lang'],langpi('translate'),'btn');
	$del='admlng,,x|admin_voc,del|lang='.$r['lang'].',app='.$r['app'];
	$ret.=aj($del.',id='.$p['id'],langpi('del'),'btdel');
	$ret.=aj($del.',ref='.$r['ref'],langpi('del all'),'btdel').br();
	foreach(lngs() as $v)if($v!=$r['lang']){
		$id=sql('id',self::$db,'v',['ref'=>$r['ref'],'lang'=>$v]);
		if($id)$ret.=aj('popup|admin_voc,edit|id='.$id,$v,'btn');
		else $ret.=aj('popup|admin_voc,addfrom|app='.$r['app'].',lang='.$v.',ref='.$r['ref'].',from='.$r['lang'].',fvoc='.$r['voc'],$v,'btsav');}
	return $ret;}

static function open($p){$ref=val($p,'ref'); $app=val($p,'app');
	$p['id']=sql('id',self::$db,'v',['ref'=>$ref]);
	if(!$p['id'])$p['id']=sqlsav(self::$db,[$ref,'',$app,ses('lng')]);
	if($p['id'])return self::edit($p);}

static function add($p){//ref,voc
	$ref=val($p,'ref'); $voc=val($p,'voc');
	$ret=input('ref',$ref?$ref:'',16,'ref').input('voc',$voc?$voc:'',16,'voc');
	$ret.=aj('admlng,,x|admin_voc,save||app,lang,ref,voc',langp('save'),'btsav');
	return $ret;}

//table
static function select($app,$lang){
	$ret=hidden('app',$app).hidden('lang',$lang);
	//langs
	$r=sql('distinct(lang)',self::$db,'rv','');
	foreach($r as $v){$c=$v==$lang?' active':'';
		$rc[]=aj('admlng|admin_voc,com|lang='.$v.'|app',$v,'btn'.$c);}
	$bt=implode(' ',$rc).' :: ';
	//apps
	$r=sql('distinct(app)',self::$db,'rv','order by app');
	if(!$r)$r=lngs();
	$rb[]=aj('admlng,,y|admin_voc,com|app=new|lang','new','btn'.($app=='new'?' active':''));
	$rb[]=aj('admlng,,y|admin_voc,com|app=all|lang','all','btn'.($app=='all'?' active':''));
	foreach($r as $v){$c=$v==$app?' active':'';
		$rb[]=aj('admlng,,y|admin_voc,com|app='.$v.'|lang',$v,'btn'.$c);}
	$bt.=implode(' ',$rb);
	$ret.=div($bt,'pane');
	if(auth(6)){
		$ret.=aj('popup|admin_voc,add|app='.$app,langp('add'),'btn');
		$ret.=aj('admlng|admin_voc,equalize||app,lang',langp('equalize'),'btn');
		$ret.=aj('popup|core,mkbcp|b=lang',langp('backup'),'btsav');
		if(sqlex('lang_bak'))
		$ret.=aj('popup|core,rsbcp|b=lang',langp('restore'),'btdel');
		//$ret.=aj('admlng|admin_voc',langp('reload'),'btn').br();
		$ret.=aj('popup|admin_voc,duplicates|lang='.$lang,langp('duplicates'),'btn');
		$ret.=aj('admlng|admin_voc,create',langp('add language'),'btn');}
	return $ret;}

static function com($p){$rb=array();
	$app=val($p,'app','new'); $lang=val($p,'lang');
	$bt=self::select($app,$lang).br();
	if($app=='new')$wh=' and voc=""';
	elseif($app!='all')$wh=' and app="'.$app.'"'; else $wh='';
	$r=sql('id,ref,voc',self::$db,'','where lang="'.$lang.'"'.$wh.' order by ref');
	$n=count($r);
	$bt.=span($n.' '.langs('occurence',$n,1),'small');
	foreach($r as $k=>$v){
		if(auth(6))$ref=aj('popup|admin_voc,edit|id='.$v[0],$v[1],'btn');
		else $ref=$v[1];
		if($v[2])$rb[$k]=array($ref,$v[2]);
		else $rc[$k]=array($ref,$v[2]);}
	if(isset($rc))$rb=array_merge($rc,$rb);
	array_unshift($rb,array('ref',$lang));
	return $bt.mktable($rb,1);}

//content
static function content($p){$ret='';
	//self::install();
	$app=val($p,'app',''); $lang=val($p,'lang',lng());
	$ret=self::com(array('app'=>$app,'lang'=>$lang));
	return div($ret,'','admlng');}
}
?>