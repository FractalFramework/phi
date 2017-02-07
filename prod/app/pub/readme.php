<?php

class readme{

	static function read($prm){
		$f=val($prm,'file');
	    $ret=File::read($f);
		$ret=tag('pre','',$ret);
	return div($ret,'pane');}
	
	#content
	static function content($prm){
		$f=val($prm,'file');
		if($f)return self::read($prm,'');
		$ret=aj('pagup|readme,read|file=readme.txt',lang('readme'),'btn');
		$ret.=aj('pagup|readme,read|file=releases.txt',lang('releases'),'btn');
	return $ret;}
}
?>
