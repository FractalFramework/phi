<?php

/**
* How to build a desk :
* - fill the array of the structure of the button
* - note we use / at begining
* - there is 4 options : 
* 	'' (empty) : normal open
* 	'j' : controled open
* 	'in' : load in place
* 	'lk' : link
* - the loader need to know app and method where is the structure
*/

class _desk{

	#structure:
	//array('folder','//j/in/lk','action','picto','text')
	static function menus(){
		$r[]=array('','lk','/','home','home');
		$call_1='popup|file,fdate|fileRoot='.ses('dev').'/app/pub/_ajax.php';
		$r[]=array('/menu1','j',$call_1,'file','fdate');
		$r[]=array('/menu1','j','popup|txt','file-text','textpad');
		$r[]=array('/menu1/menu12','lk','/txt','file-text','link to textpad');
		$r[]=array('/menu1/textpad','in','text','text','');
		$r[]=array('/menu2/menu21/m211','','pictos','map','pictos');
		$r[]=array('/menu2/menu21/m211','j','popup|login','account-login','login');
		$r[]=array('/menu2/menu21/m212','in','login','account-login','login in bubble');
		$r[]=array('/menu2/menu22/m221','j','div,cback|login','account-login','login in div');
		return $r;
	}
	
	#content
	static function content($prm){
		//loader
		return desk::load('_desk','menus');
	}
}

?>