<?php

class agenda{
static $private='0';
static $a='agenda';
static $db='agenda';
static $cb='agn';
static $cols=['tit','event','pub'];
static $typs=['var','var','int'];//var,text,int
static $conn=0;//0,1(ptag),2(brut),no(br), using 'txt'

function __construct(){
	$r=['a','db','cb','cols','conn'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	$r=array_combine(self::$cols,self::$typs);
	appx::install($r);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	add_head('csscode','');
	add_head('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){return appx::del($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function form($p){return appx::form($p);}
static function create($p){return appx::create($p);}
static function edit($p){return appx::edit($p);}

#build
static function build($p){return appx::build($p);}

static function calendar($date){
$gd=getdate($date); $dcible=date('d',ses('time')); $dyam=$gd["mon"];
$ret[]=['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
$tim=mktime(1,1,1,$dyam,1,$gd['year']);
$first=date('w',$tim); if($first==0)$first=7; $nbdy=date('t',$tim);
for($a=1;$a<$first;$a++)$ret[1][]='';
for($i=1;$i<=$nbdy;$i++){$mk=mktime(0,0,0,$dyam,$i,$gd['year']);
	$dy=date('d',$mk); if($dy==$dcible)$c='active'; else $c='';
	//$mk+86400
	$ret[$i][]='1'; 
	$a++; if($a==8){$a=1; $ret[$i][]='';}}
return $ret;}

#play
static function template(){
	//return appx::template();
	return '[[(tit)*class=tit:div][(txt)*class=txt:div]*class=paneb:div]';}

static function play($p){
	//return appx::play($p);
	$r=self::build($p);
}

static function stream($p){
	//$p['t']=self::$cols[0];
	return appx::stream($p);}

#call (read)
static function tit($p){
	//$p['t']=self::$cols[0];
	return appx::tit($p);}

static function call($p){
	return div(self::play($p),'',self::$cb.$p['id']);}

#com (edit)
static function com($p){return appx::com($p);}

#interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>