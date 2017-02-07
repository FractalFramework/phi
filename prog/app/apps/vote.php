<?php

class vote{
	static $private='0';
	
	static function headers(){
		Head::add('csscode','
	.text {background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
	.score {background:white; border-radius:2px; padding:2px 10px 0; font-size:medium; border:1px solid gray;}
	.argyes {border:1px solid #22dd22;}
	.argno {border:1px solid #dd2222;}
	.for:hover {border:1px solid #22dd22;}
	.against:hover {border:1px solid #dd2222;}
	.resyes {background:#22dd22; color:white;}
	.resno {background:#dd2222; color:white;}
	.sublock {margin-left:40px;}
		');}
	
	//install
	static function install($init=0){//return;
		Sql::create('vote_lead',array('uid'=>'int','txt'=>'var','closed'=>'int'),$init);
		Sql::create('vote_args',array('idPoll'=>'int','uid'=>'int','position'=>'int','txt'=>'var'),$init);
		Sql::create('vote_chat',array('idArg'=>'int','uid'=>'int','txt'=>'var'),$init);
		Sql::create('vote_valid',array('idArg'=>'int','uid'=>'int','val'=>'int'),$init);}
	
	//generics
	static function linktopoll($idPoll,$idArg){
		if($idArg)$idArg=',idArg='.$idArg;
		return href('/app/vote/'.$idPoll.$idArg,pic('link'),'btn').' ';}
	
	static function userdate($date,$name){
		$date=span(date('Y-m-d',strtotime($date)),'small');
		return $date.' '.small(lang('by').' '.$name).' ';}
	
	static function textarea($v=''){
		$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
		return tag('textarea',$r,$v).br().span('','small right','strcount');}
	
	static function edit($com,$p,$v){
		$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
		$ret=tag('textarea',$r,$v).br().span('','small right','strcount');
		$ret.=aj($com.'|'.$p.'|text',lang('save'),'btsav');
		return $ret;}
	
	private static function security($table,$id){
		$uid=Sql::read('uid',$table,'v','where id='.$id);
		if($uid==ses('uid'))return 1;}
	
	static function add($p){
		$com='pllscnt'.$p['idPoll'].',,x|vote,pollSave'; $p='';
		return self::edit($com,$p,'');}
	
	#vote
	static function voteSave($p){
		$idVote=Sql::read('id','vote_valid','v','where idArg="'.$p['idArg'].'" and uid="'.ses('uid').'"');
		if(isset($idVote))$p['val']=$p['val']!=$p['current']?$p['val']:'0';
		if(!isset($idVote))
			$p['idChat']=Sql::insert('vote_valid',array($p['idArg'],ses('uid'),$p['val']));
		else Sql::update('vote_valid','val',$p['val'],$idVote);
		return self::voteRead($p);}
	
	static function voteRead($p){
		$idArg=$p['idArg'];
		$rid=randid('vote');
		$vote=Sql::read('val','vote_valid','v','where idArg="'.$idArg.'" and uid="'.ses('uid').'"');
		//tots
		$yes=Sql::read('count(val)','vote_valid','v','where idArg="'.$idArg.'" and val="1"');
		$no=Sql::read('count(val)','vote_valid','v','where idArg="'.$idArg.'" and val="2"');
		$pb=$rid.'|vote,voteSave|idArg='.$idArg.',position='.$p['position'].',current='.$vote;
		//vote buttons
		$cs1=$vote==1?' active':''; $cs2=$vote==2?' active':'';
		if($p['position']==1){$cp1='for'; $cp2='against';} else{$cp1='against'; $cp2='for';}
		$ck1=$vote==1?pic('check'):''; $ck2=$vote==2?pic('check'):'';
		$bt1=$ck1.' '.lang('agree',1).' ('.$yes.')';
		$bt2=$ck2.' '.lang('not agree',1).' ('.$no.')';
		if(ses('uid')){
			$ret=aj($pb.',val=1',$bt1,'btn'.$cs1.' '.$cp1);
			$ret.=aj($pb.',val=2',$bt2,'btn'.$cs2.' '.$cp2);}
		else $ret=span($bt1,'btn').span($bt2,'btn');
		return span($ret,'',$rid);}
	
	#chat
	static function chatDel($p){
		if(!self::security('vote_chat',$p['idChat']))return;
		if($p['idChat'])Sql::delete('vote_chat',$p['idChat']);
		return self::chatRead($p);}
	static function chatSave($p){
		$p['idChat']=Sql::insert('vote_chat',array($p['idArg'],ses('uid'),$p['text']));
		return self::chatRead($p);}
	static function chatAdd($p){
		$ret=self::textarea();
		$ret.=aj($p['rid'].',,x|vote,chatSave|idPoll='.$p['idPoll'].',idArg='.$p['idArg'].'|text',lang('save'),'btsav');
		return $ret;}
	static function chatRead($p){$ret='';
		$rid=randid('chat');
		$b='vote_chat';
		$cols=$b.'.id as id,name,txt,'.$b.'.up as date';
		$where='where '.$b.'.idArg='.$p['idArg'];
		$r=Sql::read_inner($cols,$b,'login','uid','rr',$where);
		//add
		if(ses('uid'))$ret.=aj('popup|vote,chatAdd|rid='.$rid.',idPoll='.$p['idPoll'].',idArg='.$p['idArg'],langs('add,comment'),'btn').br().br();
		//read
		if($r)foreach($r as $v){
			$by=span('#'.$v['id'],'btn').' '.self::userdate($v['date'],$v['name']);
			if($v['name']==ses('user'))$by.=span(aj($rid.'|vote,chatDel|idPoll='.$p['idPoll'].',idArg='.$p['idArg'].',idChat='.$v['id'].'',lang('del'),'btdel'),'right');
			$txt=div($v['txt'],'text');
			$ret.=div($by.$txt,'pane');}
		return div($ret,'',$rid);}
	
	#argument
	static function argumentDel($p){
		if(isset($p['idArg'])){
			if(!self::security('vote_args',$p['idArg']))return;
			Sql::delete('vote_args',$p['idArg']);
			Sql::delete('vote_valid',$p['idArg'],'idArg');}
		return self::poll($p);}
	
	static function argumentSave($p){
		$p['idArg']=Sql::insert('vote_args',array($p['idPoll'],ses('uid'),$p['position'],$p['text']));
		return self::argumentPane($p);}
	
	static function argumentEdit($p){
		$ret=self::textarea();
		$ret.=aj('pllscnt'.$p['idPoll'].',,x|vote,argumentSave|idPoll='.$p['idPoll'].',position='.$p['position'].'|text',lang('save'),'btsav');
		return $ret;}
	
	static function argumentAdd($p){$ret='';
		$opts=array('1'=>lang('positive'),'2'=>lang('negative'));
		$ret=radio($opts,'position','1');
		$ret.=aj('popup|vote,argumentEdit|idPoll='.$p['idPoll'].'|position',lang('create'),'btsav');
		return $ret;}
	
	#pane
	static function argumentPane($p){//idPoll,idArg
		$idPoll=val($p,'idPoll'); $idArg=val($p,'idArg');
		if(!isset($idPoll) && $idArg)
			$idPoll=Sql::read('idPoll','vote_args','v','where id='.$idArg);
		$cols='position,name,txt,vote_args.up as date';
		$where='where vote_args.id='.$idArg;
		$r=Sql::read_inner($cols,'vote_args','login','uid','ra',$where);
		//header
		$rt=array('1'=>'argYes','2'=>'argNo');
		$cs=$r['position']==1?'argyes':'argno';
		$bt='#'.$idPoll;//pic('arrow-left').
		$back=aj('pllscnt'.$idPoll.'|vote,poll|idPoll='.$idPoll,$bt,'btn');
		if($r['position'])$here=aj('pllscnt'.$idPoll.'|vote,argumentPane|idPoll='.$idPoll.',idArg='.$idArg,'#'.$idArg.' '.langs('arg,'.$rt[$r['position']]),'btn '.$cs); else $here='';
		$by=self::userdate($r['date'],$r['name']);
		$bt=self::linktopoll($idPoll,$idArg);
		//admin
		if($r['name']==ses('user'))$bt.=aj('pllscnt'.$idPoll.'|vote,argumentDel|idArg='.$idArg.',idPoll='.$idPoll,lang('delete'),'btdel');
		$ret=$back.' '.$here.' '.$by.' '.span($bt,'right');
		//txt
		$ret.=div($r['txt'],'text');
		//vote
		$p['position']=$r['position'];
		$ret.=self::voteRead($p);
		//chat
		$n=Sql::read('count(id)','vote_chat','v','where idArg="'.$idArg.'"');
		$p='cht'.$idArg.'|vote,chatRead|idPoll='.$idPoll.',idArg='.$idArg;
		$ret.=aj($p,pic('comment').' '.lang('comments').' ('.$n.')','btn');
		if(isset($p['pagewidth']))$s='width:'.$p['pagewidth'].';'; else $s='';
		$ret=div($ret,'pane','',$s);
		$ret.=div('','sublock','cht'.$idArg);
		return $ret;}
	
	//read
	static function arguments($p){$ret='';//todo
		$r=Sql::read('id','vote_args','rv','where idPoll='.$p['idPoll'].' order by id');
		if($r)foreach($r as $k=>$v)//if(isset($rt[$v['position']])){
			$ret.=self::argumentPane($p+array('idArg'=>$v));
		return $ret;}
	
	/*---*/
	
	#edit
	static function pollUpdate($p){
		if($p['idPoll'])Sql::update('vote_lead','txt',$p['text'],$p['idPoll']);
		return self::poll($p);}
	static function pollEdit($p){
		$txt=Sql::read('txt','vote_lead','v','where id='.$p['idPoll']);
		$ret=self::textarea($txt);
		$ret.=aj('pllscnt'.$p['idPoll'].',,x|vote,pollUpdate|idPoll='.$p['idPoll'].'|text',lang('save'),'btsav');
		return $ret;}
	static function pollDel($p){
		if(!self::security('vote_lead',$p['idPoll']))return;
		if($p['idPoll'])Sql::update('vote_lead','closed','1',$p['idPoll']);//close
		//if($p['idPoll']){Sql::delete('vote_lead',$p['idPoll']); self::argumentDel($p);}
		return self::polls();}
	static function pollSave($p){
		if($p['text'])$nid=Sql::insert('vote_lead',array(ses('uid'),$p['text'],''));
		return self::poll(array('idPoll'=>$nid));}
	/*
	static function add_x(){
		return self::edit($com,$p,$v);
		$ret=self::textarea();
		$ret.=aj('pllscnt,,x|vote,pollSave||text',lang('add'),'btsav');
		return $ret;}*/
	
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
		return array('ts'=>$ts,'tt'=>$tt,'res'=>$ret,'global'=>round($ret*100,1),'score'=>$score);}
	
	static function argsResume($p){$ret=''; 
		//args by position [position]=>array([idArg])
		$ra=Sql::read('id,position','vote_args','kv','where idPoll='.$p['idPoll']); //pr($ra);
		//votes by args [idArg]=>array([position]=>cumul)
		$cols='vote_args.id,val';
		$r=Sql::read_inner($cols,'vote_valid','vote_args','idArg','kkc','where idPoll='.$p['idPoll'].' and (val=1 or val=2) order by idArg');
		if($ra)$res=self::algo($ra,$r); //pr($res);
		//$help=' '.hlpbt('vote_algo');
		$css=isset($res['global'])?'resyes':'resno';
		if(isset($res))$ret=span($res['global'].'%','score '.$css);
		if($ret)return div(lang('score').' '.$ret,'right');}
	
	#polls
	static function poll($p){
		$idPoll=val($p,'idPoll'); $rid=val($p,'rid');
		if(!$idPoll)return;
		$cols='name,txt,vote_lead.up as date';
		$where='where vote_lead.id='.$idPoll.' and closed=0';
		$r=Sql::read_inner($cols,'vote_lead','login','uid','ra',$where);
		if(!$r)return lang('not exists');
		//admin
		$go=aj('pllscnt'.$idPoll.'|vote,poll|idPoll='.$idPoll,'#'.$idPoll,'btn');
		$by=self::userdate($r['date'],$r['name']);
		$bt=self::linktopoll($idPoll,'');
		if($rid)$bt.=insertbt(lang('use'),$idPoll.':vote',$rid);
		if(ses('uid')){//add argument
			$bt.=bubble('vote,argumentAdd|idPoll='.$idPoll,langs('add,arg'),'btok',1);}
		if($r['name']==ses('user')){
			$bt.=aj('popup|vote,pollEdit|idPoll='.$idPoll,lang('modif'),'btn');
			$bt.=aj('pllscnt'.$idPoll.'|vote,pollDel|idPoll='.$idPoll,lang('del'),'btdel');}
		$bt=div($bt,'right');
		$txt=div(nl2br($r['txt']),'text');
		//args
		//if(isset($p['list'])){}
		$n=Sql::read('count(id)','vote_args','v','where idPoll="'.$idPoll.'"');
		$args=aj('arg'.$idPoll.'|vote,arguments|idPoll='.$idPoll,langs('args').' ('.$n.')','btn');
		//$args.=hlpbt('vote_args');
		//resume
		$resume=self::argsResume($p);
		//render
		$ret=div($go.' '.$by.$bt.br().$txt.$args.$resume,'pane');
		$ret.=div('','sublock','arg'.$idPoll);
		//if(!isset($p['list']))$ret.=self::arguments($p);
		return $ret;}
	
	static function polls($p){$ret=''; $rid=val($p,'rid');
		$r=Sql::read('id','vote_lead','rv','where closed!=1');
		if($r)foreach($r as $v)$ret.=self::poll(array('idPoll'=>$v,'list'=>1,'rid'=>$rid));
		return $ret;}
	
	#tlx
	static function call($p){$p['idPoll']=val($p,'id');
		if($p['idPoll'])return div(self::poll($p),'','pllscnt'.$p['idPoll']);}
		
	static function com($p){$rid=val($p,'rid');
		$id=val($p,'id'); $edt=val($p,'edt'); $mnu=val($p,'mnu');
		//if($id)$ret.=self::build($p);
		//elseif($mnu)$ret.=self::menu($p);
		return self::content($p);
		}
	
	#content
	static function content($p){$ret=''; $idPoll=val($p,'idPoll');
		//self::install(1);
		//$logbt=ses('user')?ses('user'):'login';
		//$ret.=span(Auth::logbt(),'right');
		//$ret=div(App::open('login'),'right');
		$ret.=aj('pllscnt'.$idPoll.'|vote,polls|rid='.val($p,'rid'),pic('list'),'btn').' ';
		$bt=pic('plus').' '.langs('new,referendum');
		if(ses('uid'))$ret.=aj('popup|vote,add|idPoll='.$idPoll,$bt,'btn').' ';
		$ret.=hlpbt('vote_app').' ';
		$ret.=br().br();
		//root
		if(isset($p['idArg']))$res=self::argumentPane($p);
		elseif(isset($idPoll))$res=self::poll($p);
		else $res=self::polls($p);
		$ret.=div($res,'','pllscnt'.$idPoll);
		return $ret;}
}

?>