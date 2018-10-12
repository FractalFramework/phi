<?php

class tstjs{	
static $private='0';
static $db='tstjs';
static $a='tstjs';
static $cb='cnj';

/*static function install(){
sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
$r[]=['','j','popup|tstjs,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=tstjs_app','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=tstjs','code','Code'];
return $r;}

static function injectJs(){return '
function execode(id){
	var d=getbyid(id).value; //alert(d);
	d+=" getbyid(\'cnjtst\').innerHTML=ret;";
	getbyid("cnjs").innerHTML=d;
	eval(d);
}
';}
static function headers(){
add_head('csscode','textarea{width:auto;}');
add_head('code','<script type="text/javascript" id="cnjs"></script>');
add_head('jscode',self::injectJs());}

#build
/*static function build($p){$id=val($p,'id');
$r=sql('all',self::$db,'ra',$id);
return $r;}*/

#read
static function call($p){
return $p['inp1'];}

static function com($p){
$ret=textarea('inp1','','60','30','','console');
//$bt=aj(self::$cb.',,1|tstjs,call||inp1',langp('send'),'btn');
$bt=btj(langp('send'),atj('execode','inp1'),'btn').br();
//$ret=inputcall($j,'inp1',val($p,'p1'),32).$bt;
return div($bt.$ret);}

#content
static function content($p){
//self::install();
$p['p1']=val($p,'param',val($p,'p1'));
$ret=div('','right','cnjtst','width:400px; border:1px dashed gray; padding:9px;');
$ret.=self::com($p);
return div($ret,'pane');}
}
?>