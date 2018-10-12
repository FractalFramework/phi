<?php

class operations{	
static $private='0';
static $db='_model';
static $a='_model';

/*static function install(){
	sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
	$r[]=['','j','popup|operations,content','plus',lang('open')];
	$r[]=['','pop','core,help|ref=_model_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=_model','code','Code'];
	return $r;}

static function injectJs(){return '
	var r=["az","ae","rt","rz","rzr","rze","ey"];
	var ret=r.sort();
	alert(JSON.stringify(ret));';}
static function headers(){
	add_head('csscode','');
	add_head('jscode',self::injectJs());}

static function titles($p){
	$d=val($p,'appMethod');
	$r['content']='welcome';
	$r['build']='model';
	if(isset($r[$d]))return lang($r[$d]);}

#build
/*static function build($p){$id=val($p,'id');
	$r=sql('all',self::$db,'ra',$id);
	return $r;}*/

#read
static function call($p){
	return $p['msg'].': '.$p['inp1'];}

static function com(){
	return self::content($p);}

#content
static function content($p){
	//self::install();
	$p['p1']=val($p,'param',val($p,'p1'));
	$ret=input('inp1','value1','','1');
	$ret.=aj('popup|operations,call|msg=text|inp1',lang('send'),'btn');
return div($ret,'pane');}
}
?>