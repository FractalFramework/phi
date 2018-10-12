<?php

class freevote{
static $private='1';
static $a='freevote';
static $db='freevote';
static $cb='dcwrp';
static $cols=['txt','cl'];
static $typs=['var','int'];
static $db2='freevote_args';
static $open=1;

function __construct(){
	$r=['a','db','cb','cols','db2'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	sqlcreate('freevote_args',['bid'=>'int','uid'=>'int','txb'=>'var'],1);
	sqlcreate('freevote_valid',['cid'=>'int','uid'=>'int','val'=>'int'],1);}

static function admin($p){$p['o']='1'; return appx::admin($p);}
static function headers(){add_head('csscode','');}

#sys
static function del($p){return appx::del($p);}
static function modif($p){return appx::modif($p);}
static function save($p){return appx::save($p);}
static function create($p){return appx::create($p);}

#editor
static function form($p){return appx::form($p);}
static function edit($p){return appx::edit($p);}

//generics
static function userdate($date,$name){
	$date=span(date('Y-m-d',strtotime($date)),'small');
	return span($date,'date').' '.small(lang('by').' '.$name);}

private static function security($table,$id){
	$uid=sql('uid',$table,'v',$id);
	if($uid==ses('uid'))return 1;}

//algo
static function algo($r){$ret=[];
	$nb_props=count($r); $tot_votes=0; $rt=[]; $rs=[]; $rq=[]; $rw=[];
	foreach($r as $k=>$rb){$nb_votes=count($rb); $tot_votes+=$nb_votes; $agree=0; $disagree=0;
		foreach($rb as $kb=>$vb){
			$agree+=$vb==1?1:0; $disagree+=$vb==2?1:0;}
		$rt[$k]=$agree; $rs[$k]=$disagree; $rq[$k]=$tot_votes; $rw[$k]=round(($agree-$disagree)/$tot_votes,2);}
	asort($rt); asort($rs); asort($rq); asort($rw); //pr($rt);
	//score by mass
	$i=0; if($rt)foreach($rt as $k=>$v){$i++; $rt_score[$k]=$i;} //pr($rt_score);
	//score by ratio
	$i=0; if($rw)foreach($rw as $k=>$v){$i++; $rw_score[$k]=$i;} //pr($rw_score);
	if($rt_score)foreach($rt_score as $k=>$v)$score[$k]=$v+$rw_score[$k]; //pr($score);
	if($score)arsort($score); //pr($score);
	if($score)foreach($score as $k=>$v)$ret[$k]=[$v,$rs[$k],$rq[$k],$rw[$k]*100]; //pr($ret);
	//if($ret)$ret=array_slice($ret,0,5); //pr($ret); /kill keys
	return ['nb'=>$nb_props,'tot'=>$tot_votes,'res'=>$ret];}

static function argsResume($p){$id=$p['id']; $ret='';
	$ra=sql('id','freevote_args','rv','where bid='.$id); //pr($ra);
	if($ra)foreach($ra as $k=>$v)
		$rb[$v]=sql('val','freevote_valid','rv','where val>0 and cid='.$v); //pr($rb);
	if($rb)$res=self::algo($rb); //pr($res);
	//$help=' '.hlpbt('freevote_algo');
	if(isset($res['res']))foreach($res['res'] as $k=>$v){$p['bid']=$k; $p['score']=$v[3];
		$ret.=self::argumentPane($p);}
	$css=isset($res['global'])?'resyes':'resno';
	if(isset($res))$bt=div($res['nb'].' '.lang('propositions').' - '.$res['tot'].' '.lang('votes'),'nfo');
	if($ret)return div($bt.' '.$ret);}

#vote
static function lead_save($p){
	$id=sql('id','freevote_valid','v','where cid="'.$p['bid'].'" and uid="'.ses('uid').'"');
	if(!$id)sqlsav('freevote_valid',[$p['bid'],ses('uid'),$p['val']]);
	elseif($id){$v=$p['val']==$p['current']?0:$p['val'];
		sqlup('freevote_valid','val',$v,$id);}
	return self::vote($p);}

static function vote($p){
	$bid=val($p,'bid'); $kid=randid('freevote'); $cl=val($p,'cl'); $nb=val($p,'nbvotes');
	$vote=sql('val','freevote_valid','v','where cid="'.$bid.'" and uid="'.ses('uid').'"');
	$yes=sql('count(val)','freevote_valid','v','where cid="'.$bid.'" and val="1"');
	$no=sql('count(val)','freevote_valid','v','where cid="'.$bid.'" and val="2"');
	$pb=$kid.'|freevote,lead_save|bid='.$bid.',current='.$vote;
	//vote buttons
	$cs1=$vote==1?' active':''; $cs2=$vote==2?' active':'';
	if(ses('uid') && !$cl){
		$bt1=langpi('agree',1).' '.small($yes);
		$bt2=langpi('disagree',1).' '.small($no);
		$ret=aj($pb.',val=1',$bt1,'btn'.$cs1);
		$ret.=aj($pb.',val=2',$bt2,'btn'.$cs2);}
	else $ret=span('yes:'.$yes.' no:'.$no.' diff:'.($yes-$no),'btn');
	return span($ret,'',$kid);}

#argument
static function argumentDel($p){$bid=$p['bid'];
	if(!self::security('freevote_args',$bid))return;
	if(!val($p,'ok'))return aj('vt'.$p['id'].'|freevote,argumentDel|ok=1,'.prm($p),langp('really?'),'btdel');//.aj('vt'.$p['id'].'|freevote,arguments|'.prm($p),langp('cancel'),'btn')
	sqldel('freevote_args',$bid);
	sqldel('freevote_valid',$bid,'cid');
	return self::play($p);}

static function argumentSave($p){
	$r=[$p['id'],ses('uid'),$p['addarg']];//p($r);
	$p['cid']=sqlsav('freevote_args',$r);
	return self::play($p);}

static function argumentedit($p){$id=$p['id'];
	$ret=input('addarg',lang('add proposition'),'52',1);
	$ret.=aj('vt'.$id.'|freevote,argumentSave|id='.$id.'|addarg',langp('save'),'btsav');
	return $ret;}

static function argumentPane($p){$ret=''; $bt='';
	$id=val($p,'id'); $bid=val($p,'bid'); $cl=val($p,'cl');
	if(!isset($id) && $bid)$id=sql('bid','freevote_args','v',$bid);
	$cols='name,txb,freevote_args.up as date,count(freevote_valid.id) as nb';
	$w='left join freevote_valid on freevote_valid.cid=freevote_args.id where freevote_args.id='.$bid;
	$r=sqljoin($cols,'freevote_args','login','uid','ra',$w.' order by nb desc');
	//$bt=self::userdate($r['date'],$r['name']);
	if($r['name']==ses('user') && !$cl)
		$bt=bubble('freevote,argumentDel|bid='.$bid.',id='.$id,pic('delete'),'btdel');
	//$by=small(lang('by').' '.$r['name']).' ';
	$by=bubble('tlex,profile|usr='.$r['name'].',small=1',$r['name'],'grey small',1);
	$ret.=div($by.' - '.$r['txb'],'argpane');//txb
	if(!$cl)$ret.=div(self::vote($p),'argvote');//vote
	else $ret.=span($p['score'].'%','score '.($p['score']>0?'resyes':'resno'));
	$ret.=div($bt,'argvote');//user
	return div($ret,'');}

//read
static function arguments($p){$id=$p['id']; $ret='';//todo
	$r=sql('id','freevote_args','rv','where bid='.$id.' order by id');
	if($r)foreach($r as $k=>$v){$p['bid']=$v;
		$ret.=self::argumentPane($p);}
	return $ret;}

#stream
static function play($p){
	$id=val($p,'id'); $p['cid']=val($p,'o'); $add=''; if(!$id)return;
	$cols='name,txt,cl,'.self::$db.'.up as date';
	$where='where '.self::$db.'.id='.$id;
	$r=sqljoin($cols,self::$db,'login','uid','ra',$where);
	$p['cl']=$r['cl'];
	$txt=div(nl2br($r['txt']),'txt');
	if($r['cl']==1)return $txt.self::argsResume($p).help('form closed','alert');
	//admin
	$by=self::userdate($r['date'],$r['name']);
	//$by=bubble('tlex,profile|usr='.$r['name'].',small=1',$r['name'],'grey small',1);
	//$bt=div(lk('/freevote/'.$id,ico('link'),'btn'),'right');
	//args
	$n=sql('count(id)','freevote_args','v','where bid="'.$id.'"');
	$p['nbvotes']=$n;
	//$args.=hlpbt('freevote_args');
	if(ses('uid') && !$r['cl'])//add argument
		$add=aj('arg'.$id.'|freevote,argumentedit|id='.$id,langp('add proposition'),'btok');
	//render
	$ret=div($by.$txt,'');//$bt.br().
	$ret.=div($n.' '.lang('propositions',1),'nfo');
	$ret.=div(self::arguments($p).$add,'','arg'.$id);
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
	return div(self::play($p),'pane','vt'.$p['id']);}

//com (edit)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}

?>