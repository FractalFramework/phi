<?php

class Svg{
	
static function spe(){
$path_type=array('M'=>'moveto','L'=>'lineto','H'=>'horizontal lineto','V'=>'vertical lineto','C'=>'curveto','S'=>'smooth curveto','Q'=>'quadratic Bézier curve','T'=>'smooth quadratic Bézier curveto','A'=>'elliptical Arc','Z'=>'closepath');
$filters=array('feBlend','feColorMatrix','feComponentTransfer','feComposite','feConvolveMatrix','feDiffuseLighting','feDisplacementMap','feFlood','feGaussianBlur','feImage','feMerge','feMorphology','feOffset','feSpecularLighting','feTile','feTurbulence','feDistantLight','fePointLight','feSpotLight');}

static function motor(){return array(
'attr'=>array('fill','stroke','stroke-width','size','fill-opacity','stroke-dasharray','transform','fillurl'),//transform:rotate(30 20,40)//dash:5,5
'circle'=>array('cx','cy','r','filter'),
'rect'=>array('x','y','width','height','filter','id'),
'ellipse'=>array('cx','cy','rx','ry','filter'),
'line'=>array('x1','y1','x2','y2','filter'),
'polygon'=>array('points'),//200,10 250,190 160,210
'polyline'=>array('points'),//20,20 40,25 60,40 80,120 120,140 200,180
'path'=>array('d'),//M150 0 L75 200 L225 200 Z
'text'=>array('x','y','filter'),
'tspan'=>array('x','y'),
'a'=>array('x','y','xlink:href','onclick','target'),
'lj'=>array('x','y','onclick'),
'filter'=>array('id','x','y'),//,'filter','value'
'feOffset'=>array('in','result','dx','dy'),
'feColorMatrix'=>array('in','result','type','values'),
'feGaussianBlur'=>array('in','stdDeviation','result'),
'feBlend'=>array('in','in2','mode'),
'linearGradient'=>array('id','x1','y1','x2','y2'),
'stop'=>array('offset','style','opac'),
'g'=>array('id'),
);}

static function clr($d=''){$r=Clr::get();
$rb=array_keys($r); if($d=='rand')$d=rand(0,count($rb)-1);
return is_numeric($d)?$rb[$d]:$d;}

static function build_prop($d){return str_replace(array('/','-'),array(',',' '),$d);}

static function conn($d){$ra=self::motor();
list($p,$v,$b)=readconn($d); $pr='';
if($b=='svg')return $p;
$rb=explode(',',$p); if(isset($ra[$b]))$pr=combine($ra[$b],$rb);
if($b=='attr')ses('attr',$pr); 
elseif(ses('attr')){$pr=merge($pr,ses('attr'));}//$_SESSION['attr']='';
if(isset($pr['points']))$pr['points']=self::build_prop($pr['points']);
if(isset($pr['transform']))$pr['transform']=self::build_prop($pr['transform']);
if(isset($pr['fill']))$pr['fill']=self::clr($pr['fill']);
if(isset($pr['stroke']))$pr['stroke']=self::clr($pr['stroke']);
//if(isset($pr['onclick']) && $b=='lj'){//pr($pr);
//	$pr['onclick']=Ajax::js($pr['onclick']); $b='a';}
if(@$pr['fillurl']){$pr['fill']='url(#'.$pr['fillurl'].')';$pr['fillurl']='';}
if(@$pr['filter'])$pr['filter']='url(#'.$pr['filter'].')';
if($b=='feColorMatrix')$pr['values']=self::build_prop($pr['values']);
if($b=='stop')$pr['style']='stop-color:'.self::clr($pr['style']).'; stop-opacity:'.$pr['opac'].';';
if($b!='attr')return tag($b,$pr,$v);}

static function j($prm){
$p=val($prm,'code'); $size=val($prm,'size');
$p=deln($p); $p=delsp($p);
$ret=Conn::read($p,'Svg','conn');
if(!$size)$size='600/440';
list($w,$h)=explode('/',$size);
$atr=array('version'=>'1.1','width'=>$w,'height'=>$h);
return tag('svg',$atr,$ret);}

static function content($prm){
$prm['code']=_svg::ex(); $prm['size']='600/440';
$prm['app']='Svg'; $prm['mth']='conn';
return self::j($prm);}

}

?>