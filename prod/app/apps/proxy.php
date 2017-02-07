<?php

class proxy{
	static $private='0';

	static function protect($ret,$f){$fb=domain($f);
		$ret=str_replace('href="http://','href="###',$ret);
		$ret=str_replace('src="http://','src="###',$ret);
		$ret=str_replace('href="','href="http://'.$fb.'/',$ret);
		$ret=str_replace('src="','src="http://'.$fb.'/',$ret);
		$ret=str_replace('href="###','href="http://'.$fb.'/',$ret);
		$ret=str_replace('src="###','src="http://'.$fb.'/',$ret);
	return $ret;}
	
	static function get($prm){
		$f=val($prm,'url'); $f=http($f);
		if($f){
			$ret=File::curl(http($f));
			$ret=self::protect($ret,$f);
		}
		return $ret;
	}
	
	static function content($prm){$ret='';
		$rid=randid('ifr'); $f=val($prm,'url');
		$bt=input('url',$f,32).' ';
		$bt.=Ajax::j($rid.',,y|proxy,get||url','ok','btn');
		if($f)$ret=self::get($f);
	return $bt.div($ret,'',$rid);}
}
?>
