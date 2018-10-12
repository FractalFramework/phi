<?php

class pictos{
static $private='0';

static function injectJs(){
	return '';}
static function headers(){
	add_head('csscode','');
	add_head('jscode',self::injectJs());}

static function seechr(){$ret='';
	for($i=0;$i<300;$i++)$ret.=$i.':'.picto('&#'.$i.';',32).chr($i).br();
	return $ret;}
	
static function vars(){
	$d=file_get_contents('prog/css/pictos.css');
	$r=explode('.ic-',$d); $_POST['nb']=count($r);
	foreach($r as $v){
		$pos=strpos($v,':before');
		if($pos!==false)$ret[]=substr($v,0,$pos);}
return $ret;}

static function see(){$ret='';
	//$r=json::read('db/system/philum');
	//if($r)foreach($r as $k=>$v)$ret.=picto($k,32).' '.$k.br();
	$r=self::vars();
	if($r)foreach($r as $k=>$v)$ret.=span(picto($v,32).' '.$v,'btn').br();
	return $ret;}

static function com($p){$id=val($p,'id'); $ret='';
	$r=json::read('db/system/philum');
	foreach($r as $k=>$v)$ret.=btj(picto($k,24),'insert(\'['.$k.':picto]\',\''.$id.'\'); Close(\'popup\');').' ';
	return $ret;}

static function content($prm){
	return div(self::see(),'board cols');}
}
?>
