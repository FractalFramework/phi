<?php

class poll{
	static $private='0';
	static $unit=3;
	static $length=86400;
	
	//install
	static function install(){
		Sql::create('poll_lead',array('uid'=>'int','txt'=>'var','answ'=>'var','closed'=>'int'),1);
		Sql::create('poll_valid',array('idPoll'=>'int','uid'=>'int','val'=>'int'),0);}

	static function headers(){
		Head::add('csscode','
	.anscnt{margin:4px 0; padding:2px; display:table-row;}
	.anscnt:hover{background:#ffffff;}
	.anstit{padding:2px 10px; display:table-cell; min-width:120px; max-width:340px;}
	.tensor{background:#cdcdcd; padding:2px; height:12px; border-radius:2px;}
	.anstens{padding:2px 10px; display:table-cell; width:320px;}
	.anscell{padding:2px 10px; display:table-cell;}
	.ansdiv{padding:2px 10px;}
	.tot{padding:2px 10px; background:#dcdcdc;}');}
	
	//generics
	static function userdate($ts,$name){
		$date=span(date('d/m/Y',$ts),'small');
		return $date.' '.small(lang('by').' '.$name).' ';}
	
	private static function security($table,$id){
		$uid=Sql::read('uid',$table,'v','where id='.$id);
		if($uid==ses('uid'))return 1;}
	
	static function textarea($v=''){
		$r=array('id'=>'text','style'=>'width:500px; height:100px;','onkeyup'=>'strcount(\'text\',255)');
		return tag('textarea',$r,$v).br().span('','right','strcnttext');}
	
	#create
	static function update($p){
		if($p['idPoll'])Sql::update('poll_lead','txt',$p['text'],$p['idPoll']);
		if(val($p,'mnu'))return self::com($p);
		return self::build($p);}
	
	static function modif($p){$id=val($p,'idPoll'); $mnu=val($p,'mnu');
		$txt=Sql::read('txt','poll_lead','v','where id='.$id);
		$ret=self::textarea($txt);
		$ret.=aj('pllscnt,,x|poll,update|mnu='.$mnu.',idPoll='.$id.'|text',lang('save'),'btsav');
		return div($ret,'pane');}
	
	static function del($p){$closed=val($p,'closed');
		if(!self::security('poll_lead',$p['idPoll']))return;
		if($p['idPoll'] && val($p,'del')){Sql::delete('poll_lead',$p['idPoll']);
			Sql::delete('poll_valid',$p['idPoll'],'idPoll');}
		elseif($p['idPoll'] && $closed==1){$p['closed']=0;//open
			Sql::update('poll_lead','closed','0',$p['idPoll']);}
		elseif($p['idPoll']){$p['closed']=1;//close
			Sql::update('poll_lead','closed','1',$p['idPoll']);}
		//if(val($p,'mnu'))return self::build($p);
		return self::build($p);}
	
	static function save($p){
		for($i=0;$i<10;$i++)if($v=val($p,'answ'.$i))$answ[]=$v;
		if(!isset($answ))return help('poll empty');
		$answers=implode('|',$answ);
		if($p['text'])$p['idPoll']=Sql::insert('poll_lead',array(ses('uid'),$p['text'],$answers,''));
		if(val($p,'mnu'))return self::com($p);
		if($p['idPoll'])return self::build($p);}
	
	#add
	static function create($p){$nb=val($p,'nb',2); $rid=val($p,'rid'); $mnu=val($p,'mnu');
		$inp[]='text'; for($i=1;$i<=$nb;$i++)$inp[]='answ'.$i; $inps=implode(',',$inp);
		$ret=span(aj('pllscnt,,x|poll,save|mnu='.$mnu.',rid='.$rid.'|'.$inps,lang('save'),'btsav'),'right');
		$ret.=div(lang('ask a question').' :','stit');
		$ret.=self::textarea(val($p,'text'));
		for($i=1;$i<=$nb;$i++)$ret.=div(input('answ'.$i,val($p,'answ'.$i),'',lang('choice').' '.$i));
		$ret.=aj('newpoll|poll,create|rid='.$rid.',nb='.($nb+1).'|'.$inps,langp('add choice'),'btn').br();
		return div($ret,'','newpoll');}
	
	static function leftime($end){$time=$end-ses('time');
		if($time>3600)$ret=floor($time/3600).'h ';
		elseif($time>60)$ret=floor($time/60).'min ';
		else $ret=$time.'s';
		return span($ret,'small');}
	
	#vote
	static function vote($p){$id=$p['idPoll'];
		$idVote=Sql::read('id','poll_valid','v','where idPoll="'.$id.'" and uid="'.ses('uid').'"');
		if(isset($idVote))$p['val']=$p['val']!=$p['current']?$p['val']:'0';
		if(!isset($idVote))
			$p['idVote']=Sql::insert('poll_valid',array($id,ses('uid'),$p['val']));
		else Sql::update('poll_valid','val',$p['val'],$idVote);
		return self::read($p);}
	
	static function pane($rb,$rs,$i,$sum,$closed,$vote,$com){$ret='';
		$answ=val($rb,$i);
		$score=val($rs,$i,0);
		$size=$sum&&$score?round($score/$sum*100):0;
		$css=$vote==$i?'active':'';
		$pic=$vote==$i?pic('square'):pic('square-o');
		$answer=$pic.' '.$answ;
		if(auth(6))$answer=aj($com.',val='.$i,$answer);//modif
		$tit=span($answer,'anstit');
		$tensor=span(div('','tensor','','width:'.($size).'%;'),'anstens');
		if($vote or $closed)$ret.=div($tit.$tensor.span($score,'anscell'),'anscnt');
		else $ret.=div(span(aj($com.',val='.$i,$pic.' '.$answ),'anstit'),'anscnt');
		return $ret;}
	
	static function read($p){$id=$p['idPoll']; $mnu=val($p,'mnu'); $ret='';
		$date=val($p,'date'); $answers=val($p,'answ'); if(!$date)list($answers,$date)=Sql::read('answ,UNIX_TIMESTAMP(up) as date','poll_lead','rw','where id='.$id);
		$vote=Sql::read('val','poll_valid','v','where idPoll="'.$id.'" and uid="'.ses('uid').'"');
		$rs=Sql::read('val','poll_valid','k','where idPoll="'.$id.'" order by val');//all votes
		$sum=array_sum($rs);
		$rb=explode('|',$answers); $nb=count($rb); array_unshift($rb,'null');
		$endtime=$date+self::$length;
		$leftime=ses('time')-$endtime;
		if($leftime>0)$closed=1; else $closed=0;
		//vote button
		$com='p'.$id.',,,1|poll,vote|mnu='.$mnu.',idPoll='.$id.',current='.$vote;
		//pane
		for($i=1;$i<=$nb;$i++)$ret.=self::pane($rb,$rs,$i,$sum,$closed,$vote,$com);
		//footer
		$ret.=span($sum.' '.plurial('vote',$sum,1),'tot');
		if($closed)$state=lang('poll closed').' '.lang('the',1).' '.date('d/m/Y',$endtime);
		else $state=lang('time left').' : '.self::leftime($endtime);
		$ret.=div($state,'grey');
		return div($ret,'','p'.$id);}
	
	#poll
	static function build($p){$bt=''; $id=$p['idPoll']; $mnu=val($p,'mnu'); $closed=val($p,'closed');
		$cols='name,txt,answ,UNIX_TIMESTAMP(poll_lead.up) as date';
		$where='where poll_lead.id='.$id.' order by poll_lead.id desc';
		$r=Sql::read_inner($cols,'poll_lead','login','uid','ra',$where);
		if(!$r)return lang('not exists').br();
		//admin
		if($mnu)$go=aj('pllscnt|poll,com|rid='.val($p,'rid'),'#'.$id,'btn');
		else $go=href('/app/poll/'.$id,pic('link').' '.$id,'btn').' ';
		if($mnu)$go.=insertbt(lang('use'),$id.':poll',val($p,'rid'));
		//if($mnu)$go.=telex::publishbt($id,'poll');
		$by=self::userdate($r['date'],$r['name']);
		if($r['name']==ses('user') && auth(4)){/**/
			$bt.=aj('pagup|poll,modif|mnu='.$mnu.',idPoll='.$id,lang('modif'),'btn');
			if(auth(6)){
			$prm='pol'.$id.'|poll,del|rid='.val($p,'rid').',mnu='.$mnu.',idPoll='.$id.',closed=';
			if(!$closed)$bt.=aj($prm.'0',lang('close'),'btdel');
			else{$bt.=aj($prm.'1',lang('open'),'btsav');
				$bt.=aj($prm.',del=1',lang('del'),'btdel');}}}
		$bt=div($bt,'right');
		$txt=div(nl2br($r['txt']),'text');
		//results
		$r['idPoll']=$id;
		$results=self::read($r);
		//render
		$ret=div($go.' '.$by.$bt.br().$txt.$results,'pane');
		return div($ret,'','pol'.$id);}
	
	#polls
	static function polls(){$ret='';
		$r=Sql::read('id,closed','poll_lead','kv','where uid="'.ses('uid').'" order by id desc');
		if($r)foreach($r as $k=>$v)$ret.=self::build(array('idPoll'=>$k,'closed'=>$v));
		return $ret;}
	
	#call
	static function tit($p){$id=val($p,'id');
		return Sql::read('txt','poll_lead','v','where id='.$id);}
	
	static function call($p){$id=val($p,'id');
		$r=Sql::read('txt,answ,UNIX_TIMESTAMP(up) as date','poll_lead','ra','where id='.$id);
		//$ret=div(langp('poll'),'stit');
		$ret=div(nl2br($r['txt']),'tit'); $r['idPoll']=$id;
		$ret.=self::read($r);
		return div($ret,'paneb');}
	
	static function menu($p){$id=val($p,'id'); $rid=val($p,'rid'); $ret='';
		$cols='id,UNIX_TIMESTAMP(up) as date,closed';
		$r=Sql::read($cols,'poll_lead','','where uid="'.ses('uid').'" order by id desc');
		if($r)foreach($r as $k=>$v){
			$p['idPoll']=$v[0]; $p['closed']=$v[2]; $date=date('d/m/Y',$v[1]);
			if(ses('time')-$v[1]>self::$length)$closed=1; else $closed=0;
			//if($v[2]==1)$closed=1; else $closed=0;
			$prms='mnu=1,rid='.$rid.',idPoll='.$v[0].',closed='.$v[2];
			$ret.=aj('pllscnt|poll,com|'.$prms,'#'.$v[0].' '.span($date,'date'),$closed?'':'active').' ';
			//$sav=telex::publishbt($v[0],'poll');
			//$ret.=div($bt.$sav,'menu');
			}
		return div($ret,'list');}

	static function com($p){$rid=val($p,'rid');
		$id=val($p,'idPoll'); $edt=val($p,'edt'); $mnu=val($p,'mnu'); $p['mnu']=$mnu;
		$bt=aj('pllscnt|poll,com|edt=1,rid='.$rid,langp('new'),'btsav').' ';
		$bt.=aj('pllscnt|poll,com|mnu=1,rid='.$rid,langp('menu'),'btn');
		$ret=div($bt);//div(help('vote'),'btit')..br()
		if($id)$ret.=self::build($p);
		elseif($mnu)$ret.=self::menu($p);
		elseif($edt)$ret.=div(self::create($p),'pane','');
		return div($ret,'','pllscnt');}
	
	#content
	static function content($p){$ret='';
		//self::install();
		if(isset($p['param']))$p['idPoll']=$p['param'];
		$ret=aj('pllscnt|poll,polls',pic('list'),'btn').' ';
		$ret.=hlpbt('poll_app').' ';
		if(ses('uid'))$ret.=aj('pagup|poll,create',pic('plus').' '.lang('new'),'btn').br().br();
		//root
		if(isset($p['idPoll']))$res=self::build($p);
		else $res=self::polls();
		$ret.=tag('div',array('id'=>'pllscnt'),$res);
		return $ret;}
}

?>