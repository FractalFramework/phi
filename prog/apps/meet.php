<?php

class meet{
static $private='1';
static $a='meet';
static $db='meet';
static $cb='mee';
static $cols=['txt','loc','day','pub'];
static $typs=['var','var','date','int'];
static $open=0;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	sqlcreate('meet_valid',array('bid'=>'int','uid'=>'int','ok'=>'int'),1);}

static function admin($p){$p['o']='1'; return appx::admin($p);}
static function headers(){add_head('csscode','');}

#edit
static function gps($d){
	$ret=input('loc',$d,28);
	$ret=input('loc',$d,28);
	$ret.=aj('cbkmap|map,request||loc',lang('ok',1),'btn');
	return $ret.span('','','cbmap');}

static function modif($p){
	$r=vals($p,['txt','day','loc']);
	if($p['id'])sqlups(self::$db,$r,$p['id']);
	return self::edit($p);}

#editor
static function form($p){return appx::form($p);}

static function edit($p){
	$p['collect']='meet_valid';
	return appx::edit($p);}

static function collect($p){return appx::collect($p);}
static function del($p){return appx::del($p);}

//static function save($p){return appx::save($p);}
static function save($p){
	$r=[ses('uid')]; foreach(self::$cols as $v)$r[]=val($p,$v,0);
	if($p['txt'])$nid=sqlsav(self::$db,$r);
	if(isset($nid))return div(self::play(['id'=>$nid]),'','mee'.$nid);}

//static function create($p){return appx::create($p);}
static function create(){
	$ret=input('day',date('Y-m-d',time()),8);
	$ret.=input('loc','','32',lang('address')).br();
	$ret.=textarea('txt','',70,4,lang('presentation'),'',216).br();
	$ret.=aj(self::$cb.'|meet,save||txt,day,loc',lang('save'),'btsav');
	return $ret;}

#check
static function checkDay($p){//p($p);
	if($p['status']==1)sqlup('meet_valid','ok',2,$p['uid']);
	elseif($p['status']==2)sqlup('meet_valid','ok',1,$p['uid']);
	return self::rendezvous($p);}

#rendezvous
static function rendezvous($p){
	$id=$p['id']; $uid=ses('uid');
	$r=sql('id,uid,ok','meet_valid','rr','where bid='.$id);
	if($r)foreach($r as $k=>$v){
		$name=profile::name($v['uid']);
		if($v['ok']==2){$c=' disactive'; $ico=ico('close').$name;}
		else{$c=' active'; $ico=ico('check').$name;}
		if($v['uid']!=$uid)$bt=tag('span','class=line opac'.$c,$ico);
		else $bt=aj('rv'.$id.'|meet,checkDay|id='.$id.',uid='.$v['id'].',status='.$v['ok'],$ico,'line'.$c);
		$rb[]=$bt;}
	if(isset($rb))return build::scroll($rb,10,'400');}

#play
static function participation($p){
	if($p['subscribe']=='ok')
		sqlsav('meet_valid',[$p['id'],ses('uid'),1]);
	elseif($p['subscribe']=='ko')
		sqldel('meet_valid',$p['uid']);
	return self::build($p);}

#pane
static function build($p){$id=$p['id']; $ret='';
	$n=sql('count(id)','meet_valid','v','where bid='.$id);
	$bt=$n. ' '.lang('participants');
	//$ret=toggle('rv'.$id.'|meet,rendezvous|id='.$id,$bt,'nfo').' ';
	$meet=self::rendezvous($p);
	if($uid=ses('uid')){
		$uid=sql('id','meet_valid','v','where bid="'.$id.'" and uid="'.$uid.'"');
		$j='ev'.$id.'|meet,participation|id='.$id.',uid='.$uid;
		if(!$uid)$ret.=aj($j.',subscribe=ok',lang('participate'),'btsav').' ';
		else $ret.=aj($j.',subscribe=ko',lang('unsubscribe'),'btdel').' ';}
	$ret.=div($meet,'','rv'.$id);
	return div($ret,'','ev'.$id);}

#stream
static function play($p){$id=$p['id']; $rid=val($p,'rid');
	$r=sql('uid,txt,day,loc',self::$db,'ra',$id);
	if(!$r)return lang('entry not exists');
	$bt=lk('/meet/'.$id,ico('link'));
	if($rid)$bt.=insertbt(lang('use'),$id.':meet',$rid);
	$ret=div($bt,'right');
	if(val($p,'conn')=='no')$txt=$r['txt']; else $txt=nl2br($r['txt']);
	$go=aj('mee'.$id.'|meet,play|id='.$id,'#'.$id,'btn');
	//$go=lk('/meet/'.$id,pic('url').' #'.$id,'btn');
	$name=profile::name($r['uid'],1);
	if(strtotime($r['day'])<ses('time'))$c='alert '; else $c='valid ';
	$date=span($r['day']?lang('date',1).' : '.$r['day']:lang('undefined'),$c.'date');
	$gps=aj('popup|map,request|request='.$r['loc'],pic('gps').' '.$r['loc'],'btn');
	$ret.=div($date.' '.$gps);//$go.' '.span(lang('by'),'small').' '.$name.' '.
	$ret.=div($txt,'tit');
	$ret.=self::build(['id'=>$id]);
	return div($ret,'paneb');}

static function stream($p){return appx::stream($p);}
static function call($p){return appx::call($p);}
	static function tit($p){return appx::tit($p);}

//com (edit)
static function com($p){return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}

?>