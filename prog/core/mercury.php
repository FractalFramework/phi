<?php

class mercury{
//https://mercury.postlight.com/parser?url=https://trackchanges.postlight.com/building-awesome-cms-f034344d8ed

static function getkey(){
return 'kJ0SL9ntNRqxMCEoMEHtNMA7pzT78VM8rOJ3ETdZ';}

static function post($url,$key){$d=curl_init();
curl_setopt($d,CURLOPT_URL,$url);
curl_setopt($d,CURLOPT_HTTPHEADER,['x-api-key: '.$key]);//,'Content-Type: application/json'
curl_setopt($d,CURLOPT_RETURNTRANSFER,1);
curl_setopt($d,CURLOPT_CUSTOMREQUEST,'GET');
$ret=curl_exec($d);
//$error=curl_error($d); $erno=curl_errno($d);
//if($ret==false)trigger_error(curl_error($d));//;throw new \RuntimeException($error,$errno);
curl_close($d);
return $ret;}

static function api($url){
$key=self::getkey();
if(!$url)$url='https://trackchanges.postlight.com/building-awesome-cms-f034344d8ed';
$u='https://mercury.postlight.com/parser?url='.$url;
$ret=self::post($u,$key);
//echo $enc=mb_detect_encoding($ret);
$r=json_decode($ret,true); //pr($r);
if(isset($r['code'])=='404')echo $r['content'];
//if(auth(6)){echo $u; p($r); return;}
return $r;}

#utf8
static function utf8($u){
$d=file_get_contents($u);
$enc=segment($d,'charset="','"');
if(!$enc)$enc=segment($d,'charset=','"');
if(!$enc)$enc=mb_detect_encoding($d);
return strtolower($enc);}

//com
static function com($u){
$r=self::api($u);
$enc=self::utf8($u);
$tit=$r['title'];
$img=$r['lead_image_url'];
$txt=$r['content'];
if($enc=='utf-8'){
	$tit=utf8_decode_b($tit);
	$img=utf8_decode_b($img);
	$txt=utf8_decode_b($txt);}
$txt=trans::call(['txt'=>$txt]);
return [$tit,$txt,$img];}

#call
static function read($p){
$u=val($p,'url');
list($tit,$txt,$img)=self::com($u);
$txt=conn::read(['msg'=>$txt,'ptag'=>1]);
$ret=tag('h2','',$tit);
//$ret.=image($img);
$ret.=div($txt,'');
return $ret;}

static function call($p){
list($tit,$txt,$img)=self::com($p);
$txt=conn::read(['msg'=>$txt,'ptag'=>1]);
$ret=tag('h2','',$tit);
$ret.=div($txt,'');
return $ret;}

//interface
static function content($p){
$rid=randid('yd');
$p['txt']=val($p,'url',val($p,'p'));
$ret=input('url',$p['txt'],40);
$ret.=aj($rid.'|mercury,read||url',lang('import'),'btn');
return $ret.div('','board',$rid);}
}
?>