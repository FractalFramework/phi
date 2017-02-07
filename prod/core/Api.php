<?php

class Api{

	static function build($r){
		return json_encode($r);
	}
	
	//telex
	static function call($p){
		return telex::call($p);
	}
	static function post($p){
		$msg=get('msg');
		p($p);
	}
	
	static function content($p){
		$app=val($p,'app'); ses('app',$app);
		$ret=Menu::call(array('app'=>'Api','method'=>'menus','css'=>'fix'));
		return $ret;
	}
	
}

?>