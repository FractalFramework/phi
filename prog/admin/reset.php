<?php

class reset{
	static $private='6';
	static function content($prm){
	$_SESSION='';
	return 'all sessions killed';}
}
?>
