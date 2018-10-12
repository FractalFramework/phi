<?php

class tickets{
static $private='1';
static $a='tickets';
static $db='tickets';
static $cb='tccbk';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];
static $conn=0;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	$r=['tit'=>'var','txt'=>'var','pub'=>'int'];
	appx::install($r);}
	
static function injectJs(){return '';}

static function headers(){
	add_head('csscode','.txt textarea{width:100%;}');
	add_head('jscode',self::injectJs());}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

#sys
static function del($p){
	return appx::del($p);}
	
static function modif($p){
	return appx::modif($p);}

static function save($p){
	return appx::save($p);}

static function create($p){
	return appx::create($p);}

//static function form0($p){return appx::form($p);}
static function form($p){
	$ret=hidden('tit',ses('user'));
	$ret.=div(textarea('txt',val($p,'txt'),'70',7,lang('message')));
	$ret.=hidden('pub',4);
	return $ret;}

#editor
static function edit($p){
	return appx::edit($p);}

#reader
static function build($p){
	return appx::build($p);}

static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	return appx::tit($p);}

static function template(){
	return appx::template();}

//call
static function call($p){
	return appx::call($p);}

//com
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>