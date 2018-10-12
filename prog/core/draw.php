<?php

class draw{

static function imgclr($im,$d,$a=''){$r=hexrgb_r($d);
if($a)return imagecolorallocatealpha($im,$r[0],$r[1],$r[2],$a);
else return imagecolorallocate($im,$r[0],$r[1],$r[2]);}

static function clr($im,$a=''){
$r=['ffffff','000000','ff0000','00ff00','0000ff','ffff00','00ffff','cccccc','999999'];
foreach($r as $k=>$v)$ret[]=self::imgclr($im,$v,$a);
return $ret;}

static function dots($r,$im,$ha,$font){$h=$ha-16; $mid=$h/2; $mx=$mid; $my=$mid; $sz=16;
list($white,$black,$red,$green,$blue,$yellow,$cyan,$silver,$gray)=imgclr_pack($im);//spe
foreach($r as $k=>$v){
	$ad=$v['ad']; $ad-=90; 
	$dc=$v['dc']; $mxb=$dc<0?$mx+$h:$mx;
	if($dc<0){$dc=abs($dc); $ad=180-$ad;}
	$ray=$mid-(($mid/90)*$dc);
	$a=deg2rad($ad); $x=$mxb+round(cos($a)*$ray,4); $y=$my+round(sin($a)*$ray,4);
	//verbose([$ray,$a,$x,$y]);
	$stt=$v['status'];
	if($stt=='amical')$clr=$green;
	elseif($stt=='inamical')$clr=imgclr($im,'ff9900');
	elseif($stt=='hostiles')$clr=$red;
	else $clr=$yellow;
	imagefilledellipse($im,$x,$y,$sz*2,$sz*2,$clr);
	imagefilledellipse($im,$x,$y,$sz,$sz,$black);
	imageellipse($im,$x,$y,$sz,$sz,$white);
	imagestring($im,$font,$x+8,$y+8,$v['star']!='-'?$v['star']:$v['name'],$white);
	if($v['dist'])imagestring($im,$font,$x+8,$y+24,$v['dist'].' Al',$silver);
	imagestring($im,$font,$x+8,$y+40,$v['planet'],$clr);}}

static function map($r,$im,$ha,$font,$hemi=1){
$h=$ha-16; $mid=$h/2; $mx=$hemi==2?$h+$mid:$mid; $my=$mid;
list($white,$black,$red,$green,$blue,$yellow,$cyan)=self::clr($im,'');
foreach($r as $k=>$v){$hb=round($h/6*$i,2);
	imageellipse($im,$mx,$my,$hb,$hb,$gray);
	$t=$i*15; $mb=$mid/6; $y=($mb*$i);
	if($t<90)imagestring($im,$font,$mx,$y,$t,$gray);}
imageellipse($im,$mx,$my,$h,$h,$white);
for($i=0;$i<24;$i++){$a=$i*15; $a=deg2rad($a); //15=360/24:
	$x=$mx+round(cos($a)*$mid,4); $y=$my+round(sin($a)*$mid,4);
	imageline($im,$mx,$my,$x,$y,$gray);
	$a=$i*15-90; $a=deg2rad($a); $t=$hemi==2?24-$i:$i; if($t==24)$t=0;
	$x=$mx+round(cos($a)*$mid,4); $y=$my+round(sin($a)*$mid,4);
	imagestring($im,$font,$x,$y,$t,$gray);}
}

//$ret=self::draw('graph',$r,800);
static function call($out,$r,$w,$h=''){
$out='disk/_/'.$f.'.png'; $h=$w?$w:$h;
$im=imagecreate($w,$h);
list($white,$black,$red,$green,$blue,$yellow,$cyan)=self::clr($im,'');
$font=imageloadfont('gdf/Fixedsys.gdf');
ImageFilledRectangle($im,0,0,$w,$h,$black);
self::map($r,$im,$h,$font);
self::dots($r,$im,$h,$font);
self::legend($r,$im,$h,$font);
imagepng($im,$out);
return img('/'.$out.'?'.randid());}


}
?>