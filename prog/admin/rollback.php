<?php

class rollback{
static $private='6';

static function restore(){$ret='';
	
	return $ret;}

static function menu($p){$app=val($p,'app');
	$r[]=aj('ses,,reload||k=dev,v=prog','prog');
	$r[]=aj('ses,,reload||k=dev,v=prod','prod');
	$r[]=aj('popup,,xx|dev2prod','publish');
	return div(implode('',$r),'list');}

static function op($dr,$f){$db=''; $sb='';
	$old='_bckp/'.date('ymd').'/'.substr($dr,5).'/';
	$fa=$dr.'/'.$f; $da=filemtime($fa); $sa=filesize($fa);
	$fb='prod/'.substr($fa,5); mkdir_r($fb);
	if(is_file($fb)){$db=filemtime($fb); $sb=filesize($fb);}
	if($sa!=$sb or $da>$db){mkdir_r($old);
		if(is_file($fb))copy($fb,$old.$f); copy($fa,$fb);
		return $f;}}

static function walkMethod($dir,$file){
	return after($dir.'/'.$file,'/',1);}

static function obsoletes(){
	$ra=walk('dev2prod','walkMethod','prog','',0);
	$rb=walk('dev2prod','walkMethod','prod','',0);
	$r=array_diff($rb,$ra);
	foreach($r as $v)unlink('prod/'.$v);
	return $r;}

static function content($p){
	$old='_bckp/'.date('ymd').'/'; mkdir_r($old);
	$r=walk('dev2prod','op','_bck','',0);
	//$rb=self::obsoletes();
	$ret=div('updated','valid').' '.implode(' ',$r);
	if($rb)$ret.=div('deleted','alert').' '.implode(' ',$rb);
	$f='version.txt'; mkdir_r($f); files::write($f,date('ymd'));
	return $ret;}
}
?>
