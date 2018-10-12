<?php

class files{
	
static function write($f,$d){
$h=fopen($f,'w+'); $w=fwrite($h,$d); fclose($h);
if($w===false)return 'error';}

static function read($f){
if(is_file($f))$fp=fopen($f,'r'); $ret='';
if(isset($fp)){while(!feof($fp))$ret.=fread($fp,8192); fclose($fp);}
return $ret;}

static function context($f){return self::read($f);
ini_set('user_agent','Mozilla/5.0');
$r=array('http'=>array('method'=>'GET','header'=>'User-agent: Mozilla/5.0','ignore_errors'=>1,'request_fulluri'=>true,'max_redirects'=>0));
$context=stream_context_create($r);
$h=get_headers($f,false);//$http_response_header
if(strpos($h[0],'404'))return '404';
return file_get_contents($f,false,$context);}

static function curl($f){$ch=curl_init($f); //curl_setopt($ch,CURLOPT_URL,$f);
$r=array('HTTP_ACCEPT: Something','HTTP_ACCEPT_LANGUAGE: fr, en, es','HTTP_CONNECTION: Something','Content-type: application/x-www-form-urlencoded','User-agent: Mozilla/5.0');
curl_setopt($ch,CURLOPT_HTTPHEADER,$r);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch,CURLOPT_REFERER,'http://www.google.fr/');
$ret=curl_exec($ch); curl_close($ch); return $ret;}

static function get($f){
$ret=file_get_contents($f);
//if(!$ret)$ret=self::read($f);
if(!$ret)$ret=self::context($f);
//if(!$ret)$ret=self::curl($f);
return $ret;}

static function gz($f,$fb){
$w=self::write($fb,implode('',gzfile($f)));
if($w===false)return 'error';}
static function gunz($f){return readgzfile($f);}

static function writegz($f,$d){$gz=gzopen($f,'w9');
gzwrite($gz,$d); return gzclose($gz);}
static function readgz($f){$zd=gzopen($f,'r');
$d=gzread($zd,filesize($f)); gzclose($zd); return $d;}

static function day($f,$format='ymd.His'){
if(is_file($f))return date($format,filemtime($f));}
static function size($f,$dateformat=''){
if(is_file($f))return round(filesize($f)/1024,2).'ko';}

static function fsize($p){$f=$p['f'];
if(is_file($f))return self::size($f); else return 'file not found: '.$f;}
static function fdate($p){$f=$p['f']; $format=val($p,'o','Ymd');
if(is_file($f))return self::day($f,$format); else return 'file not found: '.$f;}
static function brut($p){
$ret=self::read($p['f']);
return tag('pre','',$ret);}

static function mkthumb($nm,$w,$h=''){if(!$h)$h=$w;
$fa='img/full/'.$nm; //mkdir_r($fa);
$fb='img/mini/'.$nm; //mkdir_r($fb);
$fc='img/medium/'.$nm; //mkdir_r($fc);
mkthumb($fa,$fb,170,170,0);
list($wa,$ha)=getimagesize($fa);
if($wa>$w or $ha>$h)mkthumb($fa,$fc,$w,$h,0);}

static function saveimg($f,$prf,$w,$h='',$or=''){$er=1;
if(substr($f,0,4)!='http')return;
if(strpos($f,'?'))$f=before($f,'?');
$xt=ext($f); if(!$xt)$xt='.jpg';
$nm=$prf.substr(md5($f),0,10); $h=$h?$h:$w;
$fa='img/full/'.$nm.$xt; mkdir_r($fa);
$ok=@copy($f,$fa);
if(!$ok){$d=@file_get_contents($f); if($d)$er=selff::write($fa,$d);}
if($ok or !$er){if(filesize($fa))self::mkthumb($nm.$xt,$w,$h);
	//sqlsav(self::$db,[$id,$or,$nm.$xt]);
	return $nm.$xt;}}
}

?>