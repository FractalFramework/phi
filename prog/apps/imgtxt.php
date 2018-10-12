<?php

class imgtxt{
static $private='0';
static $a='imgtxt';
static $db='imgtxt';
static $cb='imt';
static $cols=['txt'];
static $typs=['var'];
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function modif($p){
	self::savim($p);
	return appx::modif($p);}

static function form($p){
	return appx::form($p);}

static function edit($p){
	return appx::edit($p);}

static function create($p){
	return appx::create($p);}

static function savim($p){
	$id=val($p,'id');
	$r=['9','16'];//car-width,line-height
	$font=val($p,'font','Fixedsys');
	$clr=val($p,'clr');
	$url='img/full/'.self::$a.$id.'.png';
	$txt=utf8_decode($p['txt']);
	self::imgtx($txt,$r[0],$r[1],$font,$clr,$url);
	return img('/'.$url.'?'.randid(),'','');}

#build
static function lines($t,$maxl){$n=0;
	$t=str_replace("\n"," ",$t); $r=explode(' ',$t); $nb=0; $ret='';
	foreach($r as $k=>$v){$len=strlen($v); $nb+=$len+1; $pos=strpos($v,"\n");
		if(!isset($ret[$n]))$ret[$n]=''; //else $nb+=strlen($ret[$n]);
		if($nb>$maxl){$nb=strlen($v); $n++; $nbb=floor($nb/$maxl);
			for($i=0;$i<=$nbb;$i++){$ret[$n]=substr($v,$maxl*$i,$maxl); $n++;}}
		elseif($pos!==false){$ret[$n].=substr($v,0,$pos); $n++;
			$ret[$n]=substr($v,$pos+1).' '; $nb=strlen($ret[$n]);}
		else $ret[$n].=trim($v).' ';}
	return $ret;}

/*function imt_mk($p,$n){ 
$l=50; $s=strlen($p); $n=ceil($s/$l); $sz=$s<$l?($s/5)*58:500;
for($i=0;$i<$n;$i++){$v=substr($p,$i*$l,$l); $pos=strpos($v,"\n");
	if($pos!==false){$r[]=substr($v,0,$pos); $r[]=substr($p,$pos+1);}
	else $r[]=$v;}
return $r;}*/

static function imgtx($t,$lac,$hac,$fnt,$clr,$url){
	$t=str_replace("&nbsp;",' ',$t); $r=''; $fx=''; $fy='';
	$nb_chars=strlen($t); $width=500;
	if($lac && $width)$maxl=floor($width/$lac); else $maxl=50;
	$la=$nb_chars*$lac; 
	$la=$la>$width-8?$width-8:$la;
	if($nb_chars>$maxl or strpos($t,"\n")!==false)$r=self::lines($t,$maxl);
	//$r=self::imt_mk($t); p($r);
	$ha=$r?$hac*count($r):($hac?$hac:20); $clr=$clr?$clr:'000000';
	//$rh=hexdec(substr($clr,0,2));$gh=hexdec(substr($clr,2,2));$bh=hexdec(substr($clr,4,2));
	list($rh,$gh,$bh)=rgb($clr);
	$im=imagecreate($la,$ha);
	$blanc=imagecolorallocate($im,255,255,255);
	$color=imagecolorallocate($im,$rh,$gh,$bh);
	$font=imageloadfont('fonts/gdf/'.$fnt.'.gdf');
	if($r)foreach($r as $k=>$v){$fx=$k*$lac; $fy=$k*$hac;
		imagestring($im,$font,1,$fy,$v,$color);}
	else imagestring($im,$font,$fx?$fx:0,$fy?$fy:0,$t,$color);
	imagecolortransparent($im,$blanc);
	imagepng($im,$url);}

static function build($p){$id=val($p,'id');
	return sql('uid,txt',self::$db,'ra',$id);}

static function play($p){
	$id=val($p,'id');
	$url='img/full/'.self::$a.$id.'.png';
	return img('/'.$url.'?'.randid(),'','');}

static function stream($p){
	return appx::stream($p);}

#call (read)
static function tit($p){
	return appx::tit($p);}

static function call($p){
	return appx::call($p);}

#com (edit)
static function com($p){
	return appx::com($p);}

#interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>