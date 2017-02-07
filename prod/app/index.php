<?php

class index{
	
	#content
	static function content($prm){
		return Desk::load('desktop','com',val($prm,'dir'));
	}
	
}

?>