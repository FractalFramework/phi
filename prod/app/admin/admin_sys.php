<?php

class admin_sys{
static $private='1';
static $db='sys';
static $maj='';

static function install(){
Sql::create(self::$db,array('dir'=>'var','app'=>'var','func'=>'var','vars'=>'var','code'=>'var','txt'=>'var','lang'=>'var'),1);}

//edit
static function edit(){
$ret=Form::com(['table'=>self::$db]);}

//save
static function save($p){
$app=val($p,'app'); $func=val($p,'func'); $lang=val($p,'lang');
$w='where app="'.$app.'" and func="'.$func.'" and lang="'.$lang.'"';
$id=Sql::read('id',self::$db,'v',$w);
if($id){
	$txt=Sql::read('txt',self::$db,'v','where id='.$id);
	if($txt && !$p['txt'])$p['txt']=$txt;
	Sql::updates(self::$db,$p,$id);}
else $id=Sql::insert(self::$db,$p);
if(isset(self::$maj[$id]))unset(self::$maj[$id]);
return $id;}

static function update($p){
$id=val($p,'id'); $txt=val($p,'tx'.$id); $rid=val($p,'rid');
Sql::update(self::$db,'txt',$txt,$id);
return self::modif($id,$txt,$rid);}

static function modif($id,$txt,$rid){
$ret=textarea('tx'.$id,$txt,40,4);
$ret.=aj($rid.'|admin_sys,update|id='.$id.'|tx'.$id,pico('save'));
$ret.=aj('popup|admin_sys,seecode|id='.$id,pico('view'));
return $ret;}

//read
static function read($p){$app=val($p,'app'); if(!$app)return;
$w='where app="'.$app.'" and lang="'.ses('lng').'"';
$r=Sql::read('id,func,vars,txt',self::$db,'rr',$w);
foreach($r as $k=>$v){$rid=randid('tx');
	$e=self::modif($v['id'],$v['txt'],$rid);
	$r[$k]['txt']=div($e,'',$rid);}
return Build::table($r);}

static function seecode($p){$id=val($p,'id');
$ret=Sql::read('code',self::$db,'v','where id='.$id);
return div(Build::Code($ret),'paneb');}

//build (methods)
static function build($p){
$app=val($p,'app'); if(!$app)return;
$f=unit::locate($app); $rf=explode('/',$f);
$d=File::read($f);
$ra=explode('static function ',$d);
foreach($ra as $v){
	$fnc=before($v,'{',1);
	$vr=explode('(',$fnc); $func=$vr[0];
	$vars='('.(isset($vr[1])?$vr[1]:'');
	$code=trim(accolades($v));
	if($code)$rb[]=['dir'=>$rf[1],'app'=>$app,'func'=>$func,'vars'=>$vars,'code'=>$code,'txt'=>'','lang'=>ses('lng')];}
return $rb;}

//dirs
static function reflush($p){
$r=self::build($p);
if($r)foreach($r as $v)$rb[]=self::save($v); //p($r);
if(isset($rb))return implode(',',$rb);}

static function batch($dir){$dr=ses('dev').'/'.$dir;
$r=sesclass('Dir','scan',''.$dr,1); //pr($r);
if($r)foreach($r as $k=>$v){
	if(is_file($dr.'/'.$v))$rb[]=self::reflush(['app'=>substr($v,0,-4)]);
	elseif(is_dir($dr.'/'.$v)){
		$ra=Dir::read($dr.'/'.$v);
		if($ra)foreach($ra as $va)if(is_file($dr.'/'.$v.'/'.$va))
			$rb[]=self::reflush(['app'=>substr($va,0,-4)]);}}
if(isset($rb))return implode(' ',$rb);}

//operation
static function pushall($p){
self::$maj=Sql::read('id',self::$db,'k','where lang="'.ses('lng').'"');
$ret=self::batch('core');
$ret.=self::batch('app');
//pr(self::$maj);
foreach(self::$maj as $k=>$v)Sql::delete(self::$db,$k);//obsoletes
return $ret;}

//menu
static function menu(){
$r=Sql::read('distinct(app)',self::$db,'rv','where lang="'.ses('lng').'" order by app');
return select('app',$r,'',1);}

//interface
static function content($p){
self::install();
$rid=randid('dcl');
$bt=self::menu();
//$bt.=input('app','','10',1);
$bt.=aj($rid.'|admin_sys,read|rid='.$rid.'|app',langp('view'),'btn');
$bt.=aj($rid.'|admin_sys,reflush|rid='.$rid.'|app',langp('update'),'btn');
$bt.=aj($rid.'|admin_sys,pushall|dir=app,rid='.$rid,langp('update all'),'btn');
return $bt.div('','board',$rid);}
}
?>
