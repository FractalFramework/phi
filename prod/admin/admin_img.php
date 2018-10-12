<?php

class admin_img{
static $private='0';
static $a='admin_img';
static $db='admin_img';
static $cb='admim';

static function install(){
	sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}

static function admin(){
	$r[]=['','j','popup|'.self::$a.',content','plus',lang('open')];
	$r[]=['','j',self::$cb.'|'.self::$a.',stream|display=2','list','-'];
	$r[]=['','j',self::$cb.'|'.self::$a.',stream|display=1','th-large','-'];
	$r[]=['','pop','core,help|ref='.self::$a.'_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f='.self::$a,'code','Code'];
	return $r;}

static function injectJs(){
	return '';}

static function headers(){
	add_head('csscode','');
	add_head('jscode',self::injectJs());}

static function titles($p){
	$d=val($p,'appMethod');
	$r['content']='welcome';
	$r['build']='model';
	if(isset($r[$d]))return lang($r[$d]);}

#build
static function build($p){$ret='';
	$r=dir_scan('img/mini');
	$dsp=ses(self::$a.'dsp',val($p,'display'));
	if($r)foreach($r as $k=>$v){
		$im=img('/img/mini/'.$v);
		$ret.=imgup('img/full/'.$v,$im.span($v),'bicon');}
	return $ret;}

static function call($p){
	return div(self::build($p),'',self::$cb);}

static function com(){
	return self::content($p);}

#content
static function content($p){
	//self::install();
	$p['p1']=val($p,'param',val($p,'p1'));
	$ret=self::build($p);
return div($ret,'pane');}

}
?>