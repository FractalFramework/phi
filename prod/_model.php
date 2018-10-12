<?php
class _model{
static $private=0;
static $a='_model';
static $db='model';
static $cb='mdl';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];//var,text,int,date
static $conn=0;//0,1(ptag),2(brut),no(br), while using 'txt'
static $gen=0;//use template motor, while using 'txt'
static $db2='model_vals';//second db, used in subform (better than com) or to collect datas
static $open=1;//open directly in tlex, //1=on place, 2=iframe
static $qb='db';//associated nosql-table (type db/json) - dB is the internal no-sql database

/*known cols: (assume = logic devices)
- first col is actually used for title ['t']
- col "txt" (var) will accept connectors ['conn'] or interpret templates ['gen']
- col "com" will assume settings
- col "day" is a date
- col "clr" is a color, with a colorpicker
- col "img" is a image, with a selector
- col "code" is for edit code
- col "nb" number 1-10
- col "nb1" number 1-100
- col "cl" mean close
- col "pub" will assume privacy
$db2 must use col "bid" <-linked to-> id*/

function __construct(){
$r=['a','db','cb','cols','db2','conn','gen','qb'];
foreach($r as $v)appx::$$v=self::$$v;}

//install databases
static function install($p=''){
//sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','val'=>'var'],1);
appx::install(array_combine(self::$cols,self::$typs));}

//display in admin
static function admin($p){$p['o']='1';
return appx::admin($p);}

//titles of popups
static function titles($p){return appx::titles($p);}

//injected javascript (in current page or in popups)
static function injectJs(){return;}

//headers
static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

#edit
//collected datas from public forms
static function collect($p){return appx::collect($p);}

//edition of elements
static function del($p){//->stream
//$p['db2']=self::$db2;//second db
return appx::del($p);}

static function save($p){//->edit
return appx::save($p);}

static function modif($p){//->edit
return appx::modif($p);}

static function create($p){//->form
//$p['pub']=0;//default privacy
return appx::create($p);}

//form
//used in secondary database db2
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}
static function subedit_form($r){return appx::subedit_form($r);}

//callback function for the form label named 'tit'
//static function fc_tit($k,$v){}

static function form($p){
//$p['html']='txt';//contenteditable for txt
//$p['fctit']=1;//form col call fc_tit();
//$p['barfunc']='barlabel';//function for bar()
//$p['execcode']=1;//associate an exec. thing to the field "code"
return appx::form($p);}

static function edit($p){//->form, ->call
//$p['collect']=self::$db2;//second db
//$p['help']=1;//ref of help 'model_edit'
//$p['sub']=1;//active sub process (attached datas)
return appx::edit($p);}

#build
//datas
static function build($p){//datas
return appx::build($p);}

//vue
static function template(){
//return appx::template();
//can use [tit:var] or smart vars '(tit)'
//[txt:gen] should be used while self::$gen is 1
return '[[(tit)*class=tit:div][(txt)*class=txt:div]*class=paneb:div]';}

//player
static function play($p){//->build, ->template
//$r=self::build($p);
return appx::play($p);}

//list of elements
static function stream($p){
$p['t']=self::$cols[0];//used col as title
return appx::stream($p);}

#call (read)
static function tit($p){
$p['t']=self::$cols[0];//used col as title
return appx::tit($p);}

static function call($p){//->play
return appx::call($p);}

#com (edit)
static function com($p){//->content
return appx::com($p);}
static function uid($id){//author
return appx::uid($p);}
static function own($id){//owner (used to propose edition on apps)
return appx::own($p);}

#interface
static function content($p){//->stream, ->call
self::install();//
return appx::content($p);}

 #api
static function api($p){
return appx::api($p);}
}
?>