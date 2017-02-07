<?php

class contact{
	static $private='0';
	static $db='contact';
		
	static function install(){
	Sql::create(self::$db,array('vuid'=>'int','cto'=>'int','cmail'=>'var','ctit'=>'var','ctxt'=>'text'),1);}
	
	static function del($p){
	Sql::delete(self::$db,$p['id']);
	return self::read($p);}
	
	static function save($p){$nid='';
	$cto=val($p,'cto'); $mail=val($p,'cmail'); $tit=val($p,'ctit'); $txt=val($p,'ctxt');
	$r=[ses('uid'),$cto,$mail,$tit,$txt];
	if($txt)$nid=Sql::insert(self::$db,$r);
	if($cto)$to=Sql::read('mail','login','v','where id='.$cto);
	if($nid && $mail && $to)Mail::send($to,$tit,$txt,$mail,'html');
	if($nid && $mail)return help('message posted','valid');
	else return help('message not posted','alert');}

//builder
static function read($p){$rid=val($p,'rid');
	$r=Sql::read_inner('contact.id,name,cto,cmail,ctit,ctxt,dateup',self::$db,'login','vuid','rr','where cto="'.ses('uid').'" order by contact.id desc');
	$tmp='[_name, (_cmail) [_date*class=date:span] _del
	[_ctit*class=tit:div][_ctxt*class=txt:div]*class=menu:div]';
	$ret=aj($rid.'|contact',pico('back'),'btn').br();
	if($r)foreach($r as $k=>$v){
		$v['del']=aj($rid.'|contact,del|rid='.$rid.',id='.$v['id'],langp('del'),'btxt');
		$ret.=Vue::read($v,$tmp);}
	return $ret;}
	
	//com (telex apps)
	static function tit($p){$id=val($p,'id');
	return Sql::read('cname',self::$db,'v','where id='.$id);}
	
	static function com($p){$id=val($p,'id');
	return self::menu($p);}
	
	//call (connectors)
	static function call($p){
	return self::content($p);}
	
	//interface
	static function content($p){
	//self::install();
	$rid=randid('md');
	$ret=tag('h1','',lang('contact'));
	if(auth(6))$ret.=aj($rid.'|contact,read|rid='.$rid,langp('view'),'btn').br();
	$r=[lang('general'),lang('technic'),lang('pro'),lang('admin')];
	$ret.=radio($r,'ctit','v',$r[0]).br();
	if(ses('uid'))$mail=Sql::read('mail','login','v','where id='.ses('uid')); else $mail='';
	$ret.=input('cmail',$mail,'30',lang('from'));
	$ret.=hidden('cto','1').br();
	$ret.=textarea('ctxt','',64,14,lang('message')).br();
	$ret.=aj($rid.'|contact,save||cmail,cto,ctit,ctxt',langp('send'),'btsav');
	return div($ret,'',$rid);}
}
?>
