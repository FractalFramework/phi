<?php

class petition{
	static $private='1';
	
	static function injectJs(){
		return '';}
		
	static function headers(){
		Head::add('csscode','');
		Head::add('jscode',self::injectJs());}
		
	static function admin(){
		$r[]=array('','j','popup|petition,content','plus',lang('open'));
		return $r;}
		
	static function install(){
		Sql::create('petition_lead',array('puid'=>'int','ptit'=>'var','ptxt'=>'var','pcl'=>'int'),1);
		Sql::create('petition_vals',array('pid'=>'int','pvuid'=>'int'),1);}
	
	static function sav($p){$pid=val($p,'pid'); $rid=val($p,'rid'); $vrf='';
		$nid=Sql::insert('petition_vals',[$pid,ses('uid')]);
		return help('petition_filled','valid');}
	
	static function answers($p){$pid=val($p,'pid');
		$r=Sql::read_inner('name,dateup','petition_vals','login','pvuid','rr','where pid='.$pid);
		if($r)$ret=div(count($r).' '.lang('signatures'),'valid');
		if($r)array_unshift($r,[lang('user'),lang('date')]);
		return $ret.Build::table($r);}
	
	static function already($pid){
		return Sql::read('id','petition_vals','v','where pvuid='.ses('uid').' and pid='.$pid);}
		
	static function read($p){$pid=val($p,'pid'); $rid=val($p,'rid'); $nb='';
		if($pid){$r=Sql::read('id,ptit,ptxt,pcl,dateup','petition_lead','ra','where id='.$pid);
			$n=Sql::read('count(id)','petition_vals','v','where pid='.$pid);}
		$ret=div($r['ptit'],'tit').div($r['ptxt'],'txt');
		if($n)$nb=' '.span($n.' '.plurial('signature',$n),'btok');
		if(self::already($pid))$ret.=help('petition_filled','valid').$nb;
		else $ret.=div(aj('fcbk|petition,sav|pid='.$pid.',rid='.$rid,langp('sign'),'btsav').$nb);//send
		return div($ret,'paneb','fcbk');}
	
	static function sav_lead($p){$pid=val($p,'pid');
		$r=vals($p,['ptit','ptxt']);
		if($pid)Sql::updates('petition_lead',$r,$pid);
		else $pid=Sql::insert('petition_lead',[ses('uid'),$r['ptit'],$r['ptxt'],'']);
		return self::menu($p);}
	
	static function edit_lead($p){$pid=val($p,'pid'); $rid=val($p,'rid'); $xid=val($p,'xid');
		if($pid)$r=Sql::read('id,ptit,ptxt,pcl,dateup','petition_lead','ra','where id='.$pid);
		else $r=vals($p,['id','ptit','ptxt','pcl','date']);
		$ret=aj($rid.'|petition,menu|pid='.$pid.',rid='.$rid.',xid='.$xid,langp('back'),'btn').br();//back
		$ret.=input('ptit',val($r,'ptit'),28,lang('title')).br();
		$ret.=textarea('ptxt',val($r,'ptxt'),28,4,lang('description')).br();
		$ret.=aj($rid.'|petition,sav_lead|pid='.$pid.',rid='.$rid.'|ptit,ptxt',lang('save'),'btsav');
		return $ret;}
	
	static function menu($p){$pid=val($p,'pid'); $rid=val($p,'rid'); $xid=val($p,'xid'); $in='';
		$ret=aj($p['rid'].'|petition,edit_lead|rid='.$rid,langp('add'),'btsav');//add
		$r=Sql::read('id,ptit,ptxt,pcl,dateup','petition_lead','rr','where puid='.ses('uid'));
		$tmp='[[[_date*class=date:span] _ptit _bt _insert _answ*class=tit:div][_ptxt*class=txt:div]*class=menu:div]';
		if($r)foreach($r as $k=>$v){
			$tit=aj('popup|petition,read|pid='.$v['id'],$v['ptit']);
			$bt=aj($rid.'|petition,edit_lead|pid='.$v['id'].',rid='.$rid.',xid='.$xid,pico('edit'));//edit
			if($xid)$in=insertbt(lang('use'),$v['id'].':petition',$xid); else $v['insert']='';
			//if($xid)$in=telex::publishbt($v['id'],'petition'); else $v['insert']='';
			$answ=aj('popup|petition,answers|pid='.$v['id'],langp('answers'),'btn');
			$ret.=div($tit.br().$bt.$answ.$in,'menu');}
		return $ret;}
	
	//call
	static function tit($p){$id=val($p,'id');
		return Sql::read('ptit','petition_lead','v','where id='.$id);}
	
	static function call($p){$p['pid']=val($p,'id');
		//$ret=div(langp('petition'),'stit');$ret.
		return self::read($p);}
	
	//com
	static function com($p){$p['xid']=val($p,'rid');
		$p['rid']=randid('fr');
		$ret=self::menu($p);
		return div($ret,'',$p['rid']);}
	
	//interface
	static function content($p){
		self::install();
		$p['rid']=randid('fr');
		$p['pid']=val($p,'param',val($p,'pid'));
		$ret=hlpbt('petition');
		$ret.=self::menu($p);
		return div($ret,'',$p['rid']);}
}
?>
