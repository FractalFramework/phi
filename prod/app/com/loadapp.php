<?php

class loadapp{	
static $private='0';
static $db='loadapp';
static $a='loadapp';

/*static function install(){
sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
$r[]=['','j','popup|loadapp,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=loadapp_app','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=loadapp','code','Code'];
return $r;}

static function titles($p){
$d=val($p,'inp1');
$r['content']='welcome';
$r['call']='loadapp';
if(isset($r[$d]))return lang($r[$d]);}

#read
static function call($p){
return app($p['inp1']);}

static function com(){
return inputcall('popup|loadapp,call||inp1','inp1',lang('load app'),32);}

#content
static function content($p){
$p['p1']=val($p,'param',val($p,'p1'));
$ret=self::com();
return div($ret,'board');}
}
?>