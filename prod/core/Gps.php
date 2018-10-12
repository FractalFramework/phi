<?php

class gps{
//http://adresse.data.gouv.fr/api/
static function api($p){
$lat=val($p,'lat');
$lon=val($p,'lon');
$req=val($p,'req');
$mode=val($p,'mode');
$limit=val($p,'limit',10);
$host='http://api-adresse.data.gouv.fr/';
if($mode=='search')$url=$host.'search/?q='.rawurlencode($req).'&limit='.$limit;
//elseif($mode=='postcode')$url=$url=$host.'search/?q=&postcode='.$req.'';
else $url=$host.'reverse/?lat='.$lat.'&lon='.$lon.'';
$d=files::read($url);
if($d)return json_decode($d,true);//,512,JSON_UNESCAPED_UNICODE
}

//give gps from town
//gps::search(['req'=>'address','mode'=>'search']);
static function search($p){
$req=val($p,'address');
$mode=val($p,'mode','search');//postcode
$r=self::api(['req'=>$req,'mode'=>$mode]); //pr($r);
return $r;}

static function unicode2html($d){$i=65535;
while($i>0){$hex=dechex($i); $d=str_replace("\u$hex","&#$i;",$d); $i--;}
return $d;}

static function json_utf($d){return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/',function($match){return mb_convert_encoding(pack('H*',$match[1]),'UTF-8','UCS-2BE');},$d);}

//give town from gps
//gps::com(['coords'=>'']);
static function com($p){
$coords=val($p,'coords'); if($d=sesr('apigps',$coords))return $d;
list($lat,$lon)=explode('/',$coords);
$r=self::api(['lat'=>$lat,'lon'=>$lon]);
$ret=$r['features'][0]['properties']['city'];
sesr('apigps',$coords,$ret);
return $ret;}

//interface
static function content($p){$ret='';
$type=val($p,'type');
$r=self::api($p); //pr($r);
$ret=$r['features'][0]['properties'][$type];
return $ret;}
}
?>