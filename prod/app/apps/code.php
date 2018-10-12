<?php

class code{
static $private='0';
static $a='code';
static $db='code';
static $cb='cod';
static $cols=['tit','code'];
static $typs=['var','text'];
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
	return appx::modif($p);}

static function form($p){
	return appx::form($p);}

static function edit($p){
	$p['help']='code_edit';
	return appx::edit($p);}

static function create($p){
	return appx::create($p);}

#build
static function build($p){
	return appx::build($p);}

static function play($p){
	$r=self::build($p);
	$ret=build::code($r['code'],'');
	return $ret;}

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