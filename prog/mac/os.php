<?php
class os{

static function com0(){
$keys='id,dir,type,com,picto,bt,auth'; 
$w='where uid="'.ses('uid').'"'; $w.=' or auth<='.ses('auth').'';
return sql($keys,desktop::$db,'id',$w.' order by dir');}

static function com(){
$r=['tlex','desktop','explorer','art','tabler','book','genetics','sticker','chat','poll','vote','ideas','forms','address','doodle','pad'];
//foreach($r as $k=>$v)$ret[]=['','',$v,'',$v];//.',call'
foreach($r as $k=>$v){
	if(method_exists($v,'com'))$call=$v.',com'; else $call=$v;
	$ret[]=aj('oswrp|'.$call.'|headers=1',pic($v,48).span($v),'bicon');}
return implode('',$ret);}

static function content($p){
return self::com().div('','','oswrp');
return div(desk::load('os','com',val($p,'dir')),'','oswrp');}	
}
?>