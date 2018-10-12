<?php
class genetics extends appx{
static $private=0;
static $a='genetics';
static $db='genetics';
static $cb='gnx';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];
static $conn=0;
static $gen=1;
//static $db2='genetics_vals';//sub
static $open=0;
static $qb='';//db

//first col,txt,answ,com(settings),code,day,clr,img,nb,cl,pub
//$db2 must use col "bid" <-linked to-> id

function __construct(){//informe parent
$r=['a','db','cb','cols','conn','gen'];//'db2',
foreach($r as $v)parent::$$v=self::$$v;}

static function install($p=''){
//sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','val'=>'var'],1);
parent::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; return parent::admin($p);}
static function titles($p){return parent::titles($p);}
static function injectJs(){return;}
static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

#edit
static function collect($p){return parent::collect($p);}
static function del($p){
//$p['db2']=self::$db2;
return parent::del($p);}

static function save($p){return parent::save($p);}
static function modif($p){return parent::modif($p);}
static function create($p){
//$p['pub']=0;//default privacy
return parent::create($p);}

//subform
static function subops($p){return parent::subops($p);}
static function subedit($p){return parent::subedit($p);}
static function subform($p){return parent::subform($p);}
static function subedit_form($r){return parent::subedit_form($r);}

//form
//static function fc_tit($k,$v){}
static function form($p){
//$p['html']='txt';
//$p['fctit']=1;
//$p['barfunc']='barlabel';
return parent::form($p);}

static function edit($p){
//$p['collect']=self::$db2;
//$p['help']=1;
//$p['sub']=1;
//$p['execcode']=1;
return parent::edit($p);}

#build
static function build($p){
return parent::build($p);}

static function template(){
//return parent::template();
return '[[[tit:var]*class=tit:div][[txt:gen]*class=txt:div]*class=paneb:div]';}

static function play($p){
//$r=self::build($p);
return parent::play($p);}

static function stream($p){
//$p['t']=self::$cols[0];
return parent::stream($p);}

#call (read)
static function tit($p){
//$p['t']=self::$cols[0];
return parent::tit($p);}

static function call($p){
return parent::call($p);}

#com (edit)
static function com($p){return parent::com($p);}
static function uid($id){return parent::uid($id);}
static function own($id){return parent::own($id);}

#interface
static function content($p){
self::install();//
return parent::content($p);}

static function api($p){
return parent::api($p);}
}
?>