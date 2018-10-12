<?php

class exec extends appx{
static $private='6';

static function build($p){$ret='';
	$rid=val($p,'rid'); $code=val($p,'code'.$rid); $ind=val($p,'ind',date('ymd'));
	$f='disk/_/exec'.$ind.'.php'; files::write($f,'<?php '.$code);
	require $f; return $ret;}

static function read($p){$ind=val($p,'ind',date('ymd'));
	$f='disk/_/exec'.$ind.'.php'; $ret=files::read($f);
	if($ret)return substr($ret,6);}

static function edit($p){//from scene
	$rid=val($p,'rid',randid('md')); $ind=val($p,'ind',date('ymd'));
	$d=self::read($p);
	$ret=aj($rid.',,z|exec,build|rid='.$rid.',ind='.$ind.'|code'.$rid,lang('exec'),'btsav');
	$ret.=aj('input,code'.$rid.'|exec,read|ind='.$ind,$ind,'btn').br();
	$ret.=textarea('code'.$rid,$d,80,24,'','console');
	return $ret;}

static function call($p){//from appx
	$code=val($p,'exec');
	//$code=sql('exec','scene','v',val($p,'id'));
	$ret=self::build(['code'=>$code,'ind'=>val($p,'ind')]);
	return textarea('',$ret,60,12);}

static function content($p){
	$p['rid']=randid('md'); $ind=val($p,'ind',date('ymd'));
	$ret=input('ind','');
	$ret.=aj('input,code'.$p['rid'].'|exec,read||ind',lang('call',1),'btsav').br();
	$ret.=self::edit($p);
	return $ret.div('','scroll',$p['rid']);}
}
?>
