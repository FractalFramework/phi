<?php
class Icon{
	static $app='';
	
	static function ex($d){
		$r=sesclass('Icon','com','');
		if(array_key_exists($d,$r))return 1;}
	
	static function com($app){
		return Sql::read('ref,icon','icons','kv','');}
	
	static function get($d,$o=''){
		$r=sesclass('Icon','com','');
		if(!array_key_exists($d,$r) && $d && !is_numeric($d)){
			Sql::insert('icons',array($d,''));
			$r=sesclass('Icon','com','',1);}
		$ret=isset($r[$d]) && $r[$d]?$r[$d]:$d;
		if($o)$ret=pic($ret);
		return $ret;}
	
}