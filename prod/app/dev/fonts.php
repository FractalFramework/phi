<?php

class fonts{
	static $private='0';

	static function injectJs(){
		return '';
	}
	static function headers(){
		Head::add('csscode','');
		Head::add('jscode',self::injectJs());
	}
	
	static function see(){
	}
	
	static function content($prm){$ret='';
		for($i=0;$i<300;$i++)$ret.=$i.' '.picto(chr($i)).br();
		return $ret;
	}
}
?>
