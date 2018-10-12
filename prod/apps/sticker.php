<?php

class sticker{
static $private='0';
static $a='sticker';
static $db='sticker';
static $cb='stc';
static $cols=['txt','clr','img'];
static $typs=['var','var','var'];
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
static function headers(){
	add_head('csscode','.sticker{font-family:Lato-Black; font-size:32px; text-align:center; padding:100px 20px; background-size:cover; background-position:center center; min-height:240px;}
	.stickin{vertical-align:middle;}');
	add_head('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function modif($p){
	return appx::modif($p);}

static function form($p){
	return appx::form($p);}

static function edit($p){
	$p['help']='sticker_edit';
	return appx::edit($p);}

static function create($p){
	return appx::create($p);}

#build
static function build($p){
	return appx::build($p);}

static function play($p){
	$r=self::build($p);
	//$clr=clrget($r['clr']);
	$rc=explode(',',$r['clr']);
	$clr=val($rc,0,'ff0044'); $c1=val($rc,1,'f8f123'); $c2=val($rc,2,'06ae36'); $ag=val($rc,3,'45'); 
	$s='color:#'.$clr.'; ';
	$im=build::thumb($r['img'],'full');
	if($im)$s.='background-image:url(/'.$im.')';
	else $s.='background-image:linear-gradient('.$ag.'deg, '.rgba($c1).' 0%, '.rgba($c2).' 100%);';
	$ret=div($r['txt'],'stickin');
	return div($ret,'sticker','',$s);}

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
	self::install();
	return appx::content($p);}
}
?>