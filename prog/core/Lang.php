<?php
class Lang{
	static $lang='fr';
	static $langs=array('en','fr');
	static $app='';
	
	static function set($p){
		$v=val($p,'lang','fr');
		self::$lang=$v; sez('lng',$v);
		return $v;
	}
	
	static function com($lang){
		return Sql::read('ref,voc','lang','kv','where lang="'.$lang.'"');
	}
	
	static function ex($d){
		$lang=ses('lng')?ses('lng'):self::$lang;
		$r=sesclass('Lang','com',$lang);
		if(array_key_exists($d,$r))return 1;}
	
	static function get($d,$o='',$no=''){
		$lang=ses('lng')?ses('lng'):self::$lang;
		$applng=ses('applng')?ses('applng'):self::$app;
		$r=sesclass('Lang','com',$lang);
		if($r)if(!array_key_exists($d,$r) && $d && strpos($d,',')===false && $d && !is_numeric($d) && !$no){
			Sql::insert('lang',array($d,'',$applng,$lang));
			$r=sesclass('Lang','com',$lang,'1');}
		$ret=isset($r[$d]) && $r[$d]?$r[$d]:$d;
		if(!$o)$ret=ucfirst($ret);
		return $ret;
	}
	
}
?>