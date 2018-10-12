<?php

class poster{
static $private='0';
static $a='poster';
static $db='poster';
static $cb='stc';
static $cols=['tit','img','com'];
static $typs=['var','var','text'];
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
	$p['help']='poster_edit';
	return appx::edit($p);}

static function create($p){
	return appx::create($p);}

static function savim($p){
	$id=val($p,'id');
	$r=['9','16'];//car-width,line-height
	$font=val($p,'font','Fixedsys');
	$clr=val($p,'clr');
	$img=val($p,'img');
	$src=build::thumb($img,'medium');
	$url='img/full/'.self::$a.$id.'.png';
	self::imgtx($p['com'],$url,$src);
	return img('/'.$url.'?'.randid(),'','');}

#build
/*static function lines($t,$maxl){$n=0;
	$t=str_replace("\n"," \n",$t); $r=explode(' ',$t); $nb=0; $ret='';
	foreach($r as $k=>$v){$len=strlen($v); $nb+=$len+1; 
		$pos=strpos($v,"\n"); if(!isset($ret[$n]))$ret[$n]='';
		if($nb>$maxl){$nb=strlen($v); $n++; $nbb=floor($nb/$maxl);
			for($i=0;$i<$nbb;$i++){$ret[$n]=substr($v,$maxl*$i,$maxl); $n++;}}
		elseif($pos!==false){$ret[$n].=substr($v,0,$pos); $n++;
			$ret[$n]=substr($v,$pos+1).' '; $nb=strlen($ret[$n]);}
		else $ret[$n].=trim($v).' ';}
	return $ret;}*/

static function position($d,$sz,$w){
	list($x,$y)=explode('/',$d);
	if($x=='left')$x=10;
	if($x=='center')$x=(500/2)-($w/2);
	if($x=='right')$x=500-10-$w;
	if($y=='top')$y=10+$sz;
	if($y=='middle')$y=500/2+$sz/2;
	if($y=='bottom')$y=500-10;
	return [$x,$y];}

//hello,72,ff00ff,180/300,10
static function setxt($t,$im){
	$r=explode('|',$t);
	$font='fonts/ttf/ariblk.ttf';//LCDN//verdanab
	foreach($r as $k=>$v){
		list($txt,$sz,$clr,$pos,$ang)=vals(explode(',',$v),[0,1,2,3,4,5]);
		if(!$sz)$sz=36; if(!$ang)$ang=0;
		if($klr=clrget($clr))$clr=$klr; $width=$sz*strlen($txt)/2;
		list($rh,$gh,$bh)=rgb($clr); list($x,$y)=self::position($pos,$sz,$width);
		$c=imagecolorallocate($im,$rh,$gh,$bh);
		imagettftext($im,$sz,$ang,$x,$y,$c,$font,$txt);}
	return $im;}

static function imgtx($t,$url,$src){
	$t=str_replace("&nbsp;",' ',$t);
	$im=imagecreatefromjpeg($src);
	$blanc=imagecolorallocate($im,255,255,255);
	$im=self::setxt($t,$im);
	imagecolortransparent($im,$blanc);
	imagepng($im,$url);}

#build
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