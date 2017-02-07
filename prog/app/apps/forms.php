<?php

class forms{
	static $private='1';
	
	static function injectJs(){
		return '';}
		
	static function headers(){
		Head::add('csscode','
		.ftit{background:#aaa; color:#eaeaea; padding:4px 10px;}
		.ftxt{background:#eaeaea; color:#aaa; padding:4px 10px;}
		.fcell{margin:5px 0;}
		');
		Head::add('jscode',self::injectJs());}
		
	static function admin(){
		$r[]=array('','j','popup|forms,content','plus',lang('open'));
		return $r;}
		
	static function install(){
		Sql::create('forms_lead',array('fuid'=>'int','ftit'=>'var','ftxt'=>'var','fcom'=>'var','fcl'=>'int'),1);
		Sql::create('forms_vals',array('fid'=>'int','fvuid'=>'int','q1'=>'var','q2'=>'var','q3'=>'var','q4'=>'var','q5'=>'var','q6'=>'var','q7'=>'var','q8'=>'var','q9'=>'var'),1);}
	
	static function sav($p){$fid=val($p,'fid'); $rid=val($p,'rid'); $vrf='';
		$r=vals($p,['q1','q2','q3','q4','q5','q6','q7','q8','q9']);
		for($i=1;$i<=9;$i++)$vrf.=$r['q1'];
		if(!$vrf)return aj($rid.'|forms,read|fid='.$fid,langp('registration failed'),'alert');
		$nid=Sql::insert('forms_vals',[$fid,ses('uid'),$r['q1'],$r['q2'],$r['q3'],$r['q4'],$r['q5'],$r['q6'],$r['q7'],$r['q8'],$r['q9']]);
		return help('form_filled','valid');}
	
	static function play($r){
		$tmp='[[_label*class=cell:div][_inp*class=cell:div]*class=row:div]';
		return Vue::read_r($r,$tmp);}
	
	static function answers($p){$fid=val($p,'fid');
		$fcom=Sql::read('fcom','forms_lead','v','where id='.$fid);
		$rv=explode(',',$fcom); $n=count($rv); for($i=1;$i<=$n+1;$i++)$vr[]='q'.$i; $vars=implode(',',$vr);
		foreach($rv as $v)$rd[]=str_prm($v,':',1);
		$r=Sql::read('fvuid,'.$vars,'forms_vals','rr','where fid='.$fid);
		array_unshift($rd,''); array_unshift($r,$rd);
		return Build::table($r);}
	
	static function already($fid){
		return Sql::read('id','forms_vals','v','where fvuid='.ses('uid').' and fid='.$fid);}
		
	static function read($p){$fid=val($p,'fid'); $rid=val($p,'rid');
		if($fid)$r=Sql::read('id,ftit,ftxt,fcom,fcl,dateup','forms_lead','ra','where id='.$fid);
		if($r)$rb=Form::buildfromstring($r['fcom']);
		$ret=div($r['ftit'],'tit').div($r['ftxt'],'txt');
		if(self::already($fid))$ret.=help('form_filled','valid');
		else{$ret.=self::play($rb);
		$n=substr_count($r['fcom'],','); for($i=1;$i<=$n+1;$i++)$vr[]='q'.$i; $vars=implode(',',$vr);
		$ret.=div(aj('fcbk|forms,sav|fid='.$fid.',rid='.$rid.'|'.$vars,langp('send'),'btsav'));}//send
		return div($ret,'paneb','fcbk');}
	
	static function sav_lead($p){$fid=val($p,'fid');
		$r=vals($p,['ftit','ftxt','fcom']);
		$r['fcom']=str_replace("\n",'',$r['fcom']);
		if($fid)Sql::updates('forms_lead',$r,$fid);
		else $fid=Sql::insert('forms_lead',[ses('uid'),$r['ftit'],$r['ftxt'],$r['fcom'],'']);
		return self::edit_lead($p);}
	
	static function edit_form($p){$fcom=str_replace("\n",'',val($p,'fcom')); $fid=val($p,'id');
		if(!$fcom)$fcom='input:name,textarea:message,select:choice:a;b;c,checkbox:options:a;b,radio:choose one:a;b,bar:evaluation'; $fcom=str_replace(',',",\n",$fcom);
		$ret=textarea('fcom',$fcom,40,4,lang('fields'),'console').br();
		$ret.=div(aj('fscrpt|forms,edit_form|id='.$fid.'|fcom',langp('preview'),'btn')).br();//preview
		$r=Form::buildfromstring($fcom);
		$tmp='[[_label*class=cell:div][_inp*class=cell:div]*class=menu:div]';
		$ret.=div(self::play($r),'pane');
		return $ret;}
	
	static function edit_lead($p){$fid=val($p,'fid'); $rid=val($p,'rid'); $xid=val($p,'xid');
		if($fid)$r=Sql::read('id,ftit,ftxt,fcom,fcl,dateup','forms_lead','ra','where id='.$fid);
		else $r=vals($p,['id','ftit','ftxt','fcom','fcl','date']);
		$ret=aj($rid.'|forms,menu|fid='.$fid.',rid='.$rid.',xid='.$xid,langp('back'),'btn').br();//back
		//$ret.=tag('h4','',lang('edit form'));
		$ret.=input('ftit',val($r,'ftit'),28,lang('title')).br();
		$ret.=textarea('ftxt',val($r,'ftxt'),28,4,lang('presentation')).br();
		$ret.=aj($rid.'|forms,sav_lead|fid='.$fid.',rid='.$rid.',xid='.$xid.'|ftit,ftxt,fcom',lang('save'),'btsav');//save
		if($fid)if(self::already($fid))return $ret.br().br().help('form is not editable','alert');
		$ret.=tag('h4','',lang('edit fields').' '.hlpbt('forms_com'));
		$ret.=div(self::edit_form($r),'','fscrpt');
		return $ret;}
	
	static function menu($p){$fid=val($p,'fid'); $rid=val($p,'rid'); $xid=val($p,'xid'); $in='';
		$ret=aj($p['rid'].'|forms,edit_lead|rid='.$rid,langp('add'),'btsav');//add
		$r=Sql::read('id,ftit,ftxt,fcom,fcl,dateup','forms_lead','rr','where fuid='.ses('uid'));
		if($r)foreach($r as $k=>$v){
			$tit=aj('popup|forms,read|fid='.$v['id'],$v['ftit']);
			$edit=aj($rid.'|forms,edit_lead|fid='.$v['id'].',rid='.$rid.',xid='.$xid,pico('edit'),'btn');//edit
			$answ=aj('popup|forms,answers|fid='.$v['id'],langp('answers'),'btn');
			if($xid)$in=insertbt(lang('use'),$v['id'].':forms',$xid);
			//if($xid)$in=telex::publishbt($v['id'],'forms'); else $v['insert']='';
			$ret.=div($tit.br().$edit.$answ.$in,'menu');}
		return $ret;}
	
	//com
	static function tit($p){$id=val($p,'id');
		return Sql::read('ftit','forms_lead','v','where id='.$id);}
	
	static function call($p){$p['fid']=val($p,'id');
		//$ret=div(langp('forms'),'stit');$ret.
		return self::read($p);}
	
	static function com($p){$p['xid']=val($p,'rid');
		$p['rid']=randid('fr');
		$ret=self::menu($p);
		return div($ret,'',$p['rid']);}
	
	//interface
	static function content($p){
		//self::install();
		$p['rid']=randid('fr');
		$p['fid']=val($p,'param',val($p,'fid'));
		$ret=hlpbt('forms');
		$ret.=self::menu($p);
		return div($ret,'',$p['rid']);}
}
?>