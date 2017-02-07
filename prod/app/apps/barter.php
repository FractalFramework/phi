<?php

class barter{
static $private='0';
static $unit=3;
static $length=86400;

//install
static function install(){
	Sql::create('barter_lead',array('uid'=>'int','btyp'=>'var','btit'=>'var','money'=>'int','price'=>'int','closed'=>'int'),1);
	Sql::create('barter_prop',array('idbarter'=>'int','attr'=>'var','prop'=>'var','eval'=>'int'),1);
	Sql::create('barter_chat',array('idbarter'=>'int','uid'=>'int','txt'=>'text'),1);}

static function headers(){
	Head::add('jscode','function barlabel(v,id){var d="";
	if(v==0)var d="broken"; if(v==25)var d="bad";
	if(v==50)var d="works"; if(v==75)var d="good";
	if(v==100)var d="new";
	inn(d,id);}');}

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
	if($p['idbarter'])Sql::update('barter_lead','txt',$p['text'],$p['idbarter']);
	if(val($p,'mnu'))return self::com($p);
	return self::build($p);}

static function modif($p){$id=val($p,'idbarter'); $mnu=val($p,'mnu');
	$txt=Sql::read('txt','barter_lead','v','where id='.$id);
	$ret=self::textarea($txt);
	$ret.=aj('pllscnt,,x|barter,update|mnu='.$mnu.',idbarter='.$id.'|text',lang('save'),'btsav');
	return div($ret,'pane');}

static function del($p){$closed=val($p,'closed');
	if(!self::security('barter_lead',$p['idbarter']))return;
	if($p['idbarter'] && val($p,'del')){Sql::delete('barter_lead',$p['idbarter']);
		Sql::delete('barter_valid',$p['idbarter'],'idbarter');}
	elseif($p['idbarter'] && $closed==1){$p['closed']=0;//open
		Sql::update('barter_lead','closed','0',$p['idbarter']);}
	elseif($p['idbarter']){$p['closed']=1;//close
		Sql::update('barter_lead','closed','1',$p['idbarter']);}
	//if(val($p,'mnu'))return self::build($p);
	return self::build($p);}

///////

static function save_prop($p){$idp=val($p,'idp'); 
	$r=['idbarter'=>val($p,'id'),'attr'=>val($p,'attr'.$idp),'prop'=>val($p,'prop'.$idp),'eval'=>val($p,'eval'.$idp)];
	if($idp && val($p,'del'))Sql::delete('barter_prop',$idp);
	elseif($idp=='new')$idp=Sql::insert('barter_prop',$r);
	else Sql::updates('barter_prop',$r,$idp);
	return self::edit($p);}

static function save($p){$p['id']=val($p,'id');
	$r=vals($p,['uid','btyp','btit','money','price','closed']); $r['uid']=ses('uid');
	if($p['id'])Sql::updates('barter_lead',$r,$p['id']);
	else $p['id']=Sql::insert('barter_lead',$r);
	return self::edit($p);}

#add
static function edit($p){$id=val($p,'id'); $rid=val($p,'rid'); $mnu=val($p,'mnu');
	if($id)$r=Sql::read('id,btyp,btit,money,price','barter_lead','ra','where id="'.$id.'"');
	else $r=vals($p,['btyp','btit','money','price','closed']);
	if(!$r['btyp'])$r['btyp']=1; if(!$r['money'])$r['money']=1;
	$ret=radio([1=>lang('sale'),2=>lang('buy'),3=>lang('exchange'),4=>lang('donation'),5=>lang('pickup')],'btyp',$r['btyp']).br();
	//echo val($p,'money');
	$bt=input('btit',$r['btit'],40,lang('entitled'));
	$bt.=input('price',$r['price'],4,lang('price'));
	$bt.=select('money',[2=>'euros',3=>'dollars',1=>'points'],$r['money']).br();
	$ret.=div('#'.$id.' '.$bt,'tit');
	$ret.=aj('newbarter|barter,edit|id='.$id.',rid='.$rid.',addprop=1|btyp,btit,money,price',langp('add attribut'),'btn').br();
	if($id){
		$rb=Sql::read('id,attr,prop,eval','barter_prop','id','where idbarter="'.$id.'"');
		$rc=Sql::read('distinct(attr)','barter_prop','rv','');
		//$btyp=$r['btyp']==1?langp('sale'):langp('buy');
		//$ret=div($btyp.$r['btit'],'stit');
		if(val($p,'addprop'))$rb['new']=[0=>'',1=>'',2=>''];
		foreach($rb as $k=>$v){
			//$inp=input('attr'.$k,$v[0],18,lang('attribut'));
			$inp=datalist($rc,'attr'.$k,$v[0],20,lang('attribut'));
			$inp.=input('prop'.$k,$v[1],18,lang('property'));
			$inp.=bar('eval'.$k,$v[2],25,'','','barlabel');
			$inp.=aj('newbarter|barter,save_prop|rid='.$rid.',id='.$id.',idp='.$k.'|attr'.$k.',prop'.$k.',eval'.$k,langpi('save'),'');
			$inp.=aj('newbarter|barter,save_prop|rid='.$rid.',id='.$id.',idp='.$k.',del=1|',langpi('del'),'');
			$ret.=div($inp);}
	}
	$ret.=aj('cbarter|barter,read|mnu='.$mnu.',rid='.$rid.'|',langp('back'),'btn');
	$ret.=aj('newbarter|barter,save|mnu='.$mnu.',rid='.$rid.',id='.$id.'|btyp,btit,money,price',lang('save'),'btsav');
	return div($ret,'','newbarter');}

///////////////////////

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
	$r=Sql::read('attr,prop,eval','barter_prop','','where idbarter="'.$id.'"');
	if($r)foreach($r as $k=>$v)$r[$k][2]=self::props($v[2]);
	return Build::table($r);}

