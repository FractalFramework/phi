<?php

/**
* How to build a menu :
* - fill the array of the structure of the button
* - load the first button in your page
**/

class _menu{
	static $private='2';

	#structure:
	//array('folder','j=ajax/lk=link','action','picto','text')
	//root before the first "/" load vertical bubble instead of horizontal menu
	static function menus(){
		$r[]=array('','lk','/','home','');
		$call_1='popup|File,fdate|fileRoot=app/demo/demo_ajax.php';//Ajax::j()
		$r[]=array('menu1','j',$call_1,'file','fdate');
		$r[]=array('menu1','j','popup|txt','text','textpad');
		$r[]=array('menu1/menu12','lk','/txt','','link to textpad');
		$r[]=array('menu1/open txt','in','txt','text','texpad in bubble');
		$r[]=array('menu2/menu21/m211','j','popup|pictos','map','pictos');
		$r[]=array('menu2/menu21/m211','j','popup|login','account-login','login');
		$r[]=array('menu2/menu21/m212','in','login','account-login','login in bubble');
		$r[]=array('menu2/menu22/m221','j','div,cback|login','account-login','login in div');
		return $r;
	}
	
	#content
	static function content($prm){
		//localization of the master array : _menu,menu
		$ret=Menu::call(array('app'=>'_menu','method'=>'menus'));
		$ret.=tag('div',array('id'=>'cback'),'');
	return $ret;}
}

?>