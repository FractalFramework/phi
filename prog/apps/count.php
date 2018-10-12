<?php
class count extends appx{
static $private=0;
static $a='count';
static $db='count';
static $cb='mdl';
static $cols=['tit','number','pub'];
static $typs=['var','int','int'];
static $conn=0;
static $open=0;
static $qb='db';

//first col,txt,answ,com(settings),code,day,clr,img,nb,cl,pub
//$db2 must use col "bid" <-linked to-> id

function __construct(){
$r=['a','db','cb','cols'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
//sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','val'=>'var'],1);
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; return appx::admin($p);}
static function titles($p){return appx::titles($p);}
static function injectJs(){return;}
static function headers(){
add_head('csscode','
.bigbt a{display:block; width:100%; height:140px; background:rgba(0,0,0,0.1); font-size:xx-large; text-align:center; vertical-align:middle;}
.bigbt a:hover{background:rgba(0,0,0,0.2);}');
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

static function fc_number($k,$v){return hidden($k,$v);}
static function form($p){
//$p['fcnumber']=1;
//$p['barfunc']='barlabel';
return appx::form($p);}

static function edit($p){
//$p['collect']=self::$db2;
//$p['help']=1;
//$p['sub']=1;
//$p['execcode']=1;
return appx::edit($p);}

#build
static function build($p){
return appx::build($p);}

#add
static function add($p){
$r=appx::build($p); $r['id']=$p['id'];
if(appx::permission(self::$db,$p['id'],1))$r['number']+=1;
$er=self::modif($r); //if($er)return $er;
return self::play($p);}

static function play($p){
$id=$p['id']; $rid=self::$cb.$id;
$r=self::build($p);
$ret=div($r['tit'],'tit');
if(appx::permission(self::$db,$id,1))
	$bt=aj($rid.'|count,add|id='.$id,$r['number'].picto('add'));
else $bt=$r['number'];
return $ret.div($bt,'bigbt',$rid);}

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