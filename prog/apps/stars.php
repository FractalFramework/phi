<?php
class stars{
static $private=0;
static $a='stars';
static $db='stars';
static $cb='mdl';
static $cols=['tit','hip','pub'];
static $typs=['var','var','int'];
static $conn=0;
static $open=0;
static $qb='';

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

#build
static function build($p){$w='';
if($p['hip'])$w='hip in('.$p['hip'].') ';
$r=sql('hd,hip,rarad,decrad,round(dist*3.261564,2) as dist,spect,round(lum,2)','_hipparcos','rr','where '.$w.''); 
return $r;}

static function play($p){
$ra=appx::build($p);
$r=self::build($ra);
if($r)foreach($r as $k=>$v){
	$r[$k]['rarad']=deg2ra(rad2deg($v['rarad']));
	$r[$k]['decrad']=deg2dec(rad2deg($v['decrad']));
	$r[$k]['dist']=($v['dist']);}//parsec2al
array_unshift($r,array_keys(current($r))); //pr($r);
return mktable($r);}

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
self::install();//
return appx::content($p);}

static function api($p){
return appx::api($p);}
}
?>