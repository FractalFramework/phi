<?php
class index{
#content
static function content($p){
	return div(desk::load('desktop','com',val($p,'dir')),'','wrapper');}	
}
?>