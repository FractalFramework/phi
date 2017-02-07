<?php

class apicom{
	static $private='1';
	
	//reader
	static function read($p){
	$app=val($p,'app');
	$mth=val($p,'mth');
	$msg=val($p,'msg');
	$msg=urlencode($msg);
	$oAuth=val($p,'oAuth');
	$f='http://tlex.fr/api.php?app='.$app.'&mth='.$mth.'&msg='.$msg.'&prm=oAuth:'.$oAuth;
	$ret=File::read($f);
	//$ret=file_get_contents($f);
	return $ret;}
	
	//interface
	static function content($p){
	$p['rid']=randid('md');
	$p['p1']=val($p,'param',val($p,'p1'));
	$ret=input('oAuth','iaXFWHoX','10','oAuth').hlpbt('oAuth').br();
	$ret.=textarea('msg','',64,14,lang('message'),'console').br();
	$ret.=aj('popup|apicom,read|app=tlxcall,mth=post|oAuth,msg',langp('send'),'btn');
	return div($ret,'',$p['rid']);}
}
?>
