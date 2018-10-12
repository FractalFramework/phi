<?php

class connjs{	
static $private='0';
static $db='connjs';
static $a='connjs';
static $cb='cnj';

/*static function install(){
sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
$r[]=['','j','popup|connjs,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=connjs_app','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=connjs','code','Code'];
return $r;}

static function injectJs(){return '
function execode(id){
	var d=(getbyid(id).value); 
	//var d2=encodeURIComponent(d);
	//var d2=encodeURI(d); 
	var d2=escape(d);
	//alert(d2);
	getbyid("cnjtst").innerHTML="";
	d+=" getbyid(\'cnjtst\').innerHTML=ret;";
	//getbyid("cnjs").innerHTML=(d);
	eval(decodeURI(d));//decodeURIComponent//unescape
}
';}
static function headers(){
add_head('csscode','textarea{width:auto;}');
add_head('code','<script type="text/javascript" id="cnjs"></script>');
add_head('jslink','/js/conn.js');
add_head('jscode',self::injectJs());}

#build
/*static function build($p){$id=val($p,'id');
$r=sql('all',self::$db,'ra',$id);
return $r;}*/

#read
static function call($p){
$d=$p['inp1']; $ret='';
$r=explode("\n",$d);
foreach($r as $k=>$v){
	$v=trim($v);
	
}
return $ret;}

static function com($p){
$d='var d=\'[[hello|class=btsav:b] world|class=btn:span]\';';
$d='var d=\'[[[|http-equiv=Content-Type:meta]:head][[[|value=[msg:var],name=text,row=20,cols=30:textarea][|type=submit,value=[bt:var]:input]|action=/upload,method=post:form]:body]:html]\';';
$d.='var o={\'msg\':\'hello\',\'bt\':\'ok\'};';
$d.='var ret=connectors(d,o);';
/*$d.='
var ret=tag(\'hello\',\'b\',\'class=btsav\');
//var o={\'class\':\'btsav\',\'onclick\':attj(\'prompt\',\'hello\')};
//var ret=tag(\'hello\',\'b\',o);';*/
$ret=textarea('inp1',$d,'60','30','','console');
//$bt=aj(self::$cb.',,1|connjs,call||inp1',langp('send'),'btn');
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