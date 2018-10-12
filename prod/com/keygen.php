<?php

class keygen{

static function build($p=''){
$l=val($p,'length',10); $o=val($p,'cmpx');
$a='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMONPQRSTUVWXYZ0123456789';
if($o)$a.='$%*,?;.:/!#{[-|_)]=}';
$r=str_split($a); $n=count($r)-1; $ret='';
for($i=0;$i<$l;$i++)$ret.=$r[rand(0,$n)];
return $ret;}

static function content($p){
//$p1=val($p,'p1');
$ret=input('length','10','');
$ret.=checkbox('cmpx',['complex'=>'more complex']);
$ret.=aj('gnpw|keygen,build||length,cmpx',lang('ok',1),'btsav').' ';
$ret.=hlpbt('keygen');
return div($ret.div('','','gnpw'),'board');}
}
?>