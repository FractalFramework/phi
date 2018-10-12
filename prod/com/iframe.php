<?php

class iframe{

static function get($p){
	$f=val($p,'url'); $f=http($f);
	$w=val($p,'w','580'); $h=val($p,'h','300');
	if($f)return iframe($f,$w,$h);}
	
static function getcode($p){
	return textarea('',htmlentities(self::get($p)),40,4,'','console');}

static function content($p){$ret='';
	$rid=randid('ifr'); $f=val($p,'url');
	$bt=input('url',$f,32).' ';
	$bt.=aj($rid.',,y|iframe,get||url','ok','btn');
	$bt.=popup('iframe,getcode||url','code','btn');
	if($f)$ret=self::get($f);
	return $bt.div($ret,'',$rid);}
}
?>
