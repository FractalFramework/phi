<?php 

class Lib{
	static function call($p){$f=val($p,'f'); $p=val($p,'p'); $o=val($p,'o');
		return call_user_func($f,$p,$o);
	}
}
?>