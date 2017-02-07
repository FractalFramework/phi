<?php

class _svg{
	static $private='2';

static function ex(){return '
[rand,black,1:attr]
[10,10,30,20:rect]
[300,220,200:circle]
[100,100,40,80:line]
[100,100,40,80:ellipse]
[200/10-250/190-160/210:polygon]
[20/20-40/20-60/60-20/40-20/20:polyline]
[,,popup|Svg,j*[20,20*hello:text]:lj]
[rand,red,2:attr][M150 0 L75 200 L225 200 Z:path]
[purple,,,,,,rotate(330-40/20):attr][10,20*hello:text]
[blue:attr][280,140,http://philum.net*[80,20,,1*hello:text]:a]
[20,20*[20,20*hello1:tspan][green:attr][20,40*hello2:tspan]:text]
[*[f1,0,0*[SourceGraphic,15:feGaussianBlur]:filter]:defs][rand,,,,0.4:attr][300,200,200,1:circle]

[*[grad1,0%,0%,0%,100%*[0%,rand:stop][100%,rand:stop]:linearGradient]:defs][,,,,,,,grad1:attr][0,0,600,400:rect]
[*[grad2,0%,0%,0%,100%*[0%,red,0:stop][100%,yellow:stop]:linearGradient]:defs][,,,,,,,grad2:attr][300,120,100:circle]
[*[grad3,0%,0%,0%,100%*[0%,rand,0:stop][100%,rand:stop]:linearGradient]:defs][,,,,,,,grad3:attr][0,200,600,200:rect]
[*[f1,0,0*[SourceGraphic,15:feGaussianBlur]:filter]:defs][rand,,,,0.4:attr][300,200,200,1:circle]

[*[f1*
[SourceGraphic,offOut,10,10:feOffset]
[offOut,matrixOut,matrix,0.2-0-0-0-0-0-0-2-0-0-0-0-0-0-2-0-0-0-0-0-1-0:feColorMatrix]
[matrixOut,10,blurOut:feGaussianBlur]
[SourceGraphic,blurOut,normal:feBlend]
:filter]:defs]
[rand,,,,0.4:attr][300,120,100,f1:circle]
';}

static function content($prm){$rid=randid('plg');
$code=val($prm,'code'); $size=val($prm,'size'); 
if(!$code)$code=self::ex();
$prm=array('code'=>$code,'size'=>$size);
$bt=textarea('code',$code,74,10).' ';
$bt.=Ajax::j('div,'.$rid.',2|Svg,j||code',lang('ok'),'btn');
$ret=Svg::j($prm);
//echo textarea('',$ret,60,10);
return $bt.div($ret,'',$rid);}
	
}