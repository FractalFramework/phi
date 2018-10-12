<?php

class chat{	
static $private='1';
static $a='chat';
static $db='chat';
static $cb='cht';
static $cols=['tit','list','old','pub'];
static $typs=['var','var','int','int'];
static $boot;

function __construct(){
$r=['a','db','cb','cols'];
foreach($r as $v)appx::$$v=self::$$v;}

static function boot(){$a=self::$a;
if(self::boot==null)self::$boot=new $a;}

static function install($p=''){
appx::install(array_combine(self::$cols,self::$typs));
sqlcreate('chatxt',['bid'=>'int','rusr'=>'var','txt'=>'var'],1);
sqlcreate('chambr',['bid'=>'int','musr'=>'var'],1);}

static function admin($p){$p['o']='1';
return appx::admin($p);}

static function titles($p){
return appx::titles($p);}

static function injectJs(){
add_head('jscode','chatlive();');}

static function headers(){
add_head('csscode','');}

#edit
static function del($p){return appx::del($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}

static function archive($p){$id=val($p,'del'); $action=val($p,'act');
if($act=='remove')sqlup(self::$db,'old',1,$id);}

static function toggle($p){$v=$p['v']; $id=$p['id']; $bid=$p['bid']; $rid=$p['rid'];
if(val($p,'sav'))sqlup(self::$db,$id,$v,$bid);
if(!$v){$ic='user-secret'; $t='private';}else{$ic='users'; $t='public';}
$j=$rid.'|chat,toggle|sav=1,id='.$id.',bid='.$bid.',rid='.$rid.',v='.($v?'0':'3');
return span(aj($j,ico(''.$ic,22).lang($t)).hidden($id,$v),'',$rid);}

static function listers($id,$o=''){
$d=sql('list',self::$db,'v',$id); if($d)$r=array_flip(explode(',',$d));
if($o){$o=sqljoin('name',self::$db,'login','uid','v',['chat.id'=>$id]); $r[$o]=1;}
if(isset($r))return $r;}

static function invite_people($p){$id=val($p,'id'); $ra=self::listers($id);
$ret=aj('invits|chat,save_friends|id='.$id,langp('cancel'),'btn').' ';
$r=sql('ab','tlex_ab','rv','where usr="'.ses('user').'"');
if($r)foreach($r as $k=>$v)if(!isset($ra[$v]))
	$ret.=aj('invits|chat,save_friends|op=add,id='.$id.',usr='.$v,$v,'btn').' ';
return $ret;}

static function save_friends($p){$d=''; $r='';
$id=val($p,'id'); $op=val($p,'op'); $usr=val($p,'usr');
if($id)$d=sql('list',self::$db,'v',$id);
$r=self::listers($id);
if($op=='add')$r[$usr]=1; elseif($op=='del')unset($r[$usr]);
if(is_array($r))$d=implode(',',array_keys($r));
if($id)sqlup(self::$db,'list',$d,$id);
return self::chat_friends($p);}

static function chat_friends($p){$id=val($p,'id'); $ret=''; $d='';
$r=self::listers($id); $d='';
if($r)foreach($r as $k=>$v){$bt=profile::com($k,1).div($k);//avatar
	$ret.=aj('invits|chat,save_friends|op=del,id='.$id.',usr='.$k,$bt,'cicon');}
if(isset($r))$d=implode(',',array_keys($r)); $ret.=hidden('list',$d);
return $ret;}

static function form($p){$id=val($p,'id'); $cb=self::$cb; $ret=''; $people='';
$ret=label('tit',lang('title'));
$ret.=div(input('tit',val($p,'tit'),63,lang('title'),'',255));
$ret.=label('pub',lang('pub'));
$ret.=div(self::toggle(['id'=>'pub','bid'=>$id,'v'=>val($p,'pub'),'rid'=>randid('tg')]));
$ret.=hidden('old','');
if($id){
	$ret.=aj('invits|chat,invite_people|id='.$id,langp('invite'),'btn');
	$people=self::chat_friends($p);
	$ret.=div($people,'','invits');}
else $ret.=hidden('list','');
return div($ret,'','people');}

static function create($p){return appx::create($p);}
static function edit($p){return appx::edit($p);}

//read
static function attime($sec){$ret=lang('there_was').' '; $sec=time()-$sec;
if($sec>84600*30){$n=floor($sec/84600/30); return $ret.$n.' '.langs('month',$n,1);}
elseif($sec>84600){$n=floor($sec/84600); return $ret.$n.' '.langs('day',$n,1);}
elseif($sec>3600){$n=floor($sec/3600); return $ret.$n.' '.langs('hour',$n,1);}
elseif($sec>60){$n=floor($sec/60); return $ret.$n.' '.langs('minute',$n,1);}
else return $ret.$sec.' s';}

static function clearntf($id){
$q=['typntf'=>'5','txid'=>$id,'4usr'=>ses('user'),'state'=>1];
$ntf=sql('id','tlex_ntf','v',$q);
if($ntf)sqlup('tlex_ntf','state','0',$ntf);}

static function chatntf($id){
//$r=self::listers($id,1);
$r=sql('rusr','chatxt','k',['bid'=>$id]);
if($r)foreach($r as $k=>$v)if($k!=ses('user')){
	$ra=['4usr'=>$k,'byusr'=>ses('user'),'typntf'=>5,'txid'=>$id];
	$ex=sql('id,state','tlex_ntf','rw',$ra);
	if($ex[0] && !$ex[1])sqlup('tlex_ntf','state',1,$ex[0]);
	elseif(!$ex){$ra['state']=1; $rb[]=$ra;}}
if(isset($rb))sqlsav2('tlex_ntf',$rb);}

static function say($p){
$txt=val($p,'chtsav'); $id=val($p,'id');
if(trim($txt)){
	$p['nid']=sqlsav('chatxt',[$id,ses('user'),unicode($txt)]);
	self::chatntf($id);
	if($p['nid'])return self::read($p);}}

static function delmsg($p){$id=$p['id'];
if(!isset($p['ok']))
	return aj('chtbck,,x|chat,delmsg|ok=1,id='.$id,langp('confirm deleting'),'btdel');
$p['id']=sql('bid','chatxt','v',$id);//find bid
if($id)sqldel('chatxt',$id);
return self::read($p);}

#play
static function roomusers($p){$id=val($p,'id'); $ret=''; $r=self::listers($id);
if($r)foreach($r as $k=>$v)$ret.=aj('popup|tlex,profile|small=1,usr='.$k,$k);
return div($ret,'list');}

static function pane($r){$ret='';
if($r)foreach($r as $k=>$v){$del='';
	$clr=profile::init_clr(['usr'=>$v[1]]); $txclr=clrneg($clr,1);
	$sty='background-color:#'.$clr.'; color:#'.$txclr.';';
	$user=tag('li',['class'=>'chatprofile','style'=>$sty],$v[1]);
	$txt=voc($v[2],'chatxt-txt-'.$v[0]);//translate
	$txt=tag('li',['class'=>'chatpane'],nl2br($txt));
	$date=tag('div',['class'=>'chatdate'],self::attime($v[3]));
	if($v[1]==ses('user')){$css='row-reverse';//
		$bt=aj('popup|chat,delmsg|id='.$v[0],ico('flash'));
		$del=tag('li',['class'=>'chatdate'],$bt);}
	else $css='row ';
	$ret.=div($user.$txt.$date.$del,'flex-container '.$css);}
return $ret;}

static function read($p){$id=val($p,'id'); $nid=val($p,'nid');
if(val($p,'vu'))self::clearntf($id);
if($nid)$w='where id='.$nid;
else $w='where bid="'.$id.'" order by id desc limit 100';
$r=sql('id,rusr,txt,timeup','chatxt','',$w);
//if($r)$r=array_reverse($r);
return self::pane($r);}

static function play($p){$id=$p['id'];
//$head=btj(ico('close'),'Close(\'popup\');','btn');
$head=aj(self::$cb.'|chat,stream|headers=1','#'.$id,'btn');
$head.=bubble('chat,roomusers|id='.$id,lang('members'),'btn',1);
$rt=sqljoin('name,tit',self::$db,'login','uid','rw',$id);
$head.=span($rt[1],'bold').' - '.span($rt[0],'small');
$txt=self::read(['id'=>$id,'vu'=>1]);
$js=ajs('begin,chtbck,resetform,scrollTop|chat,say','id='.$id,'chtsav');
$p=['id'=>'chtsavfrm','action'=>'javascript:if(getbyid(\'chtsav\').value)'.$js];
$area=tag('textarea',['id'=>'chtsav','placeholder'=>'message','class'=>'chatarea','rows'=>'2','cols'=>'100','maxlenght'=>'1000','onkeypress'=>'checkEnter(event,\'chtsavfrm\');'],'').br();
$ret=div($head,'chatform');
$ret.=tag('form',$p,div($area,'chatform').hidden('chtid',$id));
$ret.=div($txt,'chatcontent','chtbck');
$ret.=hidden('chtroom',$id);
return $ret;}

static function stream_notifs($p){$ret=''; $a=self::$a; $cb=self::$cb; $usr=ses('user');
$r=sql('byusr,txid,dateup','tlex_ntf','',['typntf'=>5,'4usr'=>$usr,'state'=>1]);
if($r)foreach($r as $k=>$v){
	$tit=sql('tit',self::$db,'v',$v[1]);
	$bt=ico('arrow-right').' '.$tit.' '.span($v['2'],'date').' '.span(lang('by').' '.$v[0],'small');
	$ret.=aj($cb.'|'.$a.',call|id='.$v[1],$bt,'licon active');}
return $ret;}

static function stream_tlx($p){$ret='';
$a=self::$a; $cb=self::$cb; $db=self::$db; $usr=ses('user');
$r=sqljoin($db.'.id,uid,tit,pub,name,dateup',$db,'login','uid','rr','where uid='.ses('uid').' order by uid asc, id desc');
if($r)foreach($r as $k=>$v){
	$ret.=popup($a.',call|id='.$v['id'],ico('arrow-right').' '.$v['tit'].' '.span($v['date'],'date').' '.span(lang('by').' '.$v['name'],'small'),'');}
return $ret;}

static function discussion($p){
return self::stream_tlx($p);
}

static function stream($p){
$p['t']=self::$cols[0];
$ret=self::stream_notifs($p);
$ret.=appx::stream($p);
return $ret;}

static function calltlx($p){$id=val($p,'id');
$ret=self::stream_notifs($p);
return $ret.div(self::stream_tlx($p),'lisb','');}

#interfaces
static function tit($p){
$p['t']=self::$cols[0];//first column is title
return appx::tit($p);}

//call (read)
static function access($p){
$r=sql('id,uid,list,pub',self::$db,'ra',$p['id']);
if(!$r)return help('chat not exists');
if($r['uid']!=ses('uid') && $r['pub']==1){
	sesr('chat',$r['id'],$r['uid']);//owner
	$rb=array_flip(explode(',',$r['list'])); $usr=ses('user');
	if(!isset($rb[$usr]))return div(pic('private').' '.hlpxt('private chat'),'board');}}

static function call($p){$id=val($p,'id');
$er=self::access($p); if($er)return $er;
return div(self::play($p),'chatwrapper',self::$cb.$id);}

//com (write)
static function com($p){
return appx::com($p);}

//interface
static function content($p){
//self::install();
return appx::content($p);}
}
?>