<?php

class dev{
static $private='6';
static $a='dev';
static $cb='dvdt';

function __construct(){
foreach(['a','cb'] as $v)appx::$$v=self::$$v;}

static function injectJs(){return '';}
static function headers(){
add_head('jscode',self::injectJs());
add_head('csscode','');}

static function titles($p){return appx::titles($p);}
static function admin($p){return;}

static function seeCode($p){$f=val($p,'f');
if($f)$ret=file_get_contents(self::opn($f));
return div(build::Code($ret),'paneb');}

static function model($p){$app=val($p,'app');
$d=files::read(ses('dev').'/app/model.php');
$d=str_replace('model',$app,$d);
return $d;}

static function reinit($p){$app=val($p,'app');
$d=self::model(['app'=>$app]); $f=self::opn($app);
if(auth(6) && $f && $d)files::write($f,$d);
return self::read(['f'=>$f]);}

static function exists($f,$dr){
$r=explore($dr);
foreach($r as $k=>$v)$r[$k]=portion($v,'/','.',1,1);
if(in_array($f,$r))return true;}

static function save($p){
$f=val($p,'f'); $d=val($p,'nwc');
if(auth(6) && $f && $d)files::write($f,$d);
return self::read($p);}

static function edit($p){$ret=''; $f=val($p,'f');
if($f)$d=file_get_contents($f); else return '';
$ret=aj('devedit|dev,save|f='.$f.'|nwc',langp('save'),'btsav').br();
$ret.=textarea('nwc',htmlentities($d),60,20,'','console');
return $ret;}

static function create($p){
$new=val($p,'inp1');
$p['f']=ses('dev').'/app/dev/'.$new.'.php';
$p['nwc']=self::model(['app'=>$new]);
$ex=self::exists($new,ses('dev').'/app');
if(!$ex)$ex=self::exists($new,ses('dev').'/core');
if(!$ex)return self::save($p); else return lang('already exists');}

static function add($p){
$ret=input('inp1','appname','',1);
$ret.=aj('devedit|dev,create||inp1',lang('create'),'btsav').' ';
return $ret;}

static function del($p){
if(!val($p,'ok'))return aj('devedit|dev,del|ok=1,f='.val($p,'f'),lang('really?'),'btdel');
else unlink(val($p,'f')); return;}

//editfunc
static function opn($a){$root=ses('dev'); $dr='';
$f=$root.'/core/'.$a.'.php'; if(file_exists($f))$dr='core';
if(!$dr){$f=$root.'/app/'.$a.'.php'; if(file_exists($f))$dr='app';}
if(!$dr){$r=sesfunc('scandir_b',$root.'/app',1);
	foreach($r as $k=>$v)if(file_exists($root.'/app/'.$v.'/'.$a.'.php'))$dr='app/'.$v;}
if($dr)return $root.'/'.$dr.'/'.$a.'.php';}

static function resetfunc($p){$a=val($p,'a'); $fc=val($p,'fc'); $f=self::opn($a);
$d=file_get_contents($f);
$df=innerfunc($d,$fc);
return trim($df);
return textarea('',$df,'','','','console');}

static function savecnfg($p){$f=val($p,'f'); $k=val($p,'k'); $nwc=val($p,'v'.$k,"''");
if($f)$d=file_get_contents($f); else return;
$var=segment($d,'$'.$k.'=',';');
$p['nwc']=str_replace('$'.$k.'='.$var.';','$'.$k.'='.$nwc.';',$d);
self::save($p);
return self::editvars($p);}

static function editvars($p){$ret='';
$f=val($p,'f'); $app=portion($f,'/','.',1,1);
if($f)$d=file_get_contents($f);
$vr=['private','db','cb','cols','typs','conn','db2','open','qb'];//,'a'
foreach($vr as $v)$rb[$v]=segment($d,'$'.$v.'=',';'); //p($rb);
foreach($rb as $k=>$v){
	$ret.=div(input('v'.$k,$v?$v:0).
	aj('cnf'.$app.'|dev,savecnfg|f='.$f.',k='.$k.'|v'.$k,langpi('save'),'btsav').
	label('v'.$k,$k).hlpbt('modelvar-'.$k));}
return div($ret,'','cnf'.$app);}

static function savefunc($p){$f=val($p,'f'); $fc=val($p,'fc'); $nwc=val($p,'nwc'.$fc);
if($f)$d=file_get_contents($f); else return; //echo $f;
$df=innerfunc($d,$fc); //echo $nwc;
$p['nwc']=str_replace($df,$nwc,$d);
self::save($p);//self::read($p)//
//return textarea('',htmlentities($p['nwc']),'60','20','','console');
return span(lang('ok'),'btok');}

static function editfunc($p){
$f=val($p,'f'); $fc=val($p,'fc'); $app=portion($f,'/','.',1,1);
if($f)$d=file_get_contents($f); else return;
$df=innerfunc($d,$fc);
$ret=aj('edtc'.$fc.',,xz|dev,savefunc|f='.$f.',fc='.$fc.'|nwc'.$fc,langp('save').' : '.$fc,'btsav');
$ret.=aj('nwc'.$fc.'|dev,resetfunc|a='.$app.',fc='.$fc.'|nwc'.$fc,langp('restore'),'btdel');
$ret.=aj('nwc'.$fc.'|dev,resetfunc|a=_model,fc='.$fc.'|nwc'.$fc,langp('reset'),'btdel');
$ret.=aj('nwc'.$fc.'|dev,resetfunc|a=appx,fc='.$fc.'|nwc'.$fc,langp('appx'),'btdel');
$ret.=span('','','edtc'.$fc).br();
$ret.=textarea('nwc'.$fc,htmlentities($df),'40','16','','console');
return div($ret,'','edtb'.$fc);}

static function funcs($d){$r=explode('function ',$d);
foreach($r as $k=>$v)$ret[]=substr($v,0,strpos($v,'('));
return $ret;}

static function read($p){$ret='';
$f=val($p,'f'); $app=portion($f,'/','.',1,1);
if($app)$_SESSION['afc'][$f]=$app; //echo $app;
if($f)$d=file_get_contents($f); else return '';
$bt=aj('devedit|dev,read|f='.$f,langp('reload'),'btn').' ';
$bt.=aj('popup|dev,edit|f='.$f,langp('edit'),'btn').' ';
$bt.=aj('popup|dev,seeCode|f='.$f,langp('see'),'btn').' ';
$bt.=aj('devedit|dev,reinit|app='.$app,langp('reset'),'btn').' ';
$bt.=aj('devedit|dev,del|f='.$f,langp('erase'),'btdel').' ';
$bt.=aj('popup|'.$app,langp('load'),'btn').' ';
$bt.=lk('/'.$app,pic('url'),'btn',1).br();
$fs=['injectJs','headers','subedit_form','template','play'];
$rf=self::funcs($d); $rfb=['call','template','play','build','edit','headers'];
$ret.=toggle('edtcnfg|dev,editvars|f='.$f.',a='.$app,lang('config'),'btn').div('','','edtcnfg');
if($rf)foreach($rf as $k=>$v)if($v!='__construct' && strpos($v,'<?')===false){
	if(in_array($v,$rfb))$c='btok'; else $c='btn';
	$ret.=toggle('edt'.$v.'|dev,editfunc|f='.$f.',fc='.$v,$v,$c).div('','','edt'.$v);}
return $bt.div($ret,'','').div('','','appedit');}

static function menu(){$r=explore('prog');
foreach($r as $k=>$v){$f=after($v,'/'); $dr=before($v,'/'); $xt=ext($f);
	if($xt=='.php')$rb[$f]=aj('devedit|dev,read|f='.$dr.'/'.$f,before($f,'.'),'btn').' ';}
if($rb)ksort($rb,SORT_STRING);
if($rb)$ret=implode('',$rb);
return div($ret,'');}

static function com($p){
$ret=self::read($p);
return div($ret,'','devedit');}

static function content($p){$p['f']=val($p,'p');
$bt=popup('dev,add',langp('new'),'btn').' ';
$bt.=bubble('dev,menu',langp('open'),'btn').' ';
$bt.=batch(ses('afc'),'devedit|dev,read|f=$k');
return $bt.div(self::com($p),'');}
}
?>
