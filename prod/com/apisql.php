<?php
class apisql{
static $private=6;
static $server='tlex.fr';

static function call($p){
	$p=val($p,'app','');
	$f='http://'.self::$server.'/api.php?app=apisql&mth=render&p='.$p;
	$d=files::get($f);
	$r=json_dec($d); //p($r);
	if($_SERVER['HTTP_HOST']!=self::$server)
		if(isset($r) && is_array($r)){
			sqlsav2($p,$r,0,0,1);
			if($p=='lang')ses('lang',lang_com(ses('lng')));
			if($p=='icons')ses('icon',icon_com());
			return 'renove '.$p.' ok';}
		else return 'nothing'.upsql::error();}

static function render($table){
	$keys=sqlcols($table,1);
	if($table=='login')return;
	elseif($table=='desktop')$wh='where uid=1';
	elseif($table=='articles')$wh='where uid=1';
	else $wh='';
	$r=sql($keys,$table,'rr',$wh,0);
	$ret=json_enc($r);
	return $ret;}

static function menu($p){//system tables
	$r=array('lang','icons','help','desktop','labels','articles','sys','syslib','devnote');
	foreach($r as $k=>$v)
		if($v!='login')$ret[]=aj($p['rid'].'|apisql,call|app='.$v,$v,'btn');
	return implode('',$ret);}

static function content($p){
	$p['rid']=randid('md');
	$p['p1']=val($p,'param',val($p,'p1'));//unamed param before
	$bt=hlpbt('apisql');
	$bt.=self::menu($p);
	return $bt.div('','',$p['rid']);}
}
?>