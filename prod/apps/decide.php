<?php

class decide{
static $private='1';
static $a='decide';
static $db='decide';
static $cb='dcwrp';
static $cols=['txt','cl'];
static $typs=['var','int'];
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	sqlcreate('decide_args',['idPoll'=>'int','uid'=>'int','position'=>'int','txt'=>'var'],1);
	sqlcreate('decide_chat',['idArg'=>'int','uid'=>'int','txt'=>'var'],1);
	sqlcreate('decide_valid',['idArg'=>'int','uid'=>'int','val'=>'int'],1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function headers(){
	add_head('csscode','
.text {background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
.score {background:white; border-radius:2px; padding:2px 10px 0; font-size:medium; border:1px solid gray;}
.argyes {border:1px solid #22dd22;}
.argno {border:1px solid #dd2222;}
.for,.against {border:1px solid #dfdfdf;}
.for:hover {border:1px solid #22dd22;}
.against:hover {border:1px solid #dd2222;}
.resyes {background:#22dd22; color:white;}
.resno {background:#dd2222; color:white;}
.sublock {margin-left:40px;}
	');}

#sys
static function del($p){
	return appx::del($p);}
	
static function modif($p){
	return appx::modif($p);}

static function save($p){
	return appx::save($p);}

static function create($p){
	return appx::create($p);}

#editor
static function form($p){
	return appx::form($p);}

static function edit($p){
	return appx::edit($p);}

//generics
static function linktopoll($id,$idArg){
	if($idArg)$idArg='/'.$idArg;
	return lk('/decide/'.$id.$idArg,ico('link'),'btn');}

static function userdate($date,$name){
	$date=span(date('Y-m-d',strtotime($date)),'small');
	return span($date,'date').' '.small(lang('by').' '.$name);}

static function textarea($v){
	return textarea('text',$v,60,4,lang('description'),'',216).br();}

private static function security($table,$id){
	$uid=sql('uid',$table,'v',$id);
	if($uid==ses('uid'))return 1;}

//algo
static function algo($ra,$r){$ret=0;
	//stats
	foreach($r as $id=>$v){$tot=array_sum($v); $pos=$ra[$id];
		$agree=isset($v[1])?$v[1]:0; $disagree=isset($v[2])?$v[2]:0;
		$rt[$pos][$id]=$tot; $rs[$pos][$id]=($agree-$disagree)*$tot;}
	///ponderation of arguments
	//tot votes by type of arg
	if(isset($rt[1]))$tt[1]=array_sum($rt[1]); else $tt[1]=0;
	if(isset($rt[2]))$tt[2]=array_sum($rt[2]); else $tt[2]=0;
	//tot scores
	if(isset($rs[1]))$ts[1]=array_sum($rs[1]); else $ts[1]=0;
	if(isset($rs[2]))$ts[2]=array_sum($rs[2]); else $ts[2]=0;;
	///calc
	if($tt[1])$rs[1]=$ts[1]/$tt[1]; else $rs[1]=0;
	if($tt[2])$rs[2]=$ts[2]/$tt[2]; else $rs[2]=0;
	//poonderation of positions
	$tv=array_sum($tt);//tot votes
	if($tv)$ret=($rs[1]-$rs[2])/$tv;
	$score=$ts[1]-$ts[2];
	return ['ts'=>$ts,'tt'=>$tt,'res'=>$ret,'global'=>round($ret*100,1),'score'=>$score];}

static function argsResume($p){$id=$p['id']; $ret=''; 
	//args by position [position]=>[[idArg]]
	$ra=sql('id,position','decide_args','kv','where idPoll='.$id); //pr($ra);
	//votes by args [idArg]=>[[position]=>cumul]
	$cols='decide_args.id,val';
	$r=sqljoin($cols,'decide_valid','decide_args','idArg','kkc','where idPoll='.$id.' and (val=1 or val=2) order by idArg');
	if($ra)$res=self::algo($ra,$r); //pr($res);
	//$help=' '.hlpbt('decide_algo');
	$css=isset($res['global'])?'resyes':'resno';
	if(isset($res))$ret=span($res['global'].'%','score '.$css);
	if($ret)return div(lang('score').' '.$ret,'right');}

#vote
static function lead_save($p){
	$id=sql('id','decide_valid','v','where idArg="'.$p['idArg'].'" and uid="'.ses('uid').'"');
	if(isset($id))$p['val']=$p['val']!=$p['current']?$p['val']:'0';
	if(!isset($id))
		$p['idChat']=sqlsav('decide_valid',[$p['idArg'],ses('uid'),$p['val']]);
	else sqlup('decide_valid','val',$p['val'],$id);
	return self::vote($p);}

static function vote($p){$idArg=val($p,'idArg'); $pos=val($p,'position'); $kid=randid('decide');
	$vote=sql('val','decide_valid','v','where idArg="'.$idArg.'" and uid="'.ses('uid').'"');
	//tots
	$yes=sql('count(val)','decide_valid','v','where idArg="'.$idArg.'" and val="1"');
	$no=sql('count(val)','decide_valid','v','where idArg="'.$idArg.'" and val="2"');
	$pb=$kid.'|decide,lead_save|idArg='.$idArg.',position='.$pos.',current='.$vote;
	//vote buttons
	$cs1=$vote==1?' active':''; $cs2=$vote==2?' active':'';
	if($pos==1){$cp1='for'; $cp2='against';} else{$cp1='against'; $cp2='for';}
	$ck1=$vote==1?ico('check'):''; $ck2=$vote==2?ico('check'):'';
	$bt1=$ck1.' '.lang('agree',1).' ('.$yes.')';
	$bt2=$ck2.' '.lang('not agree',1).' ('.$no.')';
	if(ses('uid')){
		$ret=aj($pb.',val=1',$bt1,'btn'.$cs1.' '.$cp1);
		$ret.=aj($pb.',val=2',$bt2,'btn'.$cs2.' '.$cp2);}
	else $ret=span($bt1,'btn').span($bt2,'btn');
	return span($ret,'',$kid);}

#chat
static function chatDel($p){
	if(!self::security('decide_chat',$p['idChat']))return;
	if($p['idChat'])sqldel('decide_chat',$p['idChat']);
	return self::chatPane($p);}

static function chatSave($p){
	$p['idChat']=sqlsav('decide_chat',[$p['idArg'],ses('uid'),$p['text']]);
	return self::chatPane($p);}

static function chatAdd($p){$id=$p['id']; $idArg=$p['idArg'];
	$ret=self::textarea('');
	$ret.=aj($p['kid'].',,x|decide,chatSave|id='.$id.',idArg='.$idArg.'|text',lang('save'),'btsav');
	return div($ret,'pane');}

static function chatPane($p){$id=$p['id']; $idArg=$p['idArg']; $kid=randid('chat'); $ret='';
	$b='decide_chat'; $cols=$b.'.id as id,name,txt,'.$b.'.up as date';
	$r=sqljoin($cols,$b,'login','uid','rr','where '.$b.'.idArg='.$idArg.' order by '.$b.'.id desc');
	if(ses('uid'))$ret.=aj($kid.'|decide,chatAdd|kid='.$kid.',id='.$id.',idArg='.$idArg,langp('add comment'),'btn').br().br();//add
	//read
	if($r)foreach($r as $v){
		$by=span('#'.$v['id'],'btn').' '.self::userdate($v['date'],$v['name']);
		if($v['name']==ses('user'))$by.=span(aj($kid.'|decide,chatDel|id='.$id.',idArg='.$idArg.',idChat='.$v['id'].'',pic('del'),'btdel'),'right');
		$txt=div($v['txt'],'txt');
		$ret.=div($by.$txt,'pane');}
	return div($ret,'',$kid);}

#argument
static function argumentDel($p){$idArg=$p['idArg'];
	if(!self::security('decide_args',$idArg))return;
	if(!val($p,'ok'))return aj('vt'.$p['id'].'|decide,argumentDel|ok=1,'.prm($p),langp('really?'),'btdel').aj('arg'.$p['id'].'|decide,argumentPane|'.prm($p),langp('cancel'),'btn');
	sqldel('decide_args',$idArg);
	sqldel('decide_valid',$idArg,'idArg');
	return self::play($p);}

static function argumentSave($p){$id=$p['id'];
	$r=[$id,ses('uid'),$p['position'],$p['text']];
	$p['idArg']=sqlsav('decide_args',$r);
	return self::argumentPane($p);}

static function argumentedit($p){$id=$p['id'];
	$ret=self::textarea('');
	$ret.=aj('vt'.$id.',,x|decide,argumentSave|id='.$id.',position='.$p['position'].'|text',lang('save'),'btsav');
	return $ret;}

static function argumentAdd($p){$id=$p['id']; $ret='';
	$opts=['1'=>'positive','2'=>'negative'];
	$ret=radio('position',$opts,1,1);
	$ret.=aj('popup|decide,argumentedit|id='.$id.'|position',lang('create'),'btsav');
	return $ret;}

static function argumentPane($p){$ret=''; $bt='';
	$id=val($p,'id'); $idArg=val($p,'idArg');
	if(!isset($id) && $idArg)$id=sql('idPoll','decide_args','v',$idArg);
	$cols='position,name,txt,decide_args.up as date'; $w='where decide_args.id='.$idArg;
	$r=sqljoin($cols,'decide_args','login','uid','ra',$w);
	$rt=['1'=>'argYes','2'=>'argNo'];
	$cs=$r['position']==1?'argyes':'argno';
	//$ret=aj('vt'.$id.'|decide,play|id='.$id,'#'.$id,'btn');
	if($r['position'])$ret=span('#'.$idArg.' '.lang($rt[$r['position']]),'btn '.$cs).' ';
	$ret.=self::userdate($r['date'],$r['name']);
	$bt=self::linktopoll($id,$idArg);
	if($r['name']==ses('user'))$bt.=aj('arg'.$id.'|decide,argumentDel|idArg='.$idArg.',id='.$id,pic('delete'),'btdel');
	$ret.=span($bt,'right');//header
	$ret.=div($r['txt'],'txt');//txt
	$p['position']=$r['position']; $ret.=self::vote($p);//vote
	$n=sql('count(id)','decide_chat','v','where idArg="'.$idArg.'"');
	$bt='cht'.$idArg.'|decide,chatPane|id='.$id.',idArg='.$idArg;
	$ret.=toggle($bt,langp('comments').' ('.$n.')','btn');
	$ret=div($ret,'pane');
	$ret.=div('','sublock','cht'.$idArg);
	return $ret;}

//read
static function arguments($p){$id=$p['id']; $ret='';//todo
	$r=sql('id','decide_args','rv','where idPoll='.$id.' order by id');
	if($r)foreach($r as $k=>$v)//if(isset($rt[$v['position']])){
		$ret.=self::argumentPane($p+['idArg'=>$v]);
	return $ret;}

#stream
static function play($p){
	$id=val($p,'id'); $p['idArg']=val($p,'o'); $arg='';
	if(!$id)return;
	$cols='name,txt,cl,'.self::$db.'.up as date';
	$where='where '.self::$db.'.id='.$id;
	$r=sqljoin($cols,self::$db,'login','uid','ra',$where);
	if($r['cl']==1)
		return self::argsResume($p).div(nl2br($r['txt']),'txt').help('form closed','alert');
	//admin
	$go=aj('vt'.$id.'|decide,play|id='.$id,ico('refresh'),'btn');
	$by=self::userdate($r['date'],$r['name']);
	$bt=self::linktopoll($id,'');
	if(ses('uid')){//add argument
		$bt.=bubble('decide,argumentAdd|id='.$id,lang('add arg'),'btok',1);}
	$bt=div($bt,'right');
	$txt=div(nl2br($r['txt']),'txt');
	//args
	$n=sql('count(id)','decide_args','v','where idPoll="'.$id.'"');
	$args=toggle('arg'.$id.'|decide,arguments|id='.$id,lang('args').' ('.$n.')','btn');
	$args.=hlpbt('decide_args');
	//resume
	$resume=self::argsResume($p);
	//render
	$ret=div($go.' '.$by.$bt.br().$txt.$args.$resume,'pane');
	if($p['idArg'])$arg=self::argumentPane($p);
	$ret.=div($arg,'sublock','arg'.$id);
	return $ret;}

static function stream($p){
	$p['privacy']='0';
	return appx::stream($p);}

#interfaces
static function tit($p){
	$p['t']='txt';
	return appx::tit($p);}

//call (read)
static function call($p){
	return div(self::play($p),'','vt'.$p['id']);}

//com (edit)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}

?>