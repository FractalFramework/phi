<?php

class pray{
static $private='1';

static function headers(){
	Head::add('csscode','
.minibtn{padding:4px; font-size:smaller;}
.minicon{display:block; height:52px; width:52px; background:#eee;}
.minicon.active {background:lightgreen; border:1px solid green;}
.minicon:hover{background:white;}
.minicon.active:hover {background:palegreen;}
.text {background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
');
}
static function voc(){
	return array();
}

//install
static function install(){
	Sql::create('pray_list',array('user'=>'var','text'=>'var'));
	Sql::create('pray_event',array('idPray'=>'int','user'=>'var','day'=>'date'));
	Sql::create('pray_group',array('user'=>'var','idEvent'=>'int'));
	Sql::create('pray_valid',array('idEvent'=>'int','user'=>'var','day'=>'date'));
}

/*---*/

#check
static function checkDay($prm){
	if($prm['status']==1){
		$id=Sql::read('id','pray_valid','v','where idEvent="'.$prm['idEv'].'" and user="'.$prm['user'].'" and day="'.$prm['day'].'"');
		Sql::delete('pray_valid',$id);}
	else Sql::insert('pray_valid',array($prm['idEv'],ses('user'),$prm['day']));
	return self::eventPane($prm);
}

#week
private static function week($prm){//idPray,idEv
	$user=ses('user'); $now=time(); $idEv=$prm['idEv'];
	$startDate=Sql::read('day','pray_event','v','where id='.$idEv);
	$rusr=Sql::read('id,user','pray_group','kv','where idEvent='.$idEv);
	$rvalid=Sql::read('user,day','pray_valid','kk','where idEvent="'.$idEv.'"');
	$firstDay=strtotime($startDate);
	for($i=1;$i<8;$i++)$rb[0][]=date('d/m',$firstDay+(86400*$i));//headers
	if($rusr)foreach($rusr as $k=>$v){$nb=8;
		for($i=1;$i<8;$i++){
			$currentTime=$firstDay+(86400*$i);
			$currentDate=date('Y-m-d',$currentTime);
			$ok=isset($rvalid[$v][$currentDate])?1:0;
			if($currentTime<=$now){$c=$ok?' active':'';
				$ico=$ok?pic('check'):pic('minus');
				if($v!=$user)$bt=tag('span','class=minicon opac'.$c,$ico);
				else $bt=aj('praycontent|pray,checkDay|idPray='.$prm['idPray'].',idEv='.$prm['idEv'].',user='.$v.',status='.$ok.',day='.$currentDate,$ico,'minicon'.$c);
			}
			else $bt=' ';
			$rb[$v][]=$bt;
		}
	}
	return Build::table($rb,'minibtn','',1);
}

/*---*/

#event
static function eventParticipation($prm){
	if($prm['subscribe']=='ok')
		Sql::insert('pray_group',array(ses('user'),$prm['idEv']));
	elseif($prm['subscribe']=='ko')
		Sql::delete('pray_group',$prm['uid']);
	return self::eventPane($prm);
}
static function eventDel($prm){
	if($prm['idEv']){Sql::delete('pray_event',$prm['idEv']);
		Sql::delete('pray_valid',$prm['idEv'],'idEvent');
		Sql::delete('pray_group',$prm['idEv'],'idEvent');}
	return self::announce($prm);
}
static function eventSave($prm){
	$date=date('Y-m-d',strtotime($prm['evDate']));
	if($prm['evDate'])Sql::insert('pray_event',array($prm['idPray'],ses('user'),$date));
	return self::announce($prm);
}
static function eventAdd($prm){
	$ret=input('evDate',date('Y-m-d',time()),8);
	$ret.=aj('div,praycontent,x|pray,eventSave|idPray='.$prm['idPray'].'|evDate','save','btn');
	return $ret;
}

#pane
static function eventPane($prm){//idPray,idEv
	if(!isset($prm['idPray']) && $prm['idEv'])
		$prm['idPray']=Sql::read('idPray','pray_event','v','where id='.$prm['idEv']);
	$r=Sql::read('day,user','pray_event','ry','where id='.$prm['idEv']);
	list($eventDate,$userEvent)=$r;
	$back=aj('praycontent|pray,announce|idPray='.$prm['idPray'],'#'.$prm['idPray'],'btn');
	$date=aj('praycontent|pray,eventPane|idPray='.$prm['idPray'].',idEv='.$prm['idEv'],$eventDate,'btn');
	$ret=$back.' '.$date.' '.lang('created by').' '.$userEvent;
	$bt=href('/app/pray/idPray='.$prm['idPray'].'/idEv='.$prm['idEv'],pic('link'));
	//register
	$user=ses('user');
	if(ses('user')){
		$uid=Sql::read('id','pray_group','v','where idEvent="'.$prm['idEv'].'" and user="'.$user.'"');
		if(!$uid)$bt.=aj('praycontent|pray,eventParticipation|idPray='.$prm['idPray'].',idEv='.$prm['idEv'].',subscribe=ok,uid='.$uid,'participer','btn');
		else $bt.=aj('praycontent|pray,eventParticipation|idPray='.$prm['idPray'].',idEv='.$prm['idEv'].',subscribe=ko,uid='.$uid,lang('unsubscribe'),'btn');
	}
	//admin
	if($userEvent==ses('user'))
		$bt.=aj('praycontent|pray,eventDel|idEv='.$prm['idEv'].',idPray='.$prm['idPray'],lang('remove'),'btn');
	$ret.=span($bt,'right');
	//calendar
	$ret.=self::week($prm);
	return div($ret,'pane');
}

/*---*/

#edit
static function announceUpdate($prm){
	if($prm['idPray'])Sql::update('pray_list','text',$prm['text'],$prm['idPray']);
	return self::announce($prm);
}
static function announceEdit($prm){
	$txt=Sql::read('text','pray_list','v','where id='.$prm['idPray']);
	$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
	$ret=tag('textarea',$r,'').br();
	$ret.=span('','small right','strcount');
	$ret.=aj('praycontent,xx|pray,announceUpdate|idPray='.$prm['idPray'].'|text','save','btn');
	return $ret;
}
static function announceDel($prm){
	if($prm['idPray']){Sql::delete('pray_list',$prm['idPray']);
		Sql::delete('pray_event',$prm['idPray'],'idPray');}
	return self::announces();
}
static function announceSave($prm){
	if($prm['text'])$nid=Sql::insert('pray_list',array(ses('user'),$prm['text']));
	return self::announce(array('idPray'=>$nid));
}
static function announceAdd(){
	$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
	$ret=tag('textarea',$r,'').br();
	$ret.=span('','small right','strcount');
	$ret.=aj('praycontent,,x|pray,announceSave||text','save','btn');
	return $ret;
}

private static function eventsList($id){$ret='';
	$r=Sql::read('id,day','pray_event','','where idPray='.$id);
	if($r)foreach($r as $v)
		$ret.=aj('praycontent|pray,eventPane|idPray='.$id.',idEv='.$v[0],$v[1],'btn');
	if($ret)return 'Sessions: '.$ret;
}

#announces
static function announce($prm){//idPray
	$r=Sql::read('user,text','pray_list','ra','where id='.$prm['idPray']);
	if(!$r)return 'Annonce inexistante';
	//bt
	$bt=href('/app/pray/idPray='.$prm['idPray'],pic('link'));
	if(ses('user')){
		$bt.=aj('bubble,,1|pray,eventAdd|idPray='.$prm['idPray'],lang('add event'),'btn');}
	if($r['user']==ses('user')){
		$bt.=aj('praycontent|pray,announceEdit|idPray='.$prm['idPray'],lang('modif'),'btn');
		$bt.=aj('praycontent|pray,announceDel|idPray='.$prm['idPray'],lang('delete'),'btn');}
	$bt=div($bt,'right');
	$txt=div(nl2br($r['text']),'text');
	//events
	$events=self::eventsList($prm['idPray']);
	$go=aj('praycontent|pray,announce|idPray='.$prm['idPray'],'#'.$prm['idPray'],'btn');
	return div($go.' '.lang('by').' '.$r['user'].$bt.br().$txt.$events,'pane').br();
}

static function announces(){$ret='';
	$r=Sql::read('id','pray_list','rv');
	if($r)foreach($r as $v)$ret.=self::announce(array('idPray'=>$v));
	return $ret;
}

/*---*/

#content
static function content($prm){$ret='';
	//self::install();
	//Lang::$app='pray';//context for new voc
	$logbt=ses('user')?ses('user'):'login';
	$ret.=aj('popup|login|auth=2',pic('user').' '.$logbt,'btn right');
	$ret.=aj('praycontent|pray,announces',pic('list'),'btn').' ';
	if(ses('uid'))$ret.=aj('praycontent|pray,announceAdd',pic('plus'),'btn');
	$ret.=hlpbt('pray');
	$ret.=br().br();
	//root
	if(isset($prm['idEv']))$res=self::eventPane($prm);
	elseif(isset($prm['idPray']))$res=self::announce($prm);
	else $res=self::announces();
	$ret.=tag('div',array('id'=>'praycontent'),$res);
	return $ret;
}
}

?>