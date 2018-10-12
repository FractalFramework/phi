<?php

class tabler{
static $private='0';
static $a='tabler';
static $db='tabler';
static $cb='tbl';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];
static $open=0;
static $qb='db';

function __construct(){
$r=['a','db','cb','cols','qb'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']='1';
return appx::admin($p);}

static function titles($p){
return appx::titles($p);}

static function injectJs(){return '';}

static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

//trans
static function trans($d){
$ret=trans::call(['txt'=>$d]); $ret=trim($ret);
if(substr($ret,-7)==':table]')$ret=substr($ret,1,-7);
$ret=str_replace(['[м','мм'],['[','м'],$ret);
return $ret;}

static function nod($uid,$id){
if(!$uid && is_numeric($id))$uid=sql('uid',self::$db,'v',$id);
if($uid){$nm=sql('name','login','v',$uid);
return $nm.'/tabler/'.$id;}}

static function sav_qb($uid,$id,$d){$r=explode_array($d,'м','|');
$u=self::nod($uid,$id); db::write('usr/'.$u,$r);}

static function readb($p){$f=val($p,'f');
$r=db::read('usr/'.$f); return mktable($r);}

#edit
static function form($p){$cb=self::$cb; $ret='';
$r=vals($p,self::$cols); $uid=val($p,'uid');
foreach($r as $k=>$v){
	if($k=='tit'){$ret.=div(input($k,$v?$v:'','',lang('title')).hlpbt('table html'));
		$ret.=autoggle('slctdb','explorer,select|tg=txt,a=tabler',langp('select table'),'btsav');}
	elseif($k=='txt'){$rb=db::read('usr/'.self::nod($uid,val($p,'id')));
		if($rb)$v=mktable($rb,1); elseif($v)$v=conn::mktable($v);
		else $v=hlpxt('paste html table here');
		$ret.=div($v,'editarea',$k,'',['contenteditable'=>'true']);}
	elseif($k=='pub')$ret.=div(appx::pub($k,$v,$uid));}
return $ret;}

static function del($p){return appx::del($p);}

static function save($p){
$a=self::$a; $db=self::$db; $cb=self::$cb;
$txt=self::trans(val($p,'txt')); $r=[ses('uid')];//$r=explorer::repair($r);
foreach(self::$cols as $v){if($v=='txt')$r[]=$txt; else $r[]=val($p,$v);}
$p['id']=sqlsav($db,$r);
self::sav_qb('',$p['id'],$txt);
return $a::edit($p);}

static function create($p){
return appx::create($p);}

static function modif($p){$id=val($p,'id');
$a=self::$a; $db=self::$db;
$r=vals($p,self::$cols);
$r['txt']=self::trans($r['txt']);
sqlups($db,$r,$id);
self::sav_qb('',$id,$r['txt']);
return $a::edit($p);}

static function edit($p){return appx::edit($p);}

#build
static function build($p){return appx::build($p);}

static function play($p){
$p['conn']=0; $ret='';
$r=self::build($p);
$f=self::nod($r['uid'],$p['id']);
$rb=db::read($f);
$ret=lkb('/api/db/f='.$f,pic('code')); $fb=self::nod(ses('uid'),$r['tit']);
if($r['uid']==ses('uid'))$ret.=aj('play'.$p['id'].'|tabler|edit=1,id='.$p['id'],langpi('edit'),'btn');
if(ses('uid'))$ret.=popup('explorer,opsav|op=import,f=/'.$fb.'.php,nm=/'.$f.'.php',langp('save datas'),'btsav');//todo: f, fb divide w usr
$ret=div($ret,'right');
$ret.=div($r['tit'],'tit');
if($rb)$txt=mktable($rb);
else $txt=conn::mktable($r['txt']);
//$ret.=db::bt($f);
$ret.=div($txt,'txt');
return $ret;}

static function stream($p){
return appx::stream($p);}

#interfaces
static function tit($p){
return appx::tit($p);}

//call (read)
static function call($p){
return div(self::play($p),'','play'.$p['id']);}

//com (write)
static function com($p){
return appx::com($p);}

//interface
static function content($p){
//self::install();
return appx::content($p);}

static function api($p){
return appx::api($p);}
}

?>