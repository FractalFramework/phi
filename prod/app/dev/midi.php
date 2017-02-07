<?php

class midi{
	//used by system
	static $private='1';
	
	static function injectJs(){
		return '';
	}
	static function headers(){
		Head::add('csscode','');
		Head::add('jscode',self::injectJs());
	}
	
	static function admin(){
		$r[]=array('','j','popup|midi,content','plus',lang('open'));
		return $r;
	}
	//builder
	static function build($p){
		$f='usr/'.val($p,'fil');
		$ret=File::read($f);
		//$ret=file_get_contents($f);
		//pr(file($f));
		//$ret=tag('audio',array('src'=>$f,'controls'=>1),href($f,lang('download')));
		return $ret;
	}
	
	//interface
	static function content($p){
		$p['rid']=randid('md');
		$p['p1']=val($p,'param',val($p,'p1','Toccata.mid'));//unamed param before
		$ret=hlpbt('midi');
		$ret.=input('fil','value1',$p['p1'],'1');
		$ret.=aj('popup|midi,build||fil',lang('send'),'btn');
		return div($ret,'',$p['rid']);
	}
}
?>
