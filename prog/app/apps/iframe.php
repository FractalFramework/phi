<?php

class iframe{
	
	static function get($prm){
		$f=val($prm,'url'); $f=http($f);
		$w=val($prm,'pagewidth','680')-40;
		if($f){
			$p=array('width'=>$w,'height'=>($w*0.6),'frameborder'=>'0','src'=>$f);
			return tag('iframe',$p,'');
		}
	}
	
	static function content($prm){$ret='';
		$rid=randid('ifr'); $f=val($prm,'url');
		$bt=input('url',$f,32).' ';
		$bt.=Ajax::j($rid.',,y|iframe,get||url','ok','btn');
		if($f)$ret=self::get($f);
	return $bt.div($ret,'',$rid);}
}
?>
