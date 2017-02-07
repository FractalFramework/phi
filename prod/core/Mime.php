<?php
class Mime{
	static $app='';
	
	static function com($app){
		return Sql::read('ref,icon','mimes','kv','');
	}
	
	static function get($d,$o=''){
		$r=sesclass('Mime','com','',1); //p($r);
		if(!array_key_exists($d,$r) && $d)
			Sql::insert('mimes',array($d,''));
		$ret=isset($r[$d]) && $r[$d]?$r[$d]:$d;
		if($o)$ret=icon($ret);
		return $ret;
	}
	
}