<?php

class contact{
static $private='0';
static $db='contact';
static $open=1;
	
static function install(){
sqlcreate(self::$db,array('vuid'=>'int','cto'=>'int','cmail'=>'var','ctit'=>'var','ctxt'=>'text'),1);}

static function del($p){
sqldel(self::$db,$p['id']);
return self::read($p);}

static function save($p){$nid='';
$cto=val($p,'cto'); $mail=val($p,'cmail'); $tit=val($p,'ctit'); $txt=val($p,'ctxt');
$r=[ses('uid'),$cto,$mail,$tit,$txt];
if($txt)$nid=sqlsav(self::$db,$r);
if($cto)$to=sql('mail','login','v','where id='.$cto);
if($nid && $mail && $to)mail::send($to,$tit,$txt,$mail,'txt');
if($nid && $mail)return help('message posted','valid');
else return help('message not posted','alert');}

//builder
static function read($p){$rid=val($p,'rid');
$r=sqljoin('contact.id,name,cto,cmail,ctit,ctxt,dateup',self::$db,'login','vuid','rr','where cto="'.ses('uid').'" order by contact.id desc');
$tmp='[(name), ((cmail)) [(date)*class=date:span] (del)
[(ctit)*class=tit:div][(ctxt)*class=txt:div]*class=menu:div]';
$ret=aj($rid.'|contact',pic('back'),'btn').br();
if($r)foreach($r as $k=>$v){
	$v['del']=aj($rid.'|contact,del|rid='.$rid.',id='.$v['id'],langp('del'),'btxt');
	$ret.=vue::read($v,$tmp);}
return $ret;}

//com (tlex apps)
static function tit($p){$id=val($p,'id');
return sql('ctit',self::$db,'v',$id);}

static function com($p){$id=val($p,'id');
return self::menu($p);}

//call (connectors)
static function call($p){
return self::content($p);}

//interface
static function content($p){
//self::install();
$rid=randid('md'); $ret='';
$bt=tag('h1','',lang('contact'));
//if(auth(6))$ret=aj($rid.'|contact,read|rid='.$rid,langp('view'),'btn').br();
$r=['general','technical','groups','dev','ideas'];
$ret.=radio('ctit',$r,$r[0],1,1).br();
if(ses('uid'))$mail=sql('mail','login','v','where id='.ses('uid')); else $mail='';
$ret.=input_label('cmail',$mail,lang('from'));
$ret.=hidden('cto','1');
$ret.=textarea('ctxt','',64,14,lang('message')).br();
$ret.=aj($rid.'|contact,save||cmail,cto,ctit,ctxt',langp('send'),'btsav');
return div($ret,'',$rid);}
}
?>