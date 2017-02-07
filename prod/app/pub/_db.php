<?php

class _db{

	static function save($prm){
		$root=val($prm,'root');
		$inp1=val($prm,'inp1');
		if($inp1)Data::add($root,$inp1);
		return self::read($root);
	}
	
	static function add($root){
		$params=array('com'=>'div,dataTable','app'=>'_db,save',
			'prm'=>'root='.$root,'inp'=>'inp1');
		$ret=input('inp1','');
		$ret.=Ajax::call($params,'Add','btn');
		return $ret;
	}
	
	static function read($root){
		$datas=Data::read($root);
		return Build::table($datas);
	}
	
	static function init($root){
		if(!is_file(Data::file($root))){
			$datas=array(1=>'one',2=>'two',3=>'three');
			Data::write($root,$datas);}
	}
	
	static function content(){
		$root='one/two'; //Data::del($root);
		self::init($root);
		$datas=Data::read($root); //p($datas);
		$ret=tag('div','',self::add($root));
		$ret.=tag('div','id=dataTable',Build::table($datas));
		return $ret;
	}
	
}