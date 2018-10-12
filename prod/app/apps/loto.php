<?php

class loto{
static $private='0';
static $a='loto';
static $db='loto';
static $cb='lto';
static $cols=['tit','nb','result','day'];
static $typs=['var','int','int','date'];
static $db2='loto_vals';
static $db3='loto_win';
static $open=1;
static $price=10;
static $ty='red';

function __construct(){
	$r=['a','db','db2','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));
	sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','val'=>'int','bet'=>'int'],1);
	sqlcreate(self::$db3,['bid'=>'int','uid'=>'int','price'=>'int'],1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return 'function barlabel(v,id){var d="";
	var r=["","broken","bad","works","good","new","","",""];
	inn(r[v],id);}';}
static function headers(){
	add_head('csscode','');
	add_head('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){$p['db2']=self::$db2; return appx::del($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}

static function fc_nb($k,$v){return hidden($k,$v).div($v,'inp opac');}
static function fc_result($k,$v){return hidden($k,$v).div($v,'inp opac');}
static function form($p){$p['fcresult']=1; //$p['barfunc']='barlabel'; 
	if(val($p,'id'))$p['fcnb']=1; return appx::form($p);}

static function create($p){return appx::create($p);}
static function edit($p){$p['collect']=self::$db2; return appx::edit($p);}

#build
static function draw($r,$id){
	for($i=0;$i<$r['nb'];$i++)$rv[]=rand(1,9); $val=implode('',$rv);
	sqlup(self::$db,'result',$val,$id);
	return $val;}

static function participate($p){
	$id=val($p,'id'); $val=val($p,'val'.$id); $lbl=$id.':'.self::$a; $p['label']=$lbl;
	if(strpos($val,'0')!==false)return self::play($p);
	$uid=sql('uid',self::$db,'v',$id);//payer
	$ok=bank::payment(['value'=>self::$price,'type'=>self::$ty,'at'=>$uid,'label'=>$lbl]);
	if(!is_numeric($ok))return $ok;//error
	else sqlsav(self::$db2,['bid'=>$id,'uid'=>ses('uid'),'val'=>$val,'bet'=>self::$price]);//'bk'=>$ok
	return self::play($p);}

//game
static function balls($v){$ret='';
	$d='10101 10102 10103 10104 10105 10106 10107 10108 10109 10110 10111';
	//$d='10111 10112 10113 10114 10115 10116 10117 10118 10119 10120 10121';//1-10
	//$d='65295 65296 65297 65298 65299 65300 65301 65302 65303 65304 65305';
	$ra=explode(' ',$d); $r=str_split($v);
	foreach($r as $v)$ret.=ascii($ra[$v]);
	return $ret;}

static function numbers($id,$n,$v){$ret='';
	for($i=1;$i<10;$i++){$c=$v==$i?' btok':'';
		$j='loto'.$id.'|'.self::$a.',game|id='.$id.',n='.$n.',v='.$i.'|val'.$id;
		$ret.=aj($j,ascii(10101+$i),'btsav'.$c);}
	return div($ret);}

static function game($p){$ret='';
	$id=val($p,'id'); $n=val($p,'n',0); $v=val($p,'v',0); $a=self::$a; $cb=self::$cb;
	$nb=sql('nb',self::$db,'v',$id);
	$val=val($p,'val'.$id,str_pad('',$nb,'0'));
	$rv=str_split($val); if($v)$rv[$n]=$v; $val=implode('',$rv);
	for($i=0;$i<$nb;$i++)$ret.=self::numbers($id,$i,$rv[$i]);
	$bt=langp('play it').' '.bank::coin(self::$price,self::$ty);
	$ret.=aj('lto'.$id.'|'.$a.',participate|id='.$id.'|val'.$id.'',$bt,'btsav');
	$ret.=hidden('val'.$id,$val);
	return $ret;}

//results
static function paywin($p){$id=val($p,'id'); $price=val($p,'price');
	$uid=sql('uid',self::$db,'v',$id);//payer
	$ex=sqlsav(self::$db3,['bid'=>$id,'uid'=>ses('uid'),'price'=>$price]);
	$rb=['uid'=>$uid,'label'=>$id.':'.self::$a,'value'=>$price,'type'=>self::$ty,'at'=>ses('uid')];
	$ok=bank::payment($rb);}

static function results($r,$ex,$id){$val='';
	if($ex)$val=sql('val',self::$db2,'v',$ex);//choice
	$players=sql('count(id)',self::$db2,'v',['bid'=>$id]);
	$winners=sqljoin('name',self::$db2,'login','uid','rv',['bid'=>$id,'val'=>$r['result']]);
	$ret=div(lang('winning number').' '.self::balls($r['result']),'nfo');
	if($val)$ret.=div(lang('you played').' '.self::balls($val),'nfo');
	if($winners){$nb=count($winners); $cagnote=ceil(self::$price*$players/$nb);
		$bt=$nb.' '.langs('player',$nb,1).' '.langs('won',$nb,1).' ';
		$bt.=bank::coin($cagnote).' ('.implode(', ',$winners).')';
		$ret.=div($bt,'valid');}
	else $ret.=div(lang('no winner'),'alert');
	if($val && $val==$r['result']){
		$payed=sql('id',self::$db3,'v',['bid'=>$id,'uid'=>ses('uid')]);
		if(!$payed)self::paywin(['id'=>$id,'price'=>$cagnote]);
		$ret.=div(lang('you win').' '.bank::coin($cagnote),'valid');}
	elseif($val)$ret.=div(lang('you loose'),'alert');
	return $ret;}

#play
static function build($p){return appx::build($p);}

static function play($p){
	$id=val($p,'id');
	$r=self::build($p);
	$ret=div($r['tit'],'tit');
	$maxday=$r['day']; $end=strtotime($maxday); 
	if($end>ses('time'))$current=1; else $current=0;
	if(!$current && !$r['result'])$r['result']=self::draw($r,$id);
	$ex=sql('id',self::$db2,'v',['bid'=>$id,'uid'=>ses('uid')]);
	if(!$ex && $current)$bt=self::game($p);
	elseif($ex && $current)$bt=div(lang('thank for playing'),'valid');
	elseif(!$current)$bt=self::results($r,$ex,$id);
	$ret.=div($bt,'','loto'.$id);
	if($current)$ret.=div(lang('time left',1).' : '.build::leftime($end),'nfo');
	else $ret.=div(lang('loto finished').' '.$maxday,'nfo');
	return $ret;}

static function stream($p){return appx::stream($p);}

#call (read)
static function tit($p){return appx::tit($p);}

static function call($p){
	return div(self::play($p),'',self::$cb.$p['id']);}

#com (edit)
static function com($p){return appx::com($p);}

#interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>