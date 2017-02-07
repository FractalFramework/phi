<?php

class genpswd{

	static function build($prm){
		$p=val($prm,'inp1',8); $o=val($prm,'inp2',8); 
		$a='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMONPQRSTUVWXYZ0123456789';
		if($o==1)$a.='$£%*µ,?;.:/!&#{[-|_)]=} ';
		$r=str_split($a); $n=count($r)-1; $ret='';
		for($i=0;$i<$p;$i++)$ret.=$r[rand(0,$n)];
	return $ret;}
	
	static function content($prm){
		//$p1=val($prm,'p1');
		$ret=input('inp1','10','');
		$ret.=checkbox('complex',array('inp2'=>'more complex'));
		$ret.=Ajax('gnpw','genpswd,build','','inp1,inp2',lang('ok'),'btn').' ';
		$ret.=hlpbt('genpswd');
		return $ret.div('','deco','gnpw');
	}
}
?>