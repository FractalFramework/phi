<?php
class scene{
static $private=0;
static $a='scene';
static $db='scene';
static $cb='mdl';
static $cols=['tit','code','codb','pub'];
static $typs=['var','text','text','int'];
static $open=0;
static $qb='db';

function __construct(){
$r=['a','db','cb','cols','qb'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
//sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','val'=>'var'],1);
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; return appx::admin($p);}
static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
//html,body{overflow:hidden; width:100%; height:100%; padding:0; margin:0;}
//.canvas{width:100%; height:100%; touch-action:none;}
//add_head('jslink','/js/babylon.js');//https://cdn.babylonjs.com/
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

static function create($p){
//$p['pub']=0;//default privacy
return appx::create($p);}

//subform
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}
static function subedit_form($r){
	$ret=hidden('bid',$r['bid']);
	//$ret.=div(input('chapter',$r['chapter'],63,lang('chapter'),'',512));
	return $ret;}

//static function fc_tit($k,$v){}
static function form($p){$a=self::$a;
$p['execcodb']=1;
return appx::form($p);}

static function edit($p){
//localStorage['m4']=res;
//$p['collect']=self::$db2;
$p['btcode']=btj(lang('cleanup',1),'repaircode(\'code\')','btn','');
$p['help']=1;
//$p['sub']=1;
return appx::edit($p);}

#build
static function build($p){
return appx::build($p);}

static function play($p){
$r=self::build($p); $rid=randid('scn');
add_head('csscode','#'.$rid.'{width:100%; height:100%; touch-action:none;}');
//add_head('jslink','https://preview.babylonjs.com/inspector/babylon.inspector.bundle.js');
//add_head('jslink','https://www.babylonjs.com/hand.minified-1.2.js');
add_head('jslink','https://preview.babylonjs.com/babylon.js');
add_head('jslink','https://preview.babylonjs.com/gui/babylon.gui.min.js');
//add_head('jslink','https://preview.babylonjs.com/cannon.js');
//add_head('jslink','https://preview.babylonjs.com/oimo.js');//physics
//add_head('jslink','/js/bab.js');
$bab=file_get_contents('prog/js/bab.js');
add_head('jscode','
window.addEventListener("DOMContentLoaded",function(){
	var canvas = document.getElementById("'.$rid.'");
	var engine = new BABYLON.Engine(canvas, true);
	var createScene = function(){
	var scene = new BABYLON.Scene(engine);
	'.$bab.'
	'.$r['code'].'
	'.$r['codb'].'
	return scene;}
	var scene = createScene();
	engine.runRenderLoop(function(){scene.render();});
	window.addEventListener("resize", function(){engine.resize();});
});
');
return tag('canvas',['id'=>$rid,'class'=>'canvas'],'');}

static function stream($p){
//$p['t']=self::$cols[0];
return appx::stream($p);}

#call (read)
static function tit($p){
//$p['t']=self::$cols[0];
return appx::tit($p);}

static function call($p){post('pw',720);
return self::play($p);
return iframe('/frame/scene/'.$p['id']);}

static function iframe($p){
add_head('csscode','html,body{overflow:hidden; width:100%; height:100%; padding:0; margin:0;}
.canvas{width:100%; height:100%; touch-action: none;}');
return appx::iframe($p);}

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