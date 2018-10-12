<?php
class upsql{
static $private=6;
static $server='tlex.fr';

static function error(){
switch(json_last_error()){
case JSON_ERROR_NONE:$ret='Aucune erreur';break;
case JSON_ERROR_DEPTH:$ret='Profondeur maximale atteinte';break;
case JSON_ERROR_STATE_MISMATCH:$ret='Inadéquation des modes ou underflow';break;
case JSON_ERROR_CTRL_CHAR:$ret='Erreur lors du contrôle des caractères';break;
case JSON_ERROR_SYNTAX:$ret='Erreur de syntaxe ; Json malformé';break;
case JSON_ERROR_UTF8:$ret='Caractères UTF-8 malformés';break;
default:$ret='Erreur inconnue';break;}
return $ret;}

static function call($p){
$p=val($p,'app','');
$f='http://'.self::$server.'/api/upsql/p='.$p.',mth=render';
$d=files::get($f);
//$d=file_get_contents($f);
if(!ses('utf'))$d=utf8_decode_b($d);
//$d=unicode2($d); eco($d);
//$r=json_decode($d,true);//
//$r=utf_r($r,1);
if($d)$r=json_dec($d);
if($_SERVER['HTTP_HOST']!=self::$server)
	if(isset($r) && is_array($r)){
		sqlsav2($p,$r,0,1);
		if($p=='lang')ses('lang',lang_com(ses('lng')));
		if($p=='icons')ses('icon',icon_com());
		return 'renove '.$p.' ok';}
	else return 'nothing'.self::error();}

static function render($p){
$table=val($p,'p');
$keys=sqlcols($table,1);
if($table=='login')return;
elseif($table=='desktop')$wh='where uid=1';
elseif($table=='articles')$wh='where uid=1';
else $wh='';
$r=sql($keys,$table,'rr',$wh,0);
$ret=json_enc($r);
return $ret;}

static function menu($p){//system tables
$r=array('lang','icons','help','desktop','labels','conn','articles','sys','syslib','devnote');
foreach($r as $k=>$v)
	if($v!='login')$ret[]=aj($p['rid'].'|upsql,call|app='.$v,$v,'btn');
return implode('',$ret);}

static function content($p){
$p['rid']=randid('md');
$p['p1']=val($p,'param',val($p,'p1'));//unamed param before
$bt=hlpbt('upsql');
$bt.=self::menu($p);
return $bt.div('','',$p['rid']);}
}
?>