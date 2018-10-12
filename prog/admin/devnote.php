<?php

class devnote{
static $private='0';
static $a='devnote';
static $db='devnote';
static $cb='dvn';
static $cols=['tit','txt'];
static $typs=['var','text'];

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']='0';
	return appx::admin($p);}

static function injectJs(){return '';}
	
static function headers(){
	add_head('csscode','.txt textarea{width:100%;}');
	add_head('jscode',self::injectJs());}

#operations
static function del($p){
	return appx::del($p);}
	
static function modif($p){
	return appx::modif($p);}

static function save($p){
	return appx::save($p);}

static function create($p){
	return appx::create($p);}

#editor
static function form($p){
	return appx::form($p);}

static function edit($p){
	return appx::edit($p);}

#reader
static function build($p){
	return appx::build($p);}

static function play($p){
	//$r=self::build($p);
	return appx::play($p);}

static function stream($p){$ret='';
	$dsp=ses('devnotedsp',val($p,'display')); $uid=ses('uid');
	$r=sql('id,uid,tit,dateup',self::$db,'rr','order by id desc limit 100');
	if($r)foreach($r as $k=>$v){
		$tit=$v['tit']?$v['tit']:$v['id']; 
		$btn=ico('file-o').$tit.' '.span($v['date'],'date');
		$c=$dsp==1?'bicon':'licon';
		if($v['uid']==$uid)$app='edit'; else $app='call';
		$ret.=aj(self::$cb.'|devnote,'.$app.'|id='.$v['id'],$btn,$c);}
	return div($ret,'');}

/*static function stream0($p){
	return appx::stream($p);}*/

#interfaces
static function tit($p){
	$p['t']='tit';
	return appx::tit($p);}

static function template(){
	return appx::template();}

//call
static function call($p){
	$p['conn']=0;
	return appx::call($p);}
	
//com (apps)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>