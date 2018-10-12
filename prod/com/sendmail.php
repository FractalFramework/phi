<?php
class sendmail{
static $private='2';

static function tlex($id){
$msg=sql('txt',tlex::$db,'v',$id);
$ret=conn::read(['msg'=>$msg,'app'=>'conn','mth'=>'reader','ptag'=>1,'opt'=>'no']);
$ret.="\n".host().'/'.$id;
return $ret;}

#content
static function send($p){
$to=val($p,'to');
$sub=utf8_encode(val($p,'subject',''));
$msg=utf8_encode(val($p,'message'));
$from=val($p,'from','bot@tlex.fr');
$mode=val($p,'text');//html
if($id=val($p,'tlex'))$msg=self::tlex($id);
$state=mail::send($to,$sub,$msg,$from,$mode);
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
