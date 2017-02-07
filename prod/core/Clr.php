<?php

class Clr{

	static function get(){
		return Data::read('system/colors');}

	static function read($d){
		$r=sesclass('Clr','get','',0);
		return $r[$d];}

	static function random(){
		$r=sesclass('Clr','get','',0);
		$r=array_values($r);
		return $r[rand(0,139)];}
		
}

?>