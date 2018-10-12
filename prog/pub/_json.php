<?php
class _json{
static function save($p){
	$root=val($p,'root');
	$inp1=val($p,'inp1');
	if($inp1)json::add($root,$inp1);
	return self::read($root);}

static function add($root){
	$ret=input('inp1','');
	$ret.=aj('dataTable|_db,save|root='.$root.'|inp1','Add','btn');
	return $ret;}

static function read($root){
	$datas=json::read($root);
	return mktable($datas);}

static function init($root){
	if(!is_file(json::file($root))){
		$datas=array(1=>'one',2=>'two',3=>'three');
		json::write($root,$datas);}}

static function content(){
	$root='one/two'; //json::del($root);
	self::init($root);
	$datas=json::read($root); //p($datas);
	$ret=tag('div','',self::add($root));
	$ret.=tag('div','id=dataTable',mktable($datas));
	return $ret;}
}