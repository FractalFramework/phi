<?php

class users{
static $private='6';

#reader
static function read($p){$rid=val($p,'rid');
	$r=sqljoin('name,pname,auth','profile','login','puid','','order by login.id desc limit 200');
	$bt=aj($rid.'|users,read|ip='.$rid,pic('refresh'));
	return $bt.mktable($r);}
	
//interface
static function content($p){
	$p['rid']=randid('md');
	$ret=self::read($p);
	return div($ret,'board',$p['rid']);}
}

?>