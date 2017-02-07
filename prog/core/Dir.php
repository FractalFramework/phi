<?php

class Dir{

//dirs
static function read($dir){
if(!is_dir($dir))return;
$elements=scandir($dir); $ret=array();
foreach($elements as $k=>$v){
	if(!in_array($v,array('.','..','_notes'))){
		if(is_dir($dir.'/'.$v))$ret[$v]=self::read($dir.'/'.$v);
		else $ret[]=$v;}}
return $ret;}

static function scan($dir){
if(is_dir($dir))$r=scandir($dir); if(!isset($r))return;
foreach($r as $k=>$v)if($v=='.' or $v=='..' or $v=='_notes')unset($r[$k]);
return $r;}

static function explore($dr,$p='',$o=''){//unused
$r=scandir($dr,0); static $i; $ret=array();
foreach($r as $k=>$f){$drb=$dr.'/'.$f; $i++;
if(is_dir($drb) && $f!='..' && $f!='.' && $f!='_notes'){
	if($p=='dirs')$ret[$f]=$f; if(!$o)$ret+=self::explore($drb,$p,$o);}
if($p!='dirs')if(is_file($drb))$ret[$i]=$drb;}
return $ret;}

static function remove($dr){
if(!ses('uid'))return;
$dir=opendir($dr); $ret='';
while($f=readdir($dir)){$ret[]=$drb=$dr.'/'.$f;
if(is_dir($drb) && $f!='..' && $f!='.'){self::remove($drb); rmdir($drb);}
elseif(is_file($drb))unlink($drb);} rmdir($dr);
return $ret;}

static function mkdir_r($u){if(is_dir(before($u,'/')))return; $ret='';
$nu=explode('/',$u); if(count($nu)>10)return;
if(strpos($u,'Warning')!==false)return;
foreach($nu as $k=>$v){$ret.=$v.'/'; if(strpos($v,'.'))$v=''; 
if(!is_dir($ret) && $v){if(!mkdir($ret))echo $v.':no permissions';}}}

static function rmdir_r($dr){if(!auth(6))return; $dir=opendir($dr);
while($f=readdir($dir)){$drb=$dr.'/'.$f;
if(is_dir($drb) && $f!='..' && $f!='.'){rmdir_r($drb); rmdir($drb);}
elseif(is_file($drb)){unlink($drb); echo $drb.br();}} rmdir($dr);}

//walk
/*Apply a function to the files of a dir
$res=Dir::walk('Dir','walkMethod','db','',1);
$res=Dir::walk('','walkfunc','db',Dir::read('db'),1);*/
static function walkMethod($dir,$file){
return $dir.'/'.$file;}

static function walk($app,$method,$dir,$r='',$recursive=''){
if(!$r)$r=self::read($dir);
$ret=array(); if(substr($dir,-1)=='/')$dir=substr($dir,0,-1);
if($r)foreach($r as $k=>$v)
	if(is_array($v)){
		$rb=self::walk($app,$method,$dir.'/'.$k,$v,$recursive);
		if($recursive)$ret[$k]=$rb; else $ret=array_merge($ret,$rb);}
	elseif(is_file($dir.'/'.$v))
		if($app)$ret[$k]=$app::$method($dir,$v);
		elseif($method)$ret[$k]=$method($dir,$v);
return $ret;}

}
?>