<?php

class pictos{
	static $private='0';

	static function injectJs(){
		return '';
	}
	static function headers(){
		Head::add('csscode','');
		Head::add('jscode',self::injectJs());
	}
	
	static function seechr(){$ret='';
		for($i=0;$i<300;$i++)$ret.=$i.':'.picto('&#'.$i.';',32).chr($i).br();
		return $ret;
	}
	
	static function see(){$ret='';
		$r=Data::read('pictos/philum');
		foreach($r as $k=>$v)$ret.=picto($k,32).' '.$k.br();
		return $ret;
	}
	
	static function com($p){$id=val($p,'id'); $ret='';
		$r=Data::read('pictos/philum');
		foreach($r as $k=>$v)$ret.=btj(picto($k,24),'insert(\'['.$k.':picto]\',\''.$id.'\'); Close(\'popup\');').' ';
		return $ret;
	}
	
	static function content($prm){
		return div(self::see(),'cols');
	}
}
?>
