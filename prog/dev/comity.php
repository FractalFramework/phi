<?php
class comity{
static $private='0';
static $a='comity';
static $db='comity';
static $cb='mdl';
static $cols=['tit','txt','cluster','pub'];
static $typs=['var','var','int','int'];
static $conn=0;
static $db2='comity_evals';
static $open=1;

function __construct(){
	$r=['a','db','cb','cols','db2','conn'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));
	sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','decision'=>'var','accept'=>'int'],1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	add_head('csscode','');
	add_head('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	//$p['db2']=self::$db2;
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function modif($p){
	return appx::modif($p);}

//static function fc_tit($k,$v){}
static function form($p){
	//$p['html']='txt';
	//$p['fctit']=1;
	//$p['barfunc']='barlabel';
	return appx::form($p);}

static function edit($p){
	//$p['collect']=self::$db2;
	//$p['help']='comity_edit';
	//$p['sub']=1;
	return appx::edit($p);}

static function create($p){
	//$p['pub']=0;//default privacy
	return appx::create($p);}

#build
static function build($p){
	return appx::build($p);}

static function template(){
	//return appx::template();
	return '[[(tit)*class=tit:div][(txt)*class=txt:div]*class=paneb:div]';}

static function play($p){
	//$r=self::build($p);
	return appx::play($p);}

static function stream($p){
	//$p['t']=self::$cols[0];
	return appx::stream($p);}

#call (read)
static function tit($p){
	//$p['t']=self::$cols[0];
	return appx::tit($p);}

static function call($p){
	return appx::call($p);}

#com (edit)
static function com($p){
	return appx::com($p);}

#interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>