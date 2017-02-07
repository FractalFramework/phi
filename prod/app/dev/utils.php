<?php

class utils{
	static $private='1';
	
	static function value($prm){
		return $prm['value'];
	}
	
	static function result($prm){
		return $prm['inp1'].': '.$prm['msg'];
	}

	static function resistance($prm){		
		return implode('',array_fill(0,1000,'123456789 '));
	}

	static function content($prm){$ret='';
		return hlpbt('ffw');
	}
	
}

?>