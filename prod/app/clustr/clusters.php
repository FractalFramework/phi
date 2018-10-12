<?php

class clusters{	
static $private='0';
static $db='clusters';
static $a='clusters';

/*static function install(){
sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
$r[]=['','j','popup|clusters,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=clusters_app','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=clusters','code','Code'];
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
return inputcall('popup|clusters,call||inp1','inp1',val($p,'p1'),32);}

#content
static function content($p){
//self::install();
$p['p1']=val($p,'param',val($p,'p1'));
$ret=self::com($p);
return div($ret,'pane');}
}
?>