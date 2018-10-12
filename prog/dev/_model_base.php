<?php

class _model_base{	
static $private='0';
static $db='_model_base';
static $a='_model_base';
static $cb='mdb';

/*static function install(){
sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
$r[]=['','j','popup|_model_base,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=_model_base_app','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=_model_base','code','Code'];
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
static function call($p){
return $p['msg'].': '.$p['inp1'];}

static function com($p){
$j='popup|_model_base,call||inp1';
$bt=aj($j,langp('send'),'btn');
return inputcall($j,'inp1',val($p,'p1'),32).$bt;}

#content
static function content($p){
//self::install();
$p['p1']=val($p,'param',val($p,'p1'));
$ret=self::com($p);
return div($ret,'pane');}
}
?>