<?php

class proxy{
static $private='0';

static function admin(){
$r[]=['com','pop','proxy,com','','com'];
$r[]=['com','pop','proxy,comim','','img'];
$r[]=['com','j','prx|proxy,deldr','','del'];
return $r;}

static function protect($ret,$f){$fb=domain($f);
$ret=str_replace('href="http://','href="###',$ret);
$ret=str_replace('src="http://','src="###',$ret);
$ret=str_replace('href="','href="http://'.$fb.'/',$ret);
$ret=str_replace('src="','src="http://'.$fb.'/',$ret);
$ret=str_replace('url(/"','url(http://'.$fb.'/',$ret);
$ret=str_replace('href="###','href="http://',$ret);
$ret=str_replace('src="###','src="http://',$ret);
return $ret;}

static function get($p){
$f=val($p,'url'); $f=http($f);
if($f){
	if(is_img($f))$ret=img($f);
	else $ret=files::curl(http($f));}
	//$ret=self::protect($ret,$f);
return $ret;}

static function delf($p){
$f=val($p,'f');
if($f)unlink($f);
if($f.'.gz')unlink($f.'.gz');
return 'del:'.$f;}

static function deldr($p){$ret='';
$dr='usr/ifr/'; $f='usr/ifr'.date('ymd').'.tar';
if(!is_file($f.'.gz') && !is_file($f))$ret.=tar::buildFromdir($f,$dr);
rmdir_r($dr);
$ret.=aj('popup|proxy,delf|f='.$f,'x','btn');
return $ret;}

static function getim($p){
$u=val($p,'urim'); $ret=''; $min=''; $max=''; $n='';
$r=preg_split('/[()]/',$u);
if(isset($r[1])){
	if(strpos($r[1],'-'))list($min,$max)=explode('-',$r[1]);
	elseif(strpos($r[1],','))$rb=explode(',',$r[1]);}
$l=strlen($min);
$dr='usr/ifr/'; mkdir_r($dr);
if(isset($rb))foreach($rb as $v){$f=$r[0].$v.$r[2]; $fa=$dr.after($f,'/');
	$ok=@copy($f,$fa); $ret.=img('/'.$fa);}
else for($i=$min;$i<=$max;$i++){
	if($l==2){if($i<=9)$n='0'.$i; else $n=$i;}
	elseif($l==3){if($i<=9)$n='00'.$i; elseif($i<=99)$n='0'.$i; else $n=$i;}
	elseif($l==4){if($i<=9)$n='000'.$i; elseif($i<=999)$n='00'.$i; elseif($i<=99)$n='0'.$i; else $n=$i;}
	$f=$r[0].$n.(isset($r[2])?$r[2]:''); $fa=$dr.after($f,'/');
	//if(fopen($f,'r'))
	$ok=@copy($f,$fa);
	//if(!$ok){$d=@file_get_contents($f); if($d)$er=files::write($fa,$d);}
	$ret.=img('/'.$fa);}
return $ret;}

static function comim($p){
$f=val($p,'url');
$ret=input('urim',$f,32).' ';
$ret.=aj('popup|proxy,getim||urim','ok','btn');
return $ret;}

static function com($p){
$f=val($p,'url');
return inputcall('popup|proxy,get||url','url',$f,32);
$ret=input('url',$f,32).' ';
$ret.=aj('popup|proxy,get||url','ok','btn');
return $ret;}

static function content($p){$ret='';
$f=val($p,'url');
if($f)$ret=self::get($f);
else $ret=self::com($p);
return div($ret,'','prx');}
}
?>
