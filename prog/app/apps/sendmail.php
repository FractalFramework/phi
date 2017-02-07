<?php
class sendmail{
	static $private='2';
	
	#content
	static function send($p){
		$to=val($p,'to');
		$sub=val($p,'subject','');
		$msg=val($p,'message');
		$from=val($p,'from','bot@tlex.fr');
		$mode=val($p,'text');//html
		$state=Mail::send($to,$sub,$msg,$from,$mode);
		return span(lang($state,1),'valid');
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
