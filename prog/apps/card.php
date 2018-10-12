<?php

class card{
static $private='0';
static $a='card';
static $db='card';
static $cb='cdcbk';
static $cols=['person','corporation','status','address','mail','site','phone','pub'];
static $typs=['var','var','var','var','var','var','var','int'];
static $conn=1;
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function injectJs(){return '';}

static function headers(){
	add_head('csscode','');
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

static function form($p){
	return appx::form($p);}

#editor	
static function edit($p){
	return appx::edit($p);}
	
#reader
static function build($p){$id=val($p,'id');
	$colstr=implode(',',self::$cols);
	$r=sql($colstr,self::$db,'ra',$id);
	return $r;}

static function stream($p){
	$p['t']='person';
	return appx::stream($p);}
	
#interfaces
static function tit($p){
	$p['t']='person';
	return appx::tit($p);}

//template
static function template(){
	return '[(card)
	[[(person):b]*class=cstitle:div]
	[[(corporation)*class=csfunction:span] - [(status)*class=csname:span]:div]
	[(address)*class=cssite:div]
	[(mail)*class=csinfos:div]
	[(phone)*class=csinfos:div]
	[[(site)*(url):a]*class=cssite:div]
*class=paneb cscard:div]';}
	
//call
static function call($p){
	$r=self::build($p);
	$r['card']=ico('vcard-o',32);
	$r['url']=http($r['site']);
	$r['site']=nohttp($r['site']);
	$template=self::template();
	$ret=vue::read($r,$template);
	$ret=conn::read(['msg'=>$ret,'ptag'=>0]);
	return $ret;}
	
//com (apps)
static function com($p){
	return appx::com($p);}
	
//interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>