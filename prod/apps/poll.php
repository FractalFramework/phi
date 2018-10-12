<?php

class poll{
static $private='1';
static $a='poll';
static $db='poll';
static $cb='pll';
static $cols=['txt','com','day'];
static $typs=['var','var','date'];
static $open=1;
static $qb='db';

function __construct(){
	$r=['a','db','cb','cols','qb'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	sqlcreate('poll_valid',['bid'=>'int','uid'=>'int','val'=>'int'],1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function headers(){
	add_head('csscode','');}

#editor
static function del($p){$p['db2']='poll_valid'; return appx::del($p);}
static function modif($p){return appx::modif($p);}
static function save($p){return appx::save($p);}
static function form($p){return appx::form($p);}
static function create($p){return appx::create($p);}
static function edit($p){$p['collect']='poll_valid'; return appx::edit($p);}
static function collect($p){return appx::collect($p);}

#vote
static function vote($p){$id=$p['id'];
	$idVote=sql('id','poll_valid','v','where bid="'.$id.'" and uid="'.ses('uid').'"');
	if(isset($idVote))$p['val']=$p['val']!=$p['current']?$p['val']:'0';
	if(!isset($idVote))
		$p['idVote']=sqlsav('poll_valid',array($id,ses('uid'),$p['val']));
	else sqlup('poll_valid','val',$p['val'],$idVote);
	return self::play($p);}

static function pane($rb,$rs,$i,$sum,$closed,$vote,$com){$ret='';
	$answ=val($rb,$i);
	$score=val($rs,$i,0);
	$css=$vote==$i?'active':'';
	$pic=$vote==$i?ico('square'):ico('square-o');
	$answer=$pic.' '.$answ;
	if(!$closed)$answer=aj($com.',val='.$i,$answer);//modif
	$tit=span($answer,'anstit');
	$size=$sum&&$score?round($score/$sum*100):0;
	$score=span($size.' %','anscell small');
	$tensor=span(div('','tensor','','width:'.($size).'%;'),'anstens');
	if($closed or $vote)$ret.=div($score.$tit.$tensor,'anscnt');
	else $ret.=div(span(aj($com.',val='.$i,$pic.' '.$answ),'anstit'),'anscnt');
	return $ret;}

static function build($p){$id=val($p,'id');
	return sql('txt,com,day',self::$db,'rw',$id);}

static function play($p){$id=$p['id']; $ret='';
	$r=self::build($p); if(!$r)return;
	list($txt,$answers,$end)=$r;
	$ret=div(nl2br($txt),'tit');
	$vote=sql('val','poll_valid','v','where bid="'.$id.'" and uid="'.ses('uid').'"');
	$rs=sql('val','poll_valid','kad','where bid="'.$id.'" order by val');//all votes
	$sum=array_sum($rs); $rb=explode('|',$answers);
	$nb=count($rb); array_unshift($rb,'null');
	$endtime=strtotime($end);
	$leftime=ses('time')-$endtime;
	if($leftime>0)$closed=1; else $closed=0;
	if(val($p,'adm'))$closed=1;
	//vote buttons
	$com=self::$cb.$id.',,,1|poll,vote|id='.$id.',current='.$vote;
	for($i=1;$i<=$nb;$i++)$ret.=self::pane($rb,$rs,$i,$sum,$closed,$vote,$com);
	//footer
	$foot=span($sum.' '.langs('vote',$sum,1),'nfo').' ';
	if($closed)$state=lang('poll closed');
	else $state=lang('time left').' : '.build::leftime($endtime);
	$foot.=span($state,'grey');
	return div($ret,'txt').div($foot);}

#call
static function tit($p){
	return appx::tit($p);}

static function stream($p){
	return appx::stream($p);}

static function call($p){
	return appx::call($p);}

//com (edit)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}

?>