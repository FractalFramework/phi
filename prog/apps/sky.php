<?php
class sky{
static $private=0;
static $a='sky';
static $db='sky';
static $cb='sk';
static $cols=['tit','css','pub'];
static $typs=['var','text','int'];
static $open=1;

function __construct(){
$r=['a','db','cb','cols'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; $r=appx::admin($p);
$r[]=['','pop','sky,see','view','watch'];
return $r;}

static function titles($p){return appx::titles($p);}
static function injectJs(){return;}
static function headers(){
add_head('csscode','
	.skyframe{display:inline-block; width:400px; height:300px; border:1px solid black;}');
add_head('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){
//$p['db2']=self::$db2;
return appx::del($p);}

static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function create($p){
//$p['pub']=0;//default privacy
return appx::create($p);}

//subform
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}
static function subedit_form($r){return appx::subedit_form($r);}

//form
//static function fc_tit($k,$v){}
static function form($p){
//$p['html']='txt';
//$p['fctit']=1;
//$p['barfunc']='barlabel';
return appx::form($p);}

static function edit($p){
//$p['collect']=self::$db2;
//$p['help']=1;
//$p['sub']=1;
//$p['execcode']=1;
return appx::edit($p);}

static function see(){$ret='';
$r=sql('tit,css','sky','kv','where uid="'.ses('uid').'" or pub>2'); //p($rb);
foreach($r as $k=>$v)$ret.=div($k,'skyframe','','background-image:'.$v.';');
return $ret;}

#build
static function build($p){
if(is_numeric($p['id']))return sql('css',self::$db,'v',$p['id']);
else return sql('css',self::$db,'v','where tit="'.$p['id'].'"');}

static function play($p){
$ret=self::build($p);
return div('','skyframe','','background-image:'.$ret.';');}

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
static function com($p){return appx::com($p);}
static function uid($id){return appx::uid($id);}
static function own($id){return appx::own($id);}

#interface
static function content($p){
//self::install();
return appx::content($p);}

static function api($p){
return appx::api($p);}
}
?>