static function build($p){
	$id=$p['id']; $rid=val($p,'rid'); $mnu=val($p,'mnu'); $closed=val($p,'closed');
	$cols='name,btyp,btit,money,price,UNIX_TIMESTAMP(barter_lead.up) as date';
	$where='where barter_lead.id='.$id.' order by barter_lead.id desc';
	$r=Sql::read_inner($cols,'barter_lead','login','uid','ra',$where);
	if(!$r)return lang('not exists');
	$do=$r['btyp']==1?lang('sale'):lang('buy');
	$price=$r['price'].' '.lang(self::money($r['money']),1);
	$edt=aj('cbarter|barter,edit|mnu='.$mnu.',rid='.$rid.',id='.$id,pico('edit'),'');
	$ret=div($edt.span($do,'btok').' '.$r['btit'].' '.span($price,'stit'),'tit');
	$ret.=div(self::read_props($id));
	return div($ret,'pane','brt'.$id);}

static function read(){$ret='';
	$r=Sql::read('id,closed','barter_lead','kv','where uid="'.ses('uid').'" order by id desc');
	if($r)foreach($r as $k=>$v)$ret.=self::build(array('id'=>$k,'closed'=>$v));
	return div($ret,'','cbarter');}

#call
static function tit($p){$id=val($p,'id');
	return Sql::read('txt','barter_lead','v','where id='.$id);}

static function call($p){$id=val($p,'id');
	$r=Sql::read('txt,answ,UNIX_TIMESTAMP(up) as date','barter_lead','ra','where id='.$id);
	//$ret=div(langp('barter'),'stit');
	$ret=div(nl2br($r['txt']),'tit'); $r['idbarter']=$id;
	$ret.=self::read($r);
	return div($ret,'paneb');}

static function menu($p){$id=val($p,'id'); $rid=val($p,'rid'); $ret='';
	$cols='id,UNIX_TIMESTAMP(up) as date,closed';
	$r=Sql::read($cols,'barter_lead','','where uid="'.ses('uid').'" order by id desc');
	if($r)foreach($r as $k=>$v){
		$p['idbarter']=$v[0]; $p['closed']=$v[2]; $date=date('d/m/Y',$v[1]);
		if(ses('time')-$v[1]>self::$length)$closed=1; else $closed=0;
		//if($v[2]==1)$closed=1; else $closed=0;
		$prms='mnu=1,rid='.$rid.',idbarter='.$v[0].',closed='.$v[2];
		$ret.=aj('pllscnt|barter,com|'.$prms,'#'.$v[0].' '.span($date,'date'),$closed?'':'active').' ';
		//$sav=telex::publishbt($v[0],'barter');
		//$ret.=div($bt.$sav,'menu');
		}
	return div($ret,'list');}

static function com($p){$rid=val($p,'rid');
	$id=val($p,'idbarter'); $edt=val($p,'edt'); $mnu=val($p,'mnu'); $p['mnu']=$mnu;
	$bt=aj('pllscnt|barter,com|edt=1,rid='.$rid,langp('new'),'btsav').' ';
	$bt.=aj('pllscnt|barter,com|mnu=1,rid='.$rid,langp('menu'),'btn');
	$ret=div($bt);//div(help('vote'),'btit')..br()
	if($id)$ret.=self::build($p);
	elseif($mnu)$ret.=self::menu($p);
	elseif($edt)$ret.=div(self::edit($p),'pane','');
	return div($ret,'','pllscnt');}

#content
static function content($p){$ret='';
	self::install();
	if(isset($p['param']))$p['idbarter']=$p['param'];
	$ret=aj('pllscnt|barter,read',pic('list'),'btn').' ';
	$ret.=hlpbt('barter_app').' ';
	if(ses('uid'))$ret.=aj('pllscnt|barter,edit',pic('plus').' '.lang('new'),'btn').br().br();
	//root
	if(isset($p['idbarter']))$res=self::build($p);
	else $res=self::read();
	$ret.=tag('div',array('id'=>'pllscnt'),$res);
	return $ret;}
}

?>