<?php

class admin_sys{
static $private='6';
static $db='sys';
static $maj='';

static function install(){
sqlcreate(self::$db,array('dir'=>'var','app'=>'var','func'=>'var','vars'=>'var','code'=>'var','txt'=>'var','lang'=>'var'),1);}

//edit
static function edit(){
$ret=form::com(['table'=>self::$db]);}

//save
static function save($p){
$app=val($p,'app'); $func=val($p,'func'); $lang=val($p,'lang');
$w='where app="'.$app.'" and func="'.$func.'" and lang="'.$lang.'"';
$id=sql('id',self::$db,'v',$w);
if($id){
	$txt=sql('txt',self::$db,'v',$id);
	if($txt && !$p['txt'])$p['txt']=$txt;
	sqlups(self::$db,$p,$id);}
else $id=sqlsav(self::$db,$p);
if(isset(self::$maj[$id]))unset(self::$maj[$id]);
return $id;}

static function update($p){
$id=val($p,'id'); $txt=val($p,'tx'.$id); $rid=val($p,'rid');
sqlup(self::$db,'txt',$txt,$id);
return self::modif($id,$txt,$rid);}

static function modif($id,$txt,$rid){
if(auth(6))$ret=textarea('tx'.$id,$txt,40,4);
else $ret=div($txt,'pane');
if(auth(6))$ret.=aj($rid.'|admin_sys,update|id='.$id.'|tx'.$id,pic('save'));
$ret.=aj('popup|admin_sys,seecode|id='.$id,pic('view'));
return $ret;}

//read
static function read($p){$app=val($p,'app'); if(!$app)return;
$w='where app="'.$app.'" and lang="'.ses('lng').'"';
$r=sql('id,func,vars,txt',self::$db,'rr',$w);
foreach($r as $k=>$v){$rid=randid('tx');
	$e=self::modif($v['id'],$v['txt'],$rid);
	$r[$k]['txt']=div($e,'',$rid);}
return mktable($r);}

static function seecode($p){$id=val($p,'id');
$ret=sql('code',self::$db,'v',$id);
return div(build::Code($ret),'paneb');}

//build (methods)
static function build($f,$app){$rf=explode('/',$f);
$d=files::read($f); $rb='';
$ra=explode('static function ',$d);
foreach($ra as $v){
	$fnc=before($v,'{',1);
	$vr=explode('(',$fnc); $func=$vr[0];
	$vars='('.(isset($vr[1])?$vr[1]:'');
	$code=trim(accolades($v));
	if($code)$rb[]=['dir'=>$rf[1],'app'=>$app,'func'=>$func,'vars'=>$vars,'code'=>$code,'txt'=>'','lang'=>ses('lng')];}
return $rb;}

static function buildlib(){
$f='/prog/lib.php';
$r=self::build($f,'lib');
if($r)foreach($r as $v)$rb[]=self::save($v);
if(isset($rb))return implode(',',$rb);}

//dirs
static function reflush($p){
$app=val($p,'app'); if(!$app)return;
$f=unit::locate($app); 
$r=self::build($f,$app);
if($r)foreach($r as $v)$rb[]=self::save($v);
if(isset($rb))return implode(',',$rb);}

static function batch($dir){$dr=ses('dev').'/'.$dir;
$r=sesfunc('dir_scan',''.$dr,1); //p($r);
if($r)foreach($r as $k=>$v){
	if(is_file($dr.'/'.$v))$rb[]=self::reflush(['app'=>substr($v,0,-4)]);
	elseif(is_dir($dr.'/'.$v)){
		$ra=read_dir(''.$dr.'/'.$v);
		if($ra)foreach($ra as $va)if(is_file($dr.'/'.$v.'/'.$va))
			$rb[]=self::reflush(['app'=>substr($va,0,-4)]);}}
if(isset($rb))return implode(' ',$rb);}

//operation
static function pushall($p){
self::$maj=sql('id',self::$db,'k','where lang="'.ses('lng').'"');
$ret=self::batch('admin');
$ret.=self::batch('com');
$ret=self::batch('core');
$ret.=self::batch('tlex');
//$ret.=self::buildlib();
foreach(self::$maj as $k=>$v)sqldel(self::$db,$k);//obsoletes
return $ret;}

//menu
static function menu(){
$r=sql('distinct(app)',self::$db,'rv','where lang="'.ses('lng').'" order by app');
sort($r);
return select('app',$r,'',1);}

//interface
static function content($p){
//self::install();
$rid=randid('dcl');
$bt=self::menu();
//$bt.=input('app','','10',1);
$bt.=aj($rid.',,y|admin_sys,read|rid='.$rid.'|app',langp('view'),'btn');
if(auth(6)){
	$bt.=aj($rid.'|admin_sys,reflush|rid='.$rid.'|app',langp('update'),'btn');
	$bt.=aj($rid.'|admin_sys,pushall|dir=app,rid='.$rid,langp('update all'),'btn');}
return $bt.div('','board',$rid);}
}
?>
