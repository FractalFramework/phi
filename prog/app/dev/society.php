<?php

class society{
	static $private='4';

	static function injectJs(){return '';}
	static function headers(){
		Head::add('csscode','
		#scroll{width:100%; height:600px; overflow-y:scroll; overflow-x:scroll;}
		#ground{width:1000px; height:1000px;}
		.square{width:100px; height:100px; background:black; position:absolute;}');
		Head::add('jscode',self::injectJs());}
	
	static function admin(){
		$r[]=array('','j','popup|society,go','',lang('goto'));
		return $r;}
	
	static function install(){
		//red/day, blue/month, green/year
		//biens, heures, ressources en jetons
		Sql::create('soc_head',array('red'=>'int','blue'=>'int','green'=>'int'));
		Sql::create('soc_prod_head',array('red'=>'int','blue'=>'int','green'=>'int'));
		Sql::create('soc_prod',array('pid'=>'int','path'=>'int','sold'=>'int','bin'=>'int'));
		Sql::create('soc_man',array('uid'=>'int','red'=>'int','blue'=>'int','green'=>'int'));
		Sql::create('soc_man_priv',array('uid'=>'int','red'=>'int','blue'=>'int','green'=>'int'));
		Sql::create('soc_share',array('type'=>'int','prop'=>'var','cost'=>'var','allocate'=>'int'));
	}
	
	static function go(){$ret=''; $n=ses('n');
		for($i=0;$i<$n;$i++)$ret.=btj('square '.$i,'scrolltopos(\'sq'.$i.'\')','').br();
		return $ret;}
	
	static function square(){$ret=''; $n=ses('n');
		for($i=1;$i<$n;$i++){$x=rand(0,1000); $y=rand(0,1000);
			//$ret.=div('','square','sq'.$i,'left:'.$x.'px; top:'.$y.'px; ');
			$ret.='['.$x.','.$y.',10,10,,sq'.$i.':rect]['.$x.','.$y.'*'.$i.':text] ';
			}
		return $ret;}
	
	static function content($prm){
		ses('n',10);
		//self::install();
		//$p1=val($prm,'p1');
		//$ret=input('inp1','value1','','1');
		//$ret.=Ajax::j('popup|society,result|msg=text|inp1',lang('send'),'btn');
		$pr['code']='[rand,black,1:attr]';
		$pr['code'].=self::square();
		$pr['size']='100%/100%';
		$ret=Svg::j($pr);
		$ret=div($ret,'','ground');
		//$ret=div($ret,'','scroll');
		return $ret;
	}
}
?>
