<?php

class graph{
static $private='0';
static $a='graph';
static $db='graph';
static $cb='tbl';
static $cols=['tit','db','pub'];
static $typs=['var','var','int'];
static $open=0;
static $qb='';//db

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
$ret=trans::call(['db'=>$d]); $ret=trim($ret);
if(substr($ret,-7)==':table]')$ret=substr($ret,1,-7);
return $ret;}

static function nod($uid,$id){
if(!$uid && is_numeric($id))$uid=sql('uid',self::$db,'v',$id);
if($uid){$nm=sql('name','login','v',$uid);
return $nm.'/graph/'.$id;}}

static function sav_qb($uid,$id,$d){$r=explode_array($d,'¬','|');
$u=self::nod($uid,$id); db::write('usr/'.$u,$r);}

static function readb($p){
return val($p,'f');}

#edit
static function form($p){$cb=self::$cb; $ret='';
$r=vals($p,self::$cols); $uid=val($p,'uid');
foreach($r as $k=>$v){
	if($k=='tit')$ret.=div(input($k,$v?$v:'','',lang('title')));
	elseif($k=='db'){
		$ret.=autoggle('slctdb','explorer,select|tg=db,a=graph',langp('select table'),'btsav');
		$ret.=input($k,$v,63).db::bt($v);}//appx::sets($p)
	elseif($k=='pub')$ret.=div(appx::pub($k,$v,$uid));}
//$rb=db::read('usr/'.self::nod($uid,val($p,'id'))); if($rb)$v=mktable($rb,1);
return $ret;}

static function del($p){return appx::del($p);}
static function create($p){return appx::create($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function edit($p){$p['help']=1; return appx::edit($p);}

#build
static function build($p){return appx::build($p);}

static function isnum($d){
$d=str_replace(["&nbsp;",' ',','],['','','.'],$d);
return is_numeric($d)?$d:'';}

static function play($p){$ret=''; $rb=''; $rc='';
$r=self::build($p); //pr($r);
$db=explode('|',$r['db']);
$f='usr/'.$db[0]; $wg=520; $hg=320; $hgb=300;
$rb=db::read($f); //pr($rb);
if(isset($rb['_']))$rt=array_shift($rb);
$n=count($rb); $w=$wg/$n;
if($rb)foreach($rb as $k=>$v){$i=0;
	foreach($v as $ka=>$va){if($n=self::isnum($va))$rc[$i][$k]=$n; else $rd[$i][$k]=$va; $i++;}} //pr($rc);
if($rc)foreach($rc as $k=>$v){$mx=max($v); $ret.='[black,,1:attr]'; $rx='';
	$ret.='[1,'.($hg-round($hgb/($mx/current($v)))).'*'.$rt[$k].':text]';//legend-y
	$clr='#'.clrand(); $ret.='['.$clr.','.$clr.',1:attr]';
	if(count($v)==1)foreach($v as $ka=>$va){$x=round($w*$ka); $h=round($hgb/($mx/$va));
		$ret.='['.$x.','.($hg-$h).','.$w.','.$h.':rect]';}
	else foreach($v as $ka=>$va){$x=round($w*$ka); $h=$hg-round($hgb/($mx/$va)); $rx[$ka]=[$x,$h];
		if(isset($rx[$ka-1])){list($xz,$hz)=$rx[$ka-1]; //else list($xz,$hz)=[0,0];
		$ret.='['.$xz.','.($hz).','.$x.','.($h).':line]['.$x.','.($h).',2:circle]';}}}
$ret.='[black,,1:attr]';
if(isset($rd))foreach($rd as $k=>$v)$ret.='['.round($w*$k).','.($hg-1).'*'.current($v).':text]';
//[rand,black,1:attr][10,10,30,20:rect][100,100,40,80:line][300,220,200:circle]
$ret.='[0,'.$hg.','.$wg.','.$hg.':line][10,0,10,'.$hg.':line]';
for($i=0;$i<count($rc);$i++)$ret.='[0,'.($hgb-round($w*$i)).'*'.(($hgb/$mx)*$i).':text]';
$ret=svg::call(['code'=>$ret,'size'=>$wg.'/'.$hg]);
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
self::install();
return appx::content($p);}

static function api($p){
return appx::api($p);}
}

?>