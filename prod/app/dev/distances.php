<?php

class distances{	
static $private='0';
static $db='_model';
static $a='_model';

/*static function install(){
	sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
	$r[]=['','j','popup|distances,content','plus',lang('open')];
	$r[]=['','pop','core,help|ref=_model_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=_model','code','Code'];
	return $r;}

static function injectJs(){return;}
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
static function call($p){$ret='';
	//$p['msg'].': '.$p['inp1'];
	//(AB)² = (AT)² + (BT)² – 2 (AT).(BT). cos Y
	/*a) Oomo - 12h 31 m 14s - +9° 18' 7"
	14,4,9.301947°,7.808374
	b) Iox - HD191408
	19.74,-36.101111,122.799583
	c) base des 3 DOOKAIA - HD150680 Zeta Herculis
	35,31.603028,70.321431*/
	
	//cos a=a/h; sin a=b/h; tan a=b/a cotan a=a/b
	
	$ra=['Oomo',9.301947,7.808374,14.4];
	$rb=['OIX',-36.101111,122.799583,19.74];
	$rc=['Dookaïa',31.603028,70.321431,35];
	//distance ab
	$ab_diff=[$ra[1]-$rb[1],$ra[2]-$rb[2]];
	
	
	return $ret;}

static function com(){
	return self::content($p);}

#content
static function content($p){
	//self::install();
	$p['p1']=val($p,'param',val($p,'p1'));
	$ret=input('inp1','value1','','1');
	$ret.=aj('popup|distances,call|msg=text|inp1',lang('send'),'btn');
return div($ret,'pane');}
}
?>