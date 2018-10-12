<?php

class _genetics extends gen{	
static $private='0';
static $a='genetics';
static $cb='cnj';

/*static function install(){
sqlcreate(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
$r[]=['','j','popup|_genetics,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=_genetics_app','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=_genetics','code','Code'];
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

static function call($p){
$r=['msg'=>'Hellooo','bt'=>'ok'];
//$ret=conn::read(['msg'=>$p['inp1'],'app'=>'gen','mth'=>'reader','opt'=>$r,'ptag'=>'no','vars'=>$r]);
$ret=parent::read($p['inp1'],$r);
//pr(conn::$r);
return $ret;}

#content
static function content($p){
//self::install();
$p['p1']=val($p,'param',val($p,'p1'));
$d='[[hello*class=btsav:b] world*btn:span]';
$d='[msg=hello,bt=ok:setvar] [[[http-equiv=Content-Type:meta]:head][[[[msg:var]*name=text,row=20,cols=30:textarea][type=submit,value=[bt:var]:input]*action=/upload,method=post:form]:body]:html]';
$d='[meta=[http-equiv=Content-Type:meta]:setvar]
[head=[[meta:var]:head]:setvar]
[textarea=[[msg:var]*name=text,row=20,cols=30:textarea]:setvar]
[input=[type=submit,value=[bt:var]:input]:setvar]
[form=[[textarea:var][input:var]*action=/upload,method=post:form]:setvar]
[[head:var][[form:var]:body]:html]';
//$ret.=btj(langp('send'),atj('execode','inp1'),'btn').br();
$ret=textarea('inp1',$d,'60','10','','console').br();
$ret.=aj('cnjtst|_genetics,call||inp1',langp('send'),'btn').br();
$ret.=div(tag('pre','',''),'pre','cnjtst','width:400px; border:1px dashed gray;');
//$ret.=inputcall($j,'inp1',val($p,'p1'),32).$bt;
return div($ret,'pane');}
}
?>