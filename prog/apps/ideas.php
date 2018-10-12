<?php

class ideas{
static $private='0';
static $a='ideas';
static $db='ideas';
static $cb='dea';
static $cols=['txt','cl'];
static $typs=['var','int'];
static $db2='ideas_args';
static $db3='ideas_valid';
static $open=0;

function __construct(){
$r=['a','db','cb','cols','db2'];
foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
appx::install(array_combine(self::$cols,self::$typs));
sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','txb'=>'var'],1);
sqlcreate(self::$db3,['cid'=>'int','uid'=>'int','val'=>'int'],1);}

static function admin($p){$p['o']='1'; return appx::admin($p);}
static function headers(){add_head('csscode','');}

#sys
static function del($p){return appx::del($p);}//$p['db2']=self::$db2;
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

#argument
static function argumentDel($p){$bid=$p['bid']; $rid=self::$cb.$p['id'];
if(!self::security(self::$db2,$bid))return;
if(!val($p,'ok'))return aj($rid.'|ideas,argumentDel|ok=1,'.prm($p),langp('really?'),'btdel');
//.aj($rid.'|ideas,arguments|'.prm($p),langp('cancel'),'btn')
sqldel(self::$db2,$bid);
sqldel(self::$db3,$bid,'cid');
return self::play($p);}

static function argumentSave($p){
$r=[$p['id'],ses('uid'),$p['addarg']];//p($r);
echo $p['cid']=sqlsav(self::$db2,$r);
return self::play($p);}

static function argumentedit($p){$id=$p['id'];
$ret=input('addarg',lang('add proposition'),'52',1);
$ret.=aj(self::$cb.$id.'|ideas,argumentSave|id='.$id.'|addarg',langp('save'),'btsav');
return $ret;}

//algo
static function algo($r){
foreach($r as $k=>$rb)$ret[$k]=count($rb);
//arsort($ret);//pr($ret);
return $ret;}

//poll
static function lead_save($p){
$id=sql('id',self::$db3,'v','where uid='.ses('uid').' and cid='.$p['bid']);
if(!$id)sqlsav(self::$db3,[$p['bid'],ses('uid'),1]);
else sqlup(self::$db3,'val',$p['v']==1?0:1,$id);
return self::play($p);}

static function barlevel($p){//p($p);
$sum=val($p,'tot'); $score=val($p,'score'); $cl=val($p,'cl');
$bid=val($p,'bid'); $vot=val($p,'voted'); $t=val($p,'txt'); $bt=val($p,'bt'); $btv=val($p,'btvot');
$size=$sum&&$score?round($score/$sum*100):0;
$bar=div('','bartensor '.($vot?'active':''),'','width:'.$size.'%;";');
$bar.=div($t,'bartxt');
$bar.=div($size.'% '.$btv,'barscore');
$ret=div($bar,'barwrap');
$ret.=div($bt,'barbt');
return div($ret,'barline');}

#proposition
static function proposition($p){$ret=''; $bt=''; $vot='';//p($p);
$id=val($p,'id'); $bid=val($p,'bid'); $cl=val($p,'cl');
//if(!isset($id) && $bid)$id=sql('bid',self::$db2,'v',$bid);
$cols='name,txb'; $w='where ideas_args.id='.$bid;//ideas_args.up as date
$ra=sqlin($cols,'login',self::$db2,'uid','ra',$w); //pr($ra);
//$cols='count(ideas_valid.id) as nb'; $w='where val=1 and cid='.$bid;
//$nb=sql($cols,self::$db3,'v',$w.''); //pr($rb);
if(ses('uid'))$vot=sql('val',self::$db3,'v','where uid='.ses('uid').' and cid='.$bid);
$p['voted']=$vot;
if($ra['name']==ses('user') && !$cl)
	$p['bt']=bubble('ideas,argumentDel|bid='.$bid.',id='.$id,pic('delete'),'');
//if(!$cl)$p['score']=$nb;
$c=$vot?'active':''; $rid=self::$cb.$id;
if(!$cl && ses('uid'))$p['btvot']=aj($rid.'|ideas,lead_save|id='.$id.',bid='.$bid.',v='.$vot,pic('accept'),$c);//5*
//$usr=self::userdate($ra['date'],$ra['name']);
//$usr=small(lang('by').' '.$ra['name']).' ';
$usr=bubble('tlex,profile|usr='.$ra['name'].',small=1',$ra['name'],'grey small',1);
static $i; $i++;
$by=$usr.' #'.$i;
$txt=nl2br(voc($ra['txb'],'ideas_arg-txb-'.$bid));
$p['txt']=$by.' - '.$txt;
$ret=self::barlevel($p);//pr($p);
return $ret;}

#play
static function play($p){$db=self::$db;
$id=val($p,'id'); $p['cid']=val($p,'o'); $content=''; $add=''; if(!$id)return;
$cols='name,txt,cl,'.$db.'.up as date';
$r=sqljoin($cols,$db,'login','uid','ra','where '.$db.'.id='.$id); //pr($r);
$p['cl']=$r['cl']; //$r=['name','txt','cl','date]
//$by=self::userdate($r['date'],$r['name']);
$by=bubble('tlex,profile|usr='.$r['name'].',small=1',$r['name'],'grey small',1);
$txt=nl2br($r['txt']);
//entries
$ra=sql('id',self::$db2,'rv','where bid='.$id); //pr($ra);
if($ra)$p['nbargs']=count($ra); //else return;
if($ra)foreach($ra as $k=>$v)
	$rb[$v]=sql('id,val',self::$db3,'kv','where val>0 and cid='.$v); //pr($rb);
if(isset($rb))$rc=self::algo($rb); //pr($rc);
if(isset($rc)){$p['tot']=array_sum($rc); arsort($rc);} //pr($rc); //by ranks //if($r['cl'])
//render
if(isset($rc))foreach($rc as $k=>$v){$p['bid']=$k; $p['score']=$v;
	$content.=self::proposition($p);}
if(ses('uid') && !$r['cl'])//add argument
	$add=div(div(self::argumentedit($p),'barwrap','addarg'.$id),'barline');
elseif(!ses('uid'))$add=help('need auth 1','alert');
else $add=help('form closed','alert');
//$bt=div(lk('/ideas/'.$id,ico('link'),'btn'),'right');
//$args.=hlpbt(self::$db2);
$ret=div(voc($txt,$db.'-txt-'.$id),'txt');//$by.$bt.br().
if($ra)$ret.=div($p['nbargs'].' '.lang('propositions',1).' - '.$p['tot'].' '.lang('votes',1),'nfo');
$ret.=div($content.$add,'barlevels');
return $ret;}

#stream
static function stream($p){
$p['privacy']='0';
return appx::stream($p);}

#interfaces
static function tit($p){
$p['t']='txt';
return appx::tit($p);}

//call (read)
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