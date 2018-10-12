<?php

class wikipedia{
static $private='1';
static $a='wikipedia';
static $db='wiki';
static $cb='wkp';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];//var,text,int
static $config=['t'=>'','conn'=>0,'ptag'=>0,'views'=>1];

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

/*specific cols:
- first col is actually used for title ['t']
- txt (var) will accept connectors ['conn']
- last col as "pub" will assume privacy*/
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
static function form($p){return appx::form($p);}
static function del($p){return appx::del($p);}
static function save($p){return appx::save($p);}
static function create($p){return appx::create($p);}
static function modif($p){return appx::modif($p);}
static function edit($p){return appx::edit($p);}

#build
static function build($p){return appx::build($p);}

#play
static function template(){
	return appx::template();}

static function play($p){
	//$r=self::build($p);
	$p['conn']=0;//0,1(ptag),2(brut),no(br)
	return appx::play($p);}

static function stream($p){
	$p['t']=self::$cols[0];
	return appx::stream($p);}

#call (read)
static function tit($p){
	$p['t']=self::$cols[0];//used is title
	return appx::tit($p);}

static function call($p){
	return div(self::play($p),'',self::$cb.$p['id']);}

#com (edit)
static function com($p){return appx::com($p);}

#interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>