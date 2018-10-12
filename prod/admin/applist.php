<?php

class applist{
public $ret;

static function headers(){
add_head('csscode','
.block{border:1px solid grey; border-radius:2px; background:white;
padding:10px; margin:10px 0;}
.block a:hover{text-decoration:underline;}
.block span{display:block; cursor:auto;}
.block span:hover{background:white;}
.block div{}');}

static function appdirs($o=''){
$r=read_dir(ses('dev'));
$rb=['admin','core','js','css']; if(!$o)$rb[]='dev';
foreach($rb as $v)if(isset($r[$v]))unset($r[$v]);
return $r;}

static function findapp($d){
	if(is_string($d)){$app=before($d,'.');
		if(method_exists($app,'content')){
			$private=isset($app::$private)?$app::$private:0;
			if(!$private or auth($private))return $app;}}}

static function folder($d){
$r=read_dir(ses('dev').'/'.$d); //pr($r);
if($r)foreach($r as $k=>$v){$a=self::findapp($v); if($a)$ret[]=$a;}
return $ret;}

static function allapps(){
$r=self::appdirs();
foreach($r as $dir=>$rb){
	if(is_array($rb) && $dir)foreach($rb as $k=>$v){
		$a=self::findapp($v);
		if($a)$ret[]=$a;}}
return $ret;}

static function comdir(){
$r=self::appdirs();
if($r)foreach($r as $dir=>$files){
	if(is_array($files) && $dir)foreach($files as $k=>$v){
		$a=self::findapp($v);
		if($a)$ret[]=[$dir,'pop',$a.'|headers=1','',$a];}}
return $ret;}

static function appsofdir($dir,$files){$ret='';
foreach($files as $k=>$v){$a=self::findapp($v);
	if($a)$ret.=popup($a.'|headers=1',langp($a),'');}
if($ret)return div(div($dir),'block').div($ret,'list');}

static function tlex(){$ret='';
$r=sql('com','desktop','rv','where dir like "/apps/tlex%" and auth<=2');
$bt=tag('h1','',lang('applist'));
if($r)foreach($r as $k=>$v){$va=after($v,'/'); $nm=span('[:'.$va.']','grey small');
	$ret.=div(tag('h3','',pic($va,32).hlpxt($va).' '.$nm).hlpxt($va.'_app','board'));}
return $bt.div($ret,'board');}

static function content($prm){$ret='';
if(isset($prm['iframe']))$mod=$prm['iframe'];
else $mod=get('app');	
$r=self::appdirs();
if(isset($r))foreach($r as $k=>$v){
	if(is_array($v))$ret.=self::appsofdir($k,$v);
	else $rb[$k]=$v;}
if(isset($rb))$first=self::appsofdir('root',$rb);
else $first='';
return $first.$ret;}

}

?>