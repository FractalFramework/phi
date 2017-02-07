<?php

class admin_lib{
static $private='1';
static $db='syslib';
static $maj='';

static function install(){
Sql::create(self::$db,['func'=>'var','vars'=>'var','code'=>'var','txt'=>'text','lang'=>'var'],1);}

//edit
static function edit(){
$ret=Form::com(['table'=>self::$db]);}

//save
static function save($p){
$func=val($p,'func'); $lang=val($p,'lang');
$w='where func="'.$func.'" and lang="'.$lang.'"';
$id=Sql::read('id',self::$db,'v',$w);
if($id){
	$txt=Sql::read('txt',self::$db,'v','where id='.$id);
	if($txt && !$p['txt'])$p['txt']=$txt;
	Sql::updates(self::$db,$p,$id);}
else $id=Sql::insert(self::$db,$p);
if(isset(self::$maj[$id]))unset(self::$maj[$id]);
return $id;}

static function update($p){
$id=val($p,'id'); $txt=val($p,'tx'.$id); $rid=val($p,'rid');
Sql::update(self::$db,'txt',$txt,$id);
return tag('pre','',($txt));}

static function modif($p){$id=val($p,'id');
$txt=Sql::read('txt',self::$db,'v',['id'=>$id]);
$ret=textarea('tx'.$id,$txt,60,6);
$ret.=aj('md'.$id.'|admin_lib,update|id='.$id.'|tx'.$id,pico('save'));
return div($ret,'','md'.$id);}

//read
static function seecode($p){$id=val($p,'id');
$ret=Sql::read('code',self::$db,'v','where id='.$id);
return div(Build::Code($ret),'paneb');}

//build (methods)
static function build($f){$rf=explode('/',$f);
$d=File::read($f);
$ra=explode('function ',$d);
foreach($ra as $v){
	$fnc=before($v,'{',1);
	$vr=explode('(',$fnc); $func=$vr[0];
	$vars=(isset($vr[1])?substr($vr[1],0,-1):'');
	$code=trim(accolades($v));
	if($code)$rb[]=['func'=>$func,'vars'=>$vars,'code'=>$code,'txt'=>'','lang'=>ses('lng')];}
return $rb;}

//operation
static function reflush(){
self::$maj=Sql::read('id',self::$db,'k','where lang="'.ses('lng').'"');
$f='prog/lib.php';
$r=self::build($f,'lib');
if($r)foreach($r as $v)$rb[]=self::save($v);
if(isset(self::$maj))foreach(self::$maj as $k=>$v)Sql::delete(self::$db,$k);//obsoletes
if(isset($rb))return implode(',',$rb);}

//menu
static function menu($p){
$r=Sql::read('id,func,vars,txt',self::$db,'rr','where lang="'.ses('lng').'"');
if($r)foreach($r as $k=>$v){$id=$v['id'];
	$bt=tag('h2','',$v['func'].'('.$v['vars'].')');
	if(auth(6))$bt.=aj('popup|admin_lib,seecode|id='.$id,langp('view'));
	if(auth(6))$bt.=aj('md'.$id.'|admin_lib,modif|id='.$id,langp('modif')).br().br();
	$bt.=div(tag('pre','',($v['txt'])),'','md'.$id);
	$ret[]=div($bt,'board').br();
}
if(isset($ret))return implode('',$ret);}

//interface
static function content($p){
self::install();
$rid=randid('dcl');
$bt=aj($rid.'|admin_lib,reflush|rid='.$rid,langp('update'),'btn');
$bt.=aj('popup|Sql,rsbcp|b=syslib',langp('restore'),'btdel');
$bt.=aj('popup|Sql,mkbcp|b=syslib',langp('backup'),'btsav');
$bt.=aj('popup|admin_lib,menu|o=1',langp('view'),'btn');
$ret=self::menu($p);
return $bt.div($ret,'board',$rid);}
}
?>
