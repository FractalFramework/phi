<?php
class Boot{
	
	static function lang($lang='fr'){
		$r=Sql::read('ref,voc','lang','kv','where lang="'.$lang.'"');
		ses('Lang',$r);
	}
	
	static function pictos(){
		$r=Sql::read('ref,icon','icons','kv','');
		ses('Icon',$r);
	}
	
	static function colors(){
		$r=Data::read('system/colors');
		ses('Clr',$r);
	}
	
	
}