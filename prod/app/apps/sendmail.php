<?php

class sendmail{
	static $private='4';
	
	#content
	static function send($p){
		$to=val($p,'to');
		$sub=val($p,'subject','');
		$msg=val($p,'message');
		$from=val($p,'from','bot@tlex.fr');
		$state=Mail::send($to,$sub,$msg,$from,'text');
		return lang($state,1);
	return $ret;}
	
	#content
	static function content($p){
		$ret=input('to','','40',lang('to')).br();
		$ret.=input('subject','','40',lang('subject')).br();
		$ret.=textarea('message','','40','4',lang('message')).br();
		$ret.=aj('cbk|sendmail,send||subject,message,to',lang('send'),'btn');
	return div($ret,'','cbk');}
}
?>
