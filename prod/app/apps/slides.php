<?php

class slides{
	static $private='0';

	static function headers(){
		Head::add('csscode','.slide{background:black; color:white; align:center;
		display:flex; min-height:300px; width:100%; padding:100px; margin:30px 0 0 0;}');
	}
	
	//install
	static function install(){
		$r=array('pid'=>'int','bid'=>'int','tid'=>'int','txt'=>'text','rel'=>'int');
		Sql::create('slides',$r,1);
		$r=array('tit'=>'text','uid'=>'int','pub'=>'int'); Sql::create('slides_menu',$r,0);
	}
	
	//create new slide
	static function createsav($p){
		$rid=val($p,'rid'); $tit=val($p,'inpid');
		$ret=array('tit'=>$tit,'uid'=>ses('uid'),'pub'=>1);
		$p['tid']=Sql::insert('slides_menu',$ret);
	return self::build($p);}
	
	static function create($p){$rid=val($p,'rid');
		$ret=input('inpid','');
		$ret.=ajax($rid,'slides,createsav','rid='.$rid,'inpid',picoxt('save'),'btn');
	return $ret;}
	
	static function editit($p){
		$p['table']='slides_menu';
		$p['cols']='tit,pub';
		$p['id']=val($p,'tid');
		$p['act']='modif';
	return Edit::com($p);}
	
	//del
	static function del($p){$id=val($p,'id'); $ok=val($p,'ok');
		if(!$ok)return ajax('popup','slides,del',prm($p).',ok=1','',picoxt('del').': '.$id,'btdel').' ';
		elseif($id && auth(6))Sql::delete('slides',$id);
	return 'deleted: '.$id;}
	
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
	return ajax($rid,'slides,build','tid='.$tid.',pid='.$pid.',rid='.$rid,'',lang('saved'),'btn');}
	
	static function add($p){$ret=''; $rid=val($p,'rid');
		$id=val($p,'id',1); $tid=val($p,'tid',1); $pid=val($p,'pid');
		$cols='bid,txt,rel'; $r=explode(',',$cols);
		if($r)foreach($r as $v){
			if($v=='bid')$va=$pid; else $va='';
			if($v=='txt')$ret.=div(textarea($v,'',40,4).' '.$v);
			else $ret.=div(input($v,$va).' '.$v);}
		$ret.=ajax($rid.'sav','slides,addsav','tid='.$tid.',pid='.$pid.',rid='.$rid,$cols,picoxt('save'),'btsav');
	return div($ret,'',$rid.'sav');}
	
	//edit
	static function edit($p){
		$p['table']='slides';
		$p['cols']='bid,txt,rel';
		$p['act']='modif';
		$ret=Edit::com($p);	
	return $ret;}
	
	//nav
	static function nav($p){
		$tid=val($p,'tid'); $pid=val($p,'pid',1); 
		$com=val($p,'rid'); $prm=prm($p);
		$ret=input('inp',$pid).' ';
		$ret.=ajax($com,'slides,build',$prm,'inp',picoxt('go'),'btn').' ';
		if(auth(6)){
			$ret.=ajax('popup','slides,edit',$prm,'',picoxt('edit'),'btn').' ';
			$ret.=ajax('popup','slides,del',$prm,'',picoxt('del'),'btdel').' ';
			$ret.=ajax('popup','slides,add',$prm,'',picoxt('add'),'btn').' ';}
	return div($ret);}
	
	//motor
	static function slide($r,$p){
		$bt1=''; $bt2=''; $bt3=''; $bt4=''; $ret='';
		$rid=val($p,'rid'); $tid=val($p,'tid'); $pid=val($p,'inp',val($p,'pid',1));
		$prm='rid='.$rid.',tid='.$tid.',pid='; $com=$rid.',,y'; $app='slides,build';
		if($r)foreach($r as $k=>$v)if($v['pid']==$pid)$ra=$v;
		if(isset($ra)){
			$p['id']=$ra['id']; $p['pid']=$pid;
			if($ra['bid'])$bt1=ajax($com,$app,$prm.$ra['bid'],'',picoxt('previous'),'btn').' ';
			elseif(auth(6)){$btx=Sql::read('tit','slides_menu','v','where id='.$tid);
				$bt1=ajax('popup','slides,editit',$prm.'','',$btx,'btn');}
			if($ra['rel'])$bt3=ajax($com,$app,$prm.$ra['rel'],'',picoxt('begin'),'btn').' ';
			if($r)foreach($r as $ka=>$va){
				if($va['rel']==$pid)$bt2=ajax($com,$app,$prm.$va['pid'],'',picoxt('end'),'btn').' ';
				if($va['bid']==$pid)$bt4.=ajax($com,$app,$prm.$va['pid'],'',picoxt('next').' ('.$va['pid'].')','btn').' ';}}
		$bt=self::nav($p).br().div($bt4,'right').div($bt1.$bt2.$bt3);
		if(isset($ra))$ret=div($pid.') '.nl2br($ra['txt']),'','','margin:auto;');
	return $bt.div($ret,'slide');}
	
	static function build($p){$tid=val($p,'tid');
		$r=Sql::read('id,pid,bid,txt,rel','slides','rr','where tid="'.$tid.'"');
		return self::slide($r,$p);}
	
	static function menu($p){$rid=val($p,'rid'); $ret='';
		if(ses('uid'))$w='where uid="'.ses('uid').'"'; else $w='where pub=1';
		$r=Sql::read('id,tit','slides_menu','kv',$w);
		if($r)foreach($r as $k=>$v)
			$ret.=ajax($rid.',,y','slides,build','tid='.$k.',rid='.$rid,'',pic('window-maximize').span($v),'bicon');
		if(auth(6))$ret.=ajax($rid.',,y','slides,create','rid='.$rid,'',pic('plus').span(lang('new')),'bicon');
	return div($ret);}
	
	static function content($p){
		//self::install();
		$p['tid']=val($p,'param',val($p,'tid'));
		$p['rid']=randid('sld');
		if($p['tid'])$ret=self::build($p);
		else $ret=self::menu($p);
		return div($ret,'',$p['rid']);
	}
}

?>
