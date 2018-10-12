<?php

class yandex{
//https://tech.yandex.com/translate/doc/dg/reference/translate-docpage/static $private='1';

/*
$r=['CN'=>'Chine','IN'=>'Inde','US'=>'États-Unis','ID'=>'Indonésie','BR'=>'Brésil','RU'=>'Russie','JP'=>'Japon','MX'=>'Mexique','DE'=>'Allemagne','TR'=>'Turquie','FR'=>'France','GB'=>'Royaume-Uni','IT'=>'Italie','ZA'=>'Afrique du Sud','ES'=>'Espagne','AR'=>'Argentine','CA'=>'Canada','SA'=>'Arabie saoudite','KR'=>'Corée du Nord','AU'=>'Australie','BN'=>'Bengali','PA'=>'Pakistan'];

$r=['cn'=>'zh','in'=>'mr','us'=>'en','id'=>'id','br'=>'pt','ru'=>'ru','jp'=>'ja','es'=>'es','de'=>'de','tr'=>'tr','fr'=>'fr','gb'=>'en','it'=>'it','za'=>'sw','es'=>'es','ar'=>'es','ca'=>'fe','sa'=>'ar','kr'=>'ko','au'=>'en','bn'=>'bn','pa'=>'pa'];

$r=['us'=>'en','cn'=>'zh','es'=>'es','sa'=>'ar','in'=>'mr','fr'=>'fr','ru'=>'ru','bn'=>'bn','pt'=>'pt','id'=>'id','pa'=>'pa','de'=>'de','jp'=>'ja','tr'=>'tr','it'=>'it','za'=>'sw','kr'=>'ko'];

*/

static function getkey(){$k=ses('yndxkey');
if(!$k)ses('yndxkey',files::read('cnfg/yandex.txt'));
//$k='trnsl.1.1.20170206T173119Z.092e1dd0a9954253.db344b1e497240fb68fd4b1f5150a3d25d9c4e95';
//$k='trnsl.1.1.20180424T150654Z.ad62660ecf66eace.b9aae90ac4dc2fb31c0391fe393f2b84e6a14208';
//$k=ses('yndxkey',$k);
return $k;}

static function api($vr,$mode){
$vr['key']=self::getkey();
if(!$mode)$mode='translate';//detect//getlangs
$u='https://translate.yandex.net/api/v1.5/tr.json/'.$mode.'?'.mkprm($vr);
if($vr)$d=@file_get_contents($u);
$r=json_decode($d,true);
//$r=json_dec($d);
return $r;}

static function getlangs(){$rb=[];
$r=self::api('','getlangs');
foreach($r['dirs'] as $v)$rb=merge($rb,explode('-',$v));
return implode(',',$rb);}

static function detect($p){
$txt=rawurlencode(html_entity_decode(val($p,'txt')));
$r=self::api(['text'=>$txt],'detect');
return $r['lang'];}

#reader
static function build($p){$id=val($p,'id'); $ret='';
$txt=val($p,'txt','');
$from=val($p,'from','');//use comma as separator
$to=val($p,'to',ses('lng'));//default lang
$format=val($p,'format','plain');//plain//html
$options=val($p,'option','1');//1 for autodetect (empty) from
if($from)$lang=$from.'-'.$to; else $lang=$to;
$vr=['text'=>rawurlencode(html_entity_decode($txt)),'lang'=>$lang,'format'=>$format,'options'=>$options]; //echo $vr['text'];
$r=self::api($vr,'translate');
return $r;}

static function read($p){
$r=self::build($p);
$detected_lang=$r['detected']['lang'];
$text=$r['text'][0];
$text=($text);//decode
$ret=div(lang('detected_lang').' '.$detected_lang,'grey').div($text,'pane');
return $ret;}

//com (apps)
static function com($p,$o=''){
$r=self::build($p);
$ret=$r['text'][0];
$_POST['lng']=$r['detected']['lang'];
$ret=rawurldecode($ret);//if($o)
if(val($p,'dtc'))$ret.=' ('.$_POST['lng'].')';
return $ret;}

//interface
static function content($p){
$rid=randid('yd');
$p['txt']=val($p,'txt',val($p,'p'));
$ret=input('txt',$p['txt']);
$ret.=aj($rid.'|yandex,read||txt',lang('translate'),'btn');
//$ret.=aj('popup|yandex,getlangs||txt',lang('lang'),'btn');
$ret.=aj('popup|yandex,detect||txt',lang('detect'),'btn');
return $ret.div('','board',$rid);}
}
?>