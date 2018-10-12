<?php

class readme{

	static function read($prm){
		$f=val($prm,'file');
	    $ret=files::read($f);
		$ret=tag('pre','',$ret);
	return div($ret,'pane');}
	
	#content
	static function content($prm){
		$f=val($prm,'file');
		if($f)return self::read($prm,'');
		$ret=pagup('readme,read|file=readme.txt',lang('readme'),'btn');
		$ret.=popup('devnote',lang('releases'),'btn');
	return $ret;}
}
?>