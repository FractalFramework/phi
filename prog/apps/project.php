<?php
class project{
static $private=0;
static $a='project';
static $db='project';
static $cb='flp';
static $cols=['tit','txt','cl'];
static $typs=['var','var','int'];
static $conn=0;
static $db2='project_opts';
static $open=0;
static $qb='';

//first col,txt,answ,com(settings),code,day,clr,img,nb,cl,pub
//$db2 must use col "bid" <-linked to-> id

function __construct(){
$r=['a','db','cb','cols','db2'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
sqlcreate(self::$db2,['bid'=>'int','step'=>'var','app'=>'var','appid'=>'var','open'=>'int'],1);
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; return appx::admin($p);}
static function titles($p){return appx::titles($p);}
static function injectJs(){return;}
static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){$p['db2']=self::$db2; return appx::del($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function create($p){return appx::create($p);}

//subform
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}

//static function subedit_form($r){return appx::subedit_form($r);}

static function newapp($p){$id=$p['id']; $app=$p['app']; $ret='';
	$id=$app::create();
	//$ret=aj('input,app'.$id.'|project,newapp|id='.$id.',app='.$app,langp('new app'),'btn');;
	return $ret;}

static function subedit_form($r){$ret='';
	$applist=sql('com','desktop','rv','where dir like "/apps/tlex/%" and auth<=2 order by id');
	$ret=hidden('bid',$r['bid']); array_shift($r);
	foreach($r as $k=>$v){
		if($k=='app'){$ret.=div(datalist($k,$applist,$v).label($k,lang('app'))); $app=$v;}
		elseif($k=='appid'){$ret.=div(input($k,$v,'4','',1).label($k,lang('id'))); //$ret.=hidden($k,$v);
			if(!$v)$ret.=popup($app.',com|add=1',langp($app),'btsav').br();}
		elseif($k=='open'){
			$ret.=build::toggle(['id'=>$k,'v'=>$v]).label($k,lang('closed'));}
		else $ret.=div(input($k,$v,63,$k,'',512));}
return $ret;}

//static function fc_tit($k,$v){}
static function form($p){
//$p['html']='txt';
//$p['fctit']=1;
//$p['barfunc']='barlabel';
return appx::form($p);}

static function edit($p){
//$p['collect']=self::$db2;
$p['help']=1;
$p['sub']=1;
return appx::edit($p);}

#build
static function build($p){
return appx::build($p);}

static function template(){
//return appx::template();
return '[[(tit)*class=tit:div][(txt)*class=txt:div]*class=paneb:div]';}

static function play($p){
$r=self::build($p);
$rb=sql('id,step,app,appid,open',self::$db2,'rr',['bid'=>$p['id']]); //p($rb);
$ret=div($r['tit'],'txt');//if($v['open'])
//foreach($rb as $k=>$v)$ret.=autoggle('',$v['app'].',call|id='.$v['appid'],$v['step'],'licon');
foreach($rb as $k=>$v){
	$bt=voc($v['step'],self::$db2.'-step-'.$v['id']);//userlang when saving (todo)
	//$bt=hlpxt($v['step']);
	$rc[]=toggle('pjm'.$p['id'].'|'.$v['app'].',call|id='.$v['appid'],$bt,'');}
if(isset($rc))$ret.=div(implode('',$rc),'tabs');
return $ret.div('','','pjm'.$p['id']);}

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
}
?>