<?php

class barter{
static $private='1';
static $a='barter';
static $db='barter';
static $cb='brt';
static $cols=['typ','tit','money','price','closed'];
static $typs=['var','var','int','int','int'];
static $unit=3;
static $length=86400;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	sqlcreate('barter_prop',array('bid'=>'int','attr'=>'var','prop'=>'var','eval'=>'int'),1);
	sqlcreate('barter_chat',array('bid'=>'int','uid'=>'int','txt'=>'text'),1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function headers(){
	add_head('jscode','function barlabel(v,id){var d="";
	if(v==0)var d="broken"; if(v==25)var d="bad";
	if(v==50)var d="works"; if(v==75)var d="good";
	if(v==100)var d="new";
	inn(d,id);}');}

#sys
/*static function del($p){
	return appx::del($p);}*/
	
/*static function modif($p){
	return appx::modif($p);}*/

/*static function save($p){
	return appx::save($p);}*/

static function create($p){
	return appx::create($p);}

#editor
static function form($p){
	return appx::form($p);}

/*static function edit($p){
	$p['collect']='barter_prop';
	return appx::edit($p);}*/

static function collect($p){
	return appx::collect($p);}

//generics
static function userdate($ts,$name){
	$date=span(date('d/m/Y',$ts),'small');
	return $date.' '.small(lang('by').' '.$name).' ';}

static function leftime($end){$time=$end-ses('time');
	if($time>3600)$ret=floor($time/3600).'h ';
	elseif($time>60)$ret=floor($time/60).'min ';
	else $ret=$time.'s';
	return span($ret,'small');}

#edit
static function update($p){
	if($p['bid'])sqlup(self::$db,'txt',$p['text'],$p['bid']);
	if(val($p,'mnu'))return self::com($p);
	return self::build($p);}

static function modif($p){$id=val($p,'bid'); $mnu=val($p,'mnu');
	$txt=sql('txt',self::$db,'v',$id);
	$ret=self::textarea($txt);
	$ret.=aj(self::$cb.'|barter,update|bid='.$id.'|text',lang('save'),'btsav');
	return div($ret,'pane');}

static function del($p){$closed=val($p,'closed');
	if(!self::security(self::$db,$p['bid']))return;
	if($p['bid'] && val($p,'del')){sqldel(self::$db,$p['bid']);
		sqldel('barter_valid',$p['bid'],'bid');}
	elseif($p['bid'] && $closed==1){$p['closed']=0;//open
		sqlup(self::$db,'closed','0',$p['bid']);}
	elseif($p['bid']){$p['closed']=1;//close
		sqlup(self::$db,'closed','1',$p['bid']);}
	//if(val($p,'mnu'))return self::build($p);
	return self::build($p);}

#sav
static function save_prop($p){$idp=val($p,'idp'); 
	$r=['bid'=>val($p,'id'),'attr'=>val($p,'attr'.$idp),'prop'=>val($p,'prop'.$idp),'eval'=>val($p,'eval'.$idp)];
	if($idp && val($p,'del'))sqldel('barter_prop',$idp);
	elseif($idp=='new')$idp=sqlsav('barter_prop',$r);
	else sqlups('barter_prop',$r,$idp);
	return self::edit($p);}

static function save($p){$p['id']=val($p,'id');
	$r=vals($p,['uid','typ','tit','money','price','closed']); $r['uid']=ses('uid');
	if($p['id'])sqlups(self::$db,$r,$p['id']);
	else $p['id']=sqlsav(self::$db,$r);
	return self::edit($p);}

#add
static function edit($p){$id=val($p,'id'); $rid=val($p,'rid'); $mnu=val($p,'mnu');
	if($id)$r=sql('id,typ,tit,money,price',self::$db,'ra',$id);
	else $r=vals($p,['typ','tit','money','price','closed']);
	if(!$r['typ'])$r['typ']=1; if(!$r['money'])$r['money']=1;
	$rc=[1=>'sale',2=>'buy',3=>'exchange',4=>'donation',5=>'pickup'];
	$ret=radio('typ',$rc,$r['typ'],0,1).br();
	//echo val($p,'money');
	$bt=input('tit',$r['tit'],40,lang('entitled'));
	$bt.=input('price',$r['price'],4,lang('price'));
	$bt.=select('money',[2=>'euros',3=>'dollars',1=>'points'],$r['money']).br();
	$ret.=div('#'.$id.' '.$bt,'tit');
	$ret.=aj('newbarter|barter,edit|id='.$id.',rid='.$rid.',addprop=1|typ,tit,money,price',langp('add attribut'),'btn').br();
	if($id){
		$rb=sql('id,attr,prop,eval','barter_prop','id','where bid="'.$id.'"');
		$rc=sql('distinct(attr)','barter_prop','rv','');
		//$typ=$r['typ']==1?langp('sale'):langp('buy');
		//$ret=div($typ.$r['tit'],'stit');
		if(val($p,'addprop'))$rb['new']=[0=>'',1=>'',2=>''];
		foreach($rb as $k=>$v){
			//$inp=input('attr'.$k,$v[0],18,lang('attribut'));
			$inp=datalist('attr'.$k,$rc,$v[0],20,lang('attribut'));
			$inp.=input('prop'.$k,$v[1],18,lang('property'));
			$inp.=bar('eval'.$k,$v[2],25,'','','barlabel');
			$inp.=aj('newbarter|barter,save_prop|rid='.$rid.',id='.$id.',idp='.$k.'|attr'.$k.',prop'.$k.',eval'.$k,langpi('save'),'');
			$inp.=aj('newbarter|barter,save_prop|rid='.$rid.',id='.$id.',idp='.$k.',del=1|',langpi('del'),'');
			$ret.=div($inp);}
	}
	$ret.=aj('cbarter|barter,read|rid='.$rid.'|',langp('back'),'btn');
	$ret.=aj('newbarter|barter,save|rid='.$rid.',id='.$id.'|typ,tit,money,price',lang('save'),'btsav');
	return div($ret,'','newbarter');}

#build
static function props($d){switch($d){
	case('0'):return lang('broken');break;
	case('25'):return lang('bad');break;
	case('50'):return lang('works');break;
	case('75'):return lang('good');break;
	case('100'):return lang('new');break;}}

static function money($d){switch($d){
	case('1'):return 'unity';break;
	case('2'):return 'euro';break;
	case('3'):return 'dollar';break;}}

static function read_props($id){
	$r=sql('attr,prop,eval','barter_prop','','where bid="'.$id.'"');
	if($r)foreach($r as $k=>$v)$r[$k][2]=self::props($v[2]);
	return mktable($r);}

static function build($p){
	$id=$p['id']; $rid=val($p,'rid'); $mnu=val($p,'mnu'); $closed=val($p,'closed');
	$cols='name,typ,tit,money,price,UNIX_TIMESTAMP('.self::$db.'.up) as date';
	$where='where '.self::$db.'.id='.$id.' order by '.self::$db.'.id desc';
	$r=sqljoin($cols,self::$db,'login','uid','ra',$where);
	if(!$r)return help('id not exists','board');
	$do=$r['typ']==1?lang('sale'):lang('buy');
	$price=$r['price'].' '.lang(self::money($r['money']),1);
	$edt=aj('cbarter|barter,edit|rid='.$rid.',id='.$id,pic('edit'),'');
	$ret=div($edt.span($do,'btok').' '.$r['tit'].' '.span($price,'stit'),'tit');
	$ret.=div(self::read_props($id));
	return div($ret,'pane','brt'.$id);}

static function read(){$ret='';
	$r=sql('id,closed',self::$db,'kv','where uid="'.ses('uid').'" order by id desc');
	if($r)foreach($r as $k=>$v)$ret.=self::build(array('id'=>$k,'closed'=>$v));
	return div($ret,'','cbarter');}

#call
static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	$p['t']='tit';
	return appx::tit($p);}

//call (read)
static function call($p){
	//return self::play($p);
	$p['conn']=0;
	return appx::call($p);}

//com (edit)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}

?>