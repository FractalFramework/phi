<?php

class unit{
	static $private='6';
	
	static function locate($d){
		$r=sesfunc('scandir','app'); $dr=ses('dev').'/';
		if($r)foreach($r as $k=>$v){$f=$v.'/'.$d.'.php';
			if(file_exists($dr.'core/'.$f))return $dr.'core/'.$f;
			elseif(file_exists($dr.'app/'.$f))return $dr.'app/'.$f;}}
	
	static function view($app,$mth){
		$f=self::locate($app);
		$d=File::read($f);
		return nl2br(innerfunc($d,$mth));}
	
	static function build($p){
		$p1=val($p,'inp1'); $p2=val($p,'inp2'); $p3=val($p,'inp3');
		$prm=stringToAssocArray($p3,',','=');
		if(method_exists($p1,$p2))$res=($p1::$p2($prm));
		else $res=call_user_func($p1,$p3);
		$ret=div($res,'valid').br();
		$ret.=div(tag('pre','',htmlentities($res)),'alert').br();
		$ret.=div(self::view($p1,$p2),'console');
		return $ret;}
	
	//unitary tests
	static function content($p){
		$p['rid']=randid('md');
		$p['p1']=val($p,'inp1','_vue');
		$p['p2']=val($p,'inp2','test');
		$p['p3']=val($p,'inp3','p1=v1,p2=v2');
		$ret=input_label('inp1',$p['p1'],'app').br();
		$ret.=input_label('inp2',$p['p2'],'method').br();
		$ret.=textarea('inp3',$p['p3'],40,4,'','console').br();
		$ret.=aj($p['rid'].'|unit,build|headers=1,injectJs=1|inp1,inp2,inp3',lang('ok',1),'btsav');
		return $ret.br().div('','',$p['rid']);}
}
?>
