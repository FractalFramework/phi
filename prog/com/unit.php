<?php

class unit{
static $private='6';

static function admin($p){
	if(auth(6))$r[]=['tools','pop','exec','','exec'];
return $r;}

static function locate($d){$dr=ses('dev');
	$r=sesfunc('scandir',$dr);
	if($r)foreach($r as $k=>$v){$f=$d.'.php';
		if(file_exists($dr.'/'.$v.'/'.$f))return $dr.'/'.$v.'/'.$f;
		elseif(file_exists($dr.'/'.$f))return $dr.'/'.$f;}}

static function view($app,$mth){
	$f=self::locate($app);
	$d=files::read($f);
	return nl2br(innerfunc($d,$mth));}

static function build($p){$res='';
	$p1=val($p,'inp1'); $p2=val($p,'inp2'); $p3=val($p,'inp3');
	$prm=atr($p3);
	if(val($prm,'p1')=='call')$res=$p1::$p2($prm['p2']);
	elseif(method_exists($p1,$p2))$res=$p1::$p2($prm);
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
	$ret=input_label('inp1',$p['p1'],'app');
	$ret.=input_label('inp2',$p['p2'],'method');
	$ret.=textarea('inp3',$p['p3'],40,4,'','console').br();
	$ret.=aj($p['rid'].'|unit,build|headers=1,injectJs=1|inp1,inp2,inp3',lang('ok',1),'btsav');
	return $ret.br().div('','',$p['rid']);}
}
?>
