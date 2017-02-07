<?php

class slide{
	static $private='0';

	static function headers(){
		Head::add('csscode','.slide{background:black; color:white; align:center;
		display:flex; min-height:300px; width:100%; padding:100px; margin:10px 0 0 0;
		white-space:pre-wrap;}');}
	
	//install
	static function install(){
		Sql::create('slides',['pid'=>'int','bid'=>'int','tid'=>'int','txt'=>'text','rel'=>'int'],0);
		Sql::create('slides_menu',['tit'=>'text','uid'=>'int','pub'=>'int'],0);}
	
	//create new slide
	static function createsav($p){
		$rid=val($p,'rid'); $tit=val($p,'inpid');
		$p['tid']=Sql::insert('slides_menu',array('tit'=>$tit,'uid'=>ses('uid'),'pub'=>1));
		Sql::insert('slides',array('1','0',$p['tid'],lang('first slide'),'0'));
	return self::callin($p);}
	
	static function create($p){$rid=val($p,'rid');
		$ret=input('inpid','');
		$ret.=aj($rid.'|slide,createsav|rid='.$rid.'|inpid',langp('save'),'btn');
	return $ret;}
	
	static function editit($p){
		$p['table']='slides_menu';
		$p['cols']='tit,pub';
		$p['colslabels']='title,public';
		$p['act']='modif';
		$p['id']=val($p,'pid');
	return Edit::com($p);}
	
	//del
	static function del($p){$id=val($p,'tid'); $ok=val($p,'ok');
		if(!$ok)return aj(val($p,'rid').'|slide,del|'.prm($p).',ok=1',langp('del').': '.$id,'btdel').' ';
		elseif($id && auth(6))
			if(val($p,'delall')){Sql::delete('slides_menu',$id); Sql::delete('slides',$id,'tid');}
			else Sql::delete('slides',$id);
	if(val($p,'delall'))return self::menu($p);
	else return self::callin($p);}
	
	//add
	static function lastpid($tid){
		$r=Sql::read('pid','slides','rv','where tid='.$tid);
	if($r)return max($r)+1; else return 1;}
	
	static function addsav($p){ $rid=val($p,'rid');
		$cl=explode(';',val($p,'cols')); $tid=val($p,'tid',1); $pid=self::lastpid($tid);
		$r=Sql::columns('slides',1); $cols=implode(',',array_keys($r));
		foreach($r as $k=>$v){$inp=val($p,$k);
			//if($v=='int' && !is_numeric($inp))return '';
			if($k=='pid')$ret[$k]=$pid;
			elseif($k=='tid')$ret[$k]=$tid;
			else $ret[$k]=val($p,$k);}
		$nid=Sql::insert('slides',$ret);
	return aj($rid.'|slide,callin|tid='.$tid.',pid='.$pid.',rid='.$rid,lang('saved'),'btn');}
	
	static function add($p){$ret=''; $rid=val($p,'rid');
		$id=val($p,'id',1); $tid=val($p,'tid',1); $pid=val($p,'pid');
		$cols='bid,txt,rel'; $r=explode(',',$cols);
		if($r)foreach($r as $v){
			if($v=='bid')$va=$pid; else $va='';
			if($v=='txt')$ret.=div(textarea($v,'',40,4).' '.$v);
			else $ret.=div(input($v,$va).' '.$v);}
		$ret.=aj($rid.'sav,,x|slide,addsav|tid='.$tid.',pid='.$pid.',rid='.$rid.'|'.$cols,langp('save'),'btsav');
	return div($ret,'',$rid.'sav');}
	
	//edit
	static function edit($p){
		$p['table']='slides';
		$p['cols']='bid,txt,rel';
		$p['colslabels']='parent id,content,related id';
		$p['act']='modif';
		$p['id']=Sql::read('id','slides','v','where tid="'.val($p,'tid').'" and pid="'.val($p,'pid').'"');
		$ret=Edit::com($p);
	return $ret;}
	
	//nav
	static function nav($p){$ret='';
		$tid=val($p,'tid'); $pid=val($p,'pid',1); 
		$rid=val($p,'rid'); $prm='tid='.$tid.',rid='.$rid.',pid='.$pid;
		//$ret=input('inp',$pid).' ';
		//$ret=aj($rid.'|slide,callin|'.$prm,picxt(ics('slide'),$pid),'btn').' ';//refresh
		$ret.=aj('popup|slide,edit|'.$prm,langpi('edit'),'btn').' ';
		//if($pid==1)$ret.=aj('popup|slide,del|'.$prm.',delall=1',langpi('delete'),'btdel').' ';
		$ret.=aj('popup|slide,del|'.$prm,langpi('del'),'btdel').' ';
		$ret.=aj('popup|slide,add'.$prm.',bid=',langpi('add'),'btn').' ';
	return div($ret);}
	
	//motor
	static function slid($r,$p){
		$bt1=''; $bt2=''; $bt3=''; $bt4=''; $ret=''; $tit=val($p,'tit');
		$rid=val($p,'rid'); $tid=val($p,'tid'); $pid=val($p,'inp',val($p,'pid',1));
		$prm='rid='.$rid.',tid='.$tid.',pid='; $app='slide,callin'; $com=$rid;//.',,y'
		if($r)foreach($r as $k=>$v)if($v['pid']==$pid)$ra=$v;
		if(isset($ra)){
			$p['id']=$ra['id']; $p['pid']=$pid;
			if($ra['bid'])$bt1=aj($com.'|'.$app.'|'.$prm.$ra['bid'],langp('previous'),'btn').' ';
			elseif(val($p,'own')){$bt1=aj('popup|slide,editit|'.$prm.$tid,pico('edit'),'btn');
			$bt1.=aj($rid.'|slide,del|'.$prm.$tid.',delall=1',langpi('delete'),'btdel').' ';
			$bt1.=href('/app/slide/'.$tid,'#'.$tid,'btxt',1);}
			//else $bt1=span($tit,'btn');
			if($ra['rel'])$bt3=aj($com.'|'.$app.'|'.$prm.$ra['rel'],langp('begin'),'btn').' ';
			if($r)foreach($r as $ka=>$va){
				if($va['rel']==$pid)$bt2=aj($com.'|'.$app.'|'.$prm.$va['pid'],langp('end'),'btn').' ';
				if($va['bid']==$pid)$bt4.=aj($com.'|'.$app.'|'.$prm.$va['pid'],langpi('next').' '.$va['pid'],'btn').' ';}}
		$here=aj($rid.'|slide,callin|'.$prm.$pid,picxt(ics('slide'),$pid),'btn').' ';
		$bt=div($bt4,'right').div($here.$bt1.$bt2.$bt3);
		if(isset($ra))$ret=div(($ra['txt']),'','tx'.$rid,'margin:auto;');//nl2br->white-space:pre-wrap;
	return $bt.div($ret,'slide');}
	
	static function build($p){$tid=val($p,'tid'); $edt='';
		$r=Sql::read('id,pid,bid,txt,rel','slides','rr','where tid="'.$tid.'"');
		$p['tit']=Sql::read('tit','slides_menu','v','where id='.$tid);
		//if($p['own'])$edt=' '.aj('popup|slide,editit|id='.$tid.$tid,pico('edit'),'btn');
		$ret=div($p['tit'].$edt,'tit');
		return $ret.self::slid($r,$p);}
	
	static function menu($p){$rid=val($p,'rid'); $ret='';
		if(ses('uid'))$w='where uid="'.ses('uid').'"'; else $w='where pub=1';
		$r=Sql::read('id,tit','slides_menu','kv',$w);
		if($r)foreach($r as $k=>$v)
			$ret.=aj($rid.',,y|slide,callin|tid='.$k.',rid='.$rid,pico('slide').span($v),'bicon');
		if(auth(6))$ret.=aj($rid.',,y|slide,create|rid='.$rid,pic('plus').span(lang('new')),'bicon');
	return div($ret);}
	
	static function callin($p){$p['rid']=val($p,'rid',randid('sld')); $tid=val($p,'tid');
		$p['own']=Sql::read('id','slides_menu','v','where uid="'.ses('uid').'" and id="'.$tid.'"');
		if($p['own'])$nav=self::nav($p); else $nav='';
		return $nav.div(self::build($p),'',$p['rid']);}
	
	static function call($p){$p['rid']=val($p,'rid',randid('sld')); $p['tid']=val($p,'id'); $p['prw']='1';
		//$ret=div(langp('slide'),'stit');$ret.
		return div(self::build($p),'paneb',$p['rid']);}
	
	static function tit($p){$id=val($p,'id');
		return Sql::read('tit','slides_menu','v','where id='.$id);}
	
	static function com($p){$rid=randid('sld');
		$r=Sql::read('id,tit','slides_menu','kv','where uid="'.ses('uid').'"');
		$ret=aj('popup|slide,create|rid='.$rid,pic('plus').span(lang('new')),'licon');
		if($r)foreach($r as $k=>$v){
			$bt=insertbt(lang('use'),$k.':slide',val($p,'rid'));
			//$bt=telex::publishbt($k,'slide');
			$op=aj('pagup|slide,call|id='.$k,pico('slide').' '.span($v),'btn');
			$ret.=div($op.$bt,'licon');}
		return div($ret,'',$rid);}
	
	static function content($p){
		//self::install();
		$p['tid']=val($p,'param',val($p,'tid'));
		$p['rid']=randid('sld');
		if($p['tid'])$ret=self::nav($p).br().self::build($p);
		else $ret=self::menu($p);
		return div($ret,'',$p['rid']);}
}

?>
