<?php

class _vue{

static function test($p){
	$p1=val($p,'p1','hello1'); $p2=val($p,'p2','hello2'); $p3=val($p,'p3','hello3');
	$datas=['var1'=>$p1,'var2'=>$p2,'var3'=>$p3,'url'=>'http://ph1.fr'];
	$template='[[_var1*class=btn:div][_var2*div:tag][_var3*_url:a]*:div]';
	return Vue::read($datas,$template);}
	
#content
static function content($p){
	$ret=self::test($p);
	return $ret;}
}
?>
