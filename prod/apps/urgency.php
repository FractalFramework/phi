<?php

class urgency{
static $private='0';
static $a='urgency';
static $db='urgency';
static $cb='smcbk';
static $cols=['txt','sky'];
static $typs=['var','var'];
static $open=1;
static $conn=1;

function __construct(){
	$r=['a','db','cb','cols','conn'];
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
static function del($p){return appx::del($p);}
static function modif($p){return appx::modif($p);}
static function save($p){return appx::save($p);}
static function create($p){return appx::create($p);}

static function fc_sky($k,$v){$ret='';
$r=['','sunset','evening','blue','automn','red','orange','green','sea','purple','night'];
return select($k,$r,$v,1);}
static function form($p){
$p['fcsky']=1;
return appx::form($p);}

#editor
static function edit($p){return appx::edit($p);}

#reader
static function stream($p){return appx::stream($p);}
static function build($p){return appx::build($p);}

static function play($p){$c='';
	if(appx::own($p['id']))$bt=div(popup('urgency|edit=1,id='.$p['id'],ico('edit')),'right');
	$ret=self::build($p);
	if($ret['sky'])$c='skytxt sky_'.$ret['sky'];
	return div($ret['txt'],'urgency '.$c);}

#interfaces
static function tit($p){
	return appx::tit($p);}

//call (connectors)
static function call($p){
	$p['conn']=1; 
	return appx::call($p);}

//com (apps)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>