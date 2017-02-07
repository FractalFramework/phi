<?php

class reset{
	static $private='1';
	static function content($prm){
	$_SESSION='';
	return 'all sessions killed';}
}
?>
