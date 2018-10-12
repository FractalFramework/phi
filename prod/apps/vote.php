<?php

class vote{
static $private='1';
static $a='vote';
static $db='vote';
static $cb='vte';
static $cols=['txt','com','day'];
static $typs=['var','var','date'];
static $open=0;

function __construct(){
$r=['a','db','cb','cols'];
foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
appx::install(array_combine(self::$cols,self::$typs));
sqlcreate('vote_note',['bid'=>'int','uid'=>'int','choice'=>'int','val'=>'int'],1);}

static function admin($p){$p['o']='0';
return appx::admin($p);}

static function headers(){
	add_head('csscode','');}

#edit
static function form($p){
return appx::form($p);}

static function edit($p){
$p['collect']='vote_note';
return appx::edit($p);}

static function collect($p){
return appx::collect($p);}

#sav
static function modif($p){
return appx::modif($p);}

static function del($p){
$p['db2']='vote_note';
return appx::del($p);}

static function save($p){return appx::save($p);}
static function create($p){return appx::create($p);}

#note
static function votants($p){$id=$p['id'];
$r=sqljoin('distinct(uid),name','vote_note','login','uid','kv','where bid="'.$id.'"');
if($r)foreach($r as $k=>$v)$ret[]=profile::small(['usr'=>$v]);
return div(implode('',$ret));}

static function note($p){$id=$p['id'];
$w=['bid'=>$id,'uid'=>ses('uid'),'choice'=>$p['choice']];
$idnote=sql('id','vote_note','v',$w);
if(isset($idnote))$p['val']=$p['val']?$p['val']:'0';
if(!isset($idnote))
	$p['idnote']=sqlsav('vote_note',[$id,ses('uid'),$p['choice'],$p['val']]);
else sqlup('vote_note','val',$p['val'],$idnote);
return self::build($p);}

static function pane($rb,$closed,$note,$nb,$id){$ret='';
if(!ses('uid'))$com='popup|core,helpget|ref=loged_needed';
else $com='pb'.$id.',,,1|vote,note|id='.$id;//note button
for($i=1;$i<=$nb;$i++){$rt='';
	$answer=ico('square-o').' '.val($rb,$i);
	$noted=count($note)==$nb?1:0; //$noted=$closed;
	//if(auth(6))$noted=0;
	$notedcase=val($note,$i);
	for($k=1;$k<=5;$k++){
		$ico=$k<=$notedcase?ico('star'):ico('star-o');
		if($closed)$rt.=span($ico);
		else $rt.=aj($com.',choice='.$i.',val='.$k,$ico);}
	$ret[]=[$answer,$rt];}
return mktable($ret);}

//results
static function algo($r){$re=''; $rd=''; $rank='';
foreach($r as $k=>$v){$tot=0;//collect scores
	$sum=array_sum($v);//nb notes
	for($i=5;$i>0;$i--){$ratio=0;
		if(isset($v[$i]))$ratio=round($v[$i]/$sum,2); $tot+=$ratio;
		$rc[$k][$i]=$ratio;//by candidat
		if(!isset($rd[$k]) && $tot>0.5)$rd[$k]=$i;}}//majoritary mention
arsort($rd);//pr($rd);
for($i=5;$i>0;$i--){$n=0;//rank
	if($rd)foreach($rd as $k=>$v)if($v==$i)$re[$i][]=$k;
	if($re)foreach($re as $k=>$v){rsort($v);//todo: find differences between ex-aequo
		foreach($v as $vb){$n++; $rank[$n]=$vb;}}}
//reorder
if($rank)foreach($rank as $k=>$v)$ret[$v]=$rc[$v];//assign rank
return $ret;}

static function play_scores($id,$nb){$ret='';
$rq=qr('select choice,val from vote_note where bid="'.$id.'"');
while($r=mysqli_fetch_row($rq))
	$ret[$r[0]][$r[1]]=isset($ret[$r[0]][$r[1]])?$ret[$r[0]][$r[1]]+=1:1;
//need minimum 1 note by choice
//foreach($ret as $k=>$v)for($i=1;$i<5;$i++)if(!isset($v[$i]))$ret[$k][$i]=1;
return $ret;}

static function pane_results($rb,$id,$nb_noters){
$ret=''; $n=0; $winner=''; $sz=200;//max width
$r=self::play_scores($id,$nb_noters);
if($r)$rc=self::algo($r);
//render
$rt[0][0]=lang('candidate');
$rt[0][1]=lang('rank'); $rt[0][2]='';
for($i=1;$i<=5;$i++)//headers
	$rt[0][2].=span(str_pad('',$i,'*'),'ansprop score'.$i,'','width:'.($sz/5+16).'px;');
	$rt[0][2]=div($rt[0][2],'pllcnt');
if(isset($rc))foreach($rc as $k=>$v){
	$stot=0; $sum=0; $n++; if(!$winner)$winner=$k;
	$rt[$k][0]=val($rb,$k);
	$rt[$k][1]=$n; $rt[$k][2]='';
	for($i=1;$i<=5;$i++){$score=$v[$i]; $nb=isset($r[$k][$i])?$r[$k][$i]:0;
		$rt[$k][2].=span($nb,'ansprop score'.$i,'','width:'.($score*$sz+16).'px;');}
		$rt[$k][2]=div($rt[$k][2],'pllcnt');}
$ret=mktable($rt,'','1');
//winner
if($winner){
	$answ=sql('com',self::$db,'v',$id);
	$rw=explode('|',$answ); $win=$rw[$winner-1];
	$ret.=div(lang('the winner is').' : '.$win,'valid');}
return $ret;}

#read
static function cancel($p){
qr('delete from vote_note where bid="'.$p['id'].'" and uid="'.ses('uid').'"');
return self::build($p);}

static function build($p){$id=$p['id']; $ret='';
list($answers,$end)=sql('com,day',self::$db,'rw',$id);
if(ses('uid'))$note=sql('choice,val','vote_note','kv','where bid="'.$id.'" and uid="'.ses('uid').'"'); else $note=[];
$rs=sql('distinct(uid)','vote_note','k','where bid="'.$id.'" order by val');
$rb=explode('|',$answers); $nb=count($rb); array_unshift($rb,'null'); $sum=array_sum($rs);
$endtime=strtotime($end);
$leftime=ses('time')-$endtime;
if($leftime>0)$closed=1; else $closed=0;
if(val($p,'adm'))$closed=1; //if(auth(6))$closed=1;//
//pane
if($closed)$ret=self::pane_results($rb,$id,$sum);
else $ret=self::pane($rb,$closed,$note,$nb,$id);
//footer
$ret.=br().aj('popup|vote,votants|id='.$id,$sum.' '.langs('noter',$sum,1),'tot').' ';
if($closed)$state=lang('vote closed').' '.lang('the',1).' '.$end;
else $state=lang('time left').' : '.build::leftime($endtime);
$ret.=span($state,'grey');
if(ses('uid') && $note && !$closed)$ret.=aj('pb'.$id.'|vote,cancel|id='.$id,lang('cancel'),'btdel');
if(!$closed)$ret.=aj('pb'.$id.'|vote,build|headers=1,adm=1,id='.$id,lang('see'),'btn');
return div($ret,'','pb'.$id);}

#stream
static function play($p){$bt=''; $go='';
$id=$p['id']; $rid=val($p,'rid'); $tid=randid('txt');
$cols=self::$db.'.id,name,txt,com,day';
$where='where '.self::$db.'.id='.$id.' order by '.self::$db.'.id desc';
$r=sqljoin($cols,self::$db,'login','uid','ra',$where);
if(!$r)return help('id not exists','board');
//admin
if($rid){$go=aj('blcbk|vote,stream|rid='.$rid,'#'.$id,'btn');
	$go.=insertbt(lang('use'),$id.':vote',$rid);}
$go.=lk('/vote/'.$id,ico('link'),'btn').' ';
$by=small($r['day'].' '.lang('by').' '.$r['name']).' ';
//$bt=div($bt,'right');
$txt=div(nl2br(voc($r['txt'],self::$db.'-txt-'.$id)),'txt',$tid);
//results
$results=self::build($p);
//render
$ret=div($txt.$results,'');//$go.' '.$by.$bt.br().
return div($ret,'','pol'.$id);}

static function stream($p){
return appx::stream($p);}

#interface
static function tit($p){
return appx::tit($p);}

static function call($p){
return appx::call($p);}

static function com($p){
return appx::com($p);}

#content
static function content($p){
//self::install();
return appx::content($p);}
}

?>