<?php

class icons{

	static function buildIcons($dir,$file){
		if(strpos($file,'4x')){
		list($name,$ext)=explode('.',$file);
		$name=str_replace('-4x','',$name);
		$ret=icon($name,4);
		return tag('span','class=icon',$ret.br().$name);}
	}
	
	static function content($prm){		
		$links=Dir::walk('icons','buildIcons','icon');
		$ret=implode(' ',$links);
		$ret.=tag('div',array('class'=>'clear'),'');
		return tag('section',array('class'=>''),$ret);
	}
	
}

?>