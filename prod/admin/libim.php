<?php

class libim{	
static $private='0';
static $db='_libim';
static $a='libim';
static $cb='lbm';

static function install(){
	sqlcreate(self::$db,['idb'=>'int','im'=>'var','src'=>'var'],1);}

static function admin(){
	$r[]=['','j','popup|libim,content','plus',lang('open')];
	$r[]=['','pop','core,help|ref=_model_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=_model','code','Code'];
	return $r;}

#reader
static function reader($d,$b=''){
list($p,$o,$c)=readconn($d);
$s=strrpos($p,'.');
if($s){$xt=substr($p,$s+1);
	if($xt=='jpg' or $xt=='png' or $xt=='gif')$c='img';}
if($c=='img')return $p;}

#build
static function build($p){$id=val($p,'id');
	$r=sql('all',self::$db,'ra',$id);
	return $r;}

static function sav($id,$src,$im){
	$ex=sql('id',self::$db,'v',['idb'=>$id,'src'=>$src,'im'=>$im]);
	if(!$ex)sqlsav(self::$db,[$id,$im,$src]);
	if($ex)return $im.br();}

static function find($r,$src){$ret='';
	foreach($r as $k=>$v){
		if($n=strpos($v,'.jpg')){
			$im=conn::parse($v,'libim','reader',0);
			if($im && substr($im,0,4)!='http')$ret.=self::sav($k,$src,$im);}}
	return $ret;}

static function find2($r,$src){$ret='';
	foreach($r as $k=>$v)if($v && substr($v,0,4)!='http')$ret.=self::sav($k,$src,$v);
	return $ret;}

static function rebuild($p){$ret='';
	//$r=dir_scan('/img/full');
	$r=sql('id,txt','tlex','kv',''); $ret=self::find($r,'tlex');
	$r=sql('id,avatar','profile','kv',''); $ret.=self::find2($r,'avatar');
	$r=sql('id,banner','profile','kv',''); $ret.=self::find2($r,'banner');
	return $ret;}

static function delold($p){
	$ra=dir_scan('/img/full');
	$rb=sql('id,im',self::$db,'kv','');
	$r=array_diff($ra,$rb); pr($r);
	return $r;}

#read
static function call($p){
	return $p['inp1'];}

static function com(){
	return self::content($p);}

#content
static function content($p){
	self::install();
	$p['p1']=val($p,'param',val($p,'p1'));
	$ret=input('inp1','value1','','1');
	$ret.=aj(self::$cb.'|libim,call||inp1',lang('send'),'btsav');
	$ret.=aj(self::$cb.'|libim,rebuild||',lang('rebuild'),'btn');
	$ret.=div('','',self::$cb);
return div($ret,'pane');}
}
?>