<?php

class test{
	static $private='6';

	static function injectJs(){}
	static function headers(){
		Head::add('csscode','');
		Head::add('jscode',self::injectJs());
	}
	
	static function admin(){
		$r[]=array('','j','popup|test,content','plus',lang('open'));
		return $r;
	}
	
	static function result($prm){
		$msg=val($prm,'msg');
		$content=val($prm,'inp1','nothing');
		return $msg.': '.$content;
	}
	
	static function content($prm){
		$p['lat']=48.8390804;
		$p['lon']=2.23537670;
		$p['type']='city';
		//$ret=App::open('gps',$p);
		$ret=App::open('Gps',$p);
		return div($ret,'');
	}
}
?>