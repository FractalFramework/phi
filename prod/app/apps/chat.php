<?php

class chat{	
	static $private='0';
	
//install
static function install(){
	Sql::create('chat',array('cuid'=>'int','room'=>'int','txt'=>'text','vu'=>'int'),'');
	Sql::create('chatroom',array('ruid'=>'int','private'=>'int'),'');
	Sql::create('chatlist',array('roid'=>'int','ruid'=>'int'),'');}

static function injectJs(){
	return '';}

static function headers(){
	Head::add('csscode','
	/*display:inline-block; */
	.chatwrapper{background-color:#eaeaea; width:80vw; max-width:630px;}
	.chatcontent{height:calc(100vh - 180px); overflow-x:visible; overflow-y:auto; background-color:#eaeaea; padding:10px;}
	.chatpane{font-size:12px; color:black; background-color:lightblue; border:0px solid #aaa; margin:10px 0; padding:7px 10px; border-radius:2px; box-shadow: 2px 2px 4px #aaa; min-width:100px; max-width:60%;}
	.chatpane a{color:black;}
	.chatdate{display:block; font-size:x-small; margin:0 7px;}
	.chatprofile{background:whitesmoke; padding:9px; font-size:small;}
	.chatform{background-color:#f4f4f4; width:80vw; max-width:630px;}
	.flex-container{padding:0; margin:10px 0; list-style:none;
	-ms-box-orient: horizontal; display: -webkit-box; display: -moz-box; display: -ms-flexbox; display: -moz-flex; display: -webkit-flex; display: flex;}
	.chatarea{width:100%; resize:none; padding:4px;}
	.row{-webkit-flex-direction: row; flex-direction: row;}
	.row-reverse{-webkit-flex-direction: row-reverse; flex-direction: row-reverse;}
	.flex-container li{margin:0;}  
	.row-reverse .chatpane{background:steelblue; color:white;}
	.menu,.menuactive{clear:left; background:#f4f4f4; margin:0; padding:4px 7px;}
	.menuactive{background:#ffffff;}
	.menu:hover{background:#ffffff;}
');
	Head::add('jscode',self::injectJs());}

//read
static function attime($sec){$ret=lang('there_was').' ';
	if($sec>84600){$nj=floor($sec/84600); return $ret.$nj.' days';}
	if($sec>3600){$hr=floor($sec/3600); return $ret.$hr.'h ';}
	elseif($sec>60){$min=floor($sec/60); return $ret.$min.'min ';}
	else return $ret.$sec.'s';}

static function pane($r){$ret='';
	if($r)foreach($r as $k=>$v){$del='';
		$user=tag('li',['class'=>'chatprofile'],$v[0]);
		$txt=tag('li',['class'=>'chatpane'],nl2br($v[1]));
		$date=tag('div',['class'=>'chatdate'],self::attime($v[2]));
		if($v[0]==ses('user')){
			$bt=aj('popup|chat,del|id='.$v[3],pic('bolt'));
			$del=tag('li',['class'=>'chatdate'],$bt);}
		if($v[0]==ses('user'))$css='row-reverse'; else $css='row ';
		$ret.=div($user.$txt.$date.$del,'flex-container '.$css);}
	return $ret;}

static function clearntf($room){
	$ntf=Sql::read('id','telex_ntf','v','where 4usr="'.ses('user').'" and typntf=5 and txid='.$room.' and state=1');
	if($ntf)Sql::update('telex_ntf','state','0',$ntf);}

static function read($p){$id=val($p,'id'); $room=val($p,'room',ses('room'));
	if(!val($p,'appName'))self::clearntf($room);
	$cols='name,txt,now()-chat.up as now, chat.id as id';
	if($id)$where='where chat.id='.$id;//and chat.up>now()-86400 
	else $where='where room="'.$room.'" order by chat.id asc limit 100';
	$r=Sql::read_inner($cols,'chat','login','cuid','',$where);
	return self::pane($r);}

static function chatntf($roid){
	$r=Sql::read_inner('name','chatlist','login','ruid','rv','where roid='.$roid);
	if($r)foreach($r as $v)if($v!=ses('user'))$sql[]=[$v,ses('user'),5,$roid,'1'];
	if(isset($sql))Sql::insert2('telex_ntf',$sql);}

static function save($p){
	$msg=val($p,'chtsav'); $room=val($p,'room'); 
	if(trim($msg)){
		$id=Sql::insert('chat',array(ses('uid'),$room,unicode($msg),''));
		self::chatntf($room);
		if(isset($id))return self::read(['id'=>$id]);}}

static function del($p){
	if(!isset($p['ok']))return aj('chtbck,,x|chat,del|ok=1,id='.$p['id'],langp('confirm deleting'),'btdel');
	if($p['id'])$id=Sql::delete('chat',$p['id']);
		return self::read([]);}

static function create($p){$r=explode('-',val($p,'users')); $prv=val($p,'private');
	$room=Sql::insert('chatroom',[ses('uid'),$prv]);
	if($room){
		foreach($r as $v){
			$id=Sql::read('id','login','v','where name="'.$v.'"');
			Sql::insert('telex_ntf',[$id,ses('user'),5,'','1']);
			$ok=Sql::insert('chatlist',[$room,$v]);}
		$bt=lang('room created').' #'.$room;
		if($rid=val($p,'rid'))$ret=insertbt($bt,$room.':chat',$rid);
		//if(val($p,'rid'))$ret=telex::publishbt($room,'chat');
		else $ret=aj('chtwrp,,y|chat|headers=1,room='.$room,$bt,'btsav');}
	return $ret;}

static function access($p){$room=val($p,'room');
	if(val($p,'ask')){
		$ok=Sql::insert('chatlist',[$room,ses('uid')]);
		if($ok)return self::content(['room'=>$room]);}
	$ret=span(langp('access_refused'),'alert').' ';
	$reg=Sql::read('id','chatlist','v','where roid="'.$room.'" and ruid="'.ses('uid').'"');
	if(ses('uid') && !$reg)//auto-register
		$ret=aj('chtacc,,y|chat,access|ask=1,room='.$room,langp('inscription'),'btsav');
	elseif(!$reg)$ret=aj('chtwrp,,y|chat,menu',langp('private'),'btdel');
	else return self::read(['room'=>$room]);
	return div($ret,'','chtacc');}

//telex
static function chatinvit($p){$rid=val($p,'rid'); $ret='';
	$id=val($p,'id'); $usr=val($p,'usr'); $op=val($p,'op');
	if($op)sesrz('chtnvt',$usr,$op=='add'?$id:'x');
	$r=ses('chtnvt');
	if($r)foreach($r as $k=>$v){
		$avatar=profile::com($k,1);
		$ret.=aj('invits|chat,chatinvit|op=del,usr='.$k,$avatar.div($k),'cicon');}
	$ret=div($ret,'menu');
	$rusr=ses('chtnvt');
	if($rusr)$usrs=ses('uid').'-'.implode('-',$rusr); else $usrs=ses('uid');
	//$ret.=aj('newchat|chat,create|users='.$usrs.',rid='.$rid,lang('create room'),'btsav');
	$ret.=aj('newchat|chat,create|private=1,users='.$usrs.',rid='.$rid,lang('create private room'),'btsav');
	return $ret;}

static function newchat($p){$rid=val($p,'rid'); $ret=''; sez('chtnvt','');
	$sq='inner join telex_ab on name=ab ';
	$abs=Sql::read('login.id,ab','login','kv',$sq.'where usr="'.ses('user').'"');
	foreach($abs as $k=>$v)
		$ret.=aj('invits|chat,chatinvit|op=add,id='.$k.',usr='.$v.',rid='.$rid,$v,'btn').' ';
	$ret.=div('','','invits');
	return $ret;}

//menu
static function menu($p){$ret=''; $rid=val($p,'rid');//tlex
	$r=Sql::read('distinct(roid) as room,DATE_FORMAT(chatlist.up,"%d/%m/%Y") as date,vu','chatlist','rr','left join chat on ruid=cuid where ruid="'.ses('uid').'" order by chatlist.up desc');
	foreach($r as $v){$in='';
	$rc=Sql::read_inner('name,private','chatroom','login','ruid','rw','where chatroom.id='.$v['room'].'');
	$rb=Sql::read_inner('name','chatlist','login','ruid','rv','where roid='.$v['room'].' and name!="'.$rc[0].'"');
	$ntf=Sql::read('state','telex_ntf','v','where 4usr="'.ses('user').'" and typntf=5 and txid='.$v['room']);//
		$prv=langp($rc[1]?'private':'public').' ';
		$nfo=span('#'.$v['room'].' '.$v['date'],'small').' ';
		$im=profile::com($rc[0],2).' ';
		if($rc[1])$from=span(lang('with',1).' '.implode(', ',$rb),'small').' '; else $from='';
		if($rid)$in=insertbt(langp('use'),$v['room'].':chat',$rid);
		//if($rid)$in=telex::publishbt($v['room'],'chat');
		//if($rid)$pp='popup'; else $pp='chtwrp,,y';
		$cssvu=$v['vu']||$ntf?'active':'';
		$bt=div($im.$from.$prv.$nfo.$in,'menu'.$cssvu);
		$ret.=aj('pagup|chat|headers=1,room='.$v['room'],$bt);
	}
	$ret.=div(toggle('newchat|chat,newchat',langp('select users'),'btsav')).' ';
	$ret.=div('','','newchat');
	return $ret;}

static function roomusers($p){$room=val($p,'room'); $ret='';
	$r=Sql::read_inner('name','chatlist','login','ruid','rv','where roid="'.$room.'"');
	foreach($r as $v)$ret.=aj('popup|profile,read|headers=1,usr='.$v,$v);
	return div($ret,'list');}

static function comtlx($p){$rid=val($p,'rid'); $bt=pic('comment').sp();
	//steps of invitation
	//$ret=aj('newchat|chat,newchat|rid='.$rid,$bt.lang('new chat'),'btsav').' ';
	$ret=aj('newchat|chat,create|users='.ses('uid').',rid='.$rid,'#'.lang('new chat'),'btsav').' ';
	$ret.=aj('newchat|chat,menu|rid='.$rid,lang('existing chat'),'btn');
	$ret.=hlpbt('chatconn');
	$ret.=div('','','newchat');
	return $ret;}

static function com($p){
	$tit=div(lang('chat'),'btit');
	return $tit.div(self::menu(''),'chatwrapper','chtwrp');;}

#content
static function content($prm){
	//self::install();
	if(!ses('uid'))return $form=App::open('login');
	$room=sez('room',val($prm,'room',val($prm,'param')));
	if(!$room)return div(self::menu(''),'chatwrapper','chtwrp');
	//head
	$head=btj(pic('close'),'Close(\'popup\');','btn');
	$head.=aj('pagup|chat,com|headers=1','#'.$room,'btn');
	$head.=dropdown('chat,roomusers|room='.$room,lang('members'),'btn');
	//room
	$ex=Sql::read('id','chatroom','v','where id="'.$room.'"');
	$reg=Sql::read('id','chatlist','v','where roid="'.$room.'" and ruid="'.ses('uid').'"');
	if($ex && !$reg)return div(self::access(['room'=>$room]),'chatwrapper','chtwrp');
	elseif($room && !$ex)return aj('chtwrp,,y|chat,menu',langp('closed'),'btdel');
	else $txt=self::read(['room'=>$room]);
	//form
	$js=Ajax::js(['com'=>'atend,chtbck,resetform,scrollBottom,chtbck','app'=>'chat,save','prm'=>'room='.$room,'inp'=>'chtsav']);
	$prm=['id'=>'chtsavfrm','action'=>'javascript:if(getbyid(\'chtsav\').value)'.$js];
	$area=tag('textarea',['id'=>'chtsav','placeholder'=>'message','class'=>'chatarea','rows'=>'2','maxlenght'=>'1000','onkeypress'=>'checkEnter(event,\'chtsavfrm\');'],'').br();
$ret=div($head,'chatform').div($txt,'chatcontent','chtbck');
$ret.=tag('form',$prm,div($area,'chatform').hidden('chtroom',$room));
//$ret.=aj('chtwrp|chat,menu','menu','btn');
//$ret.=aj('chtwrp|chat,invite','invite','btn');
return div($ret,'chatwrapper','chtwrp','');}
}
?>