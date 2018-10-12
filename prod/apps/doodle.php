<?php
class doodle{
static $private=1;
static $a='doodle';
static $db='doodle';
static $cb='ddl';
static $cols=['tit','date','nbdays'];
static $typs=['var','date','int'];
static $conn=0;
static $db2='doodle_valid';
static $open=0;
static $qb='';

//first col,txt,answ,com(settings),code,day,clr,img,nb,cl,pub
//$db2 must use col "bid" <-linked to-> id

function __construct(){
$r=['a','db','cb','cols','db2','conn'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
sqlcreate(self::$db2,['bid'=>'int','uid'=>'int','day'=>'int','ok'=>'int'],1);
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; return appx::admin($p);}
static function titles($p){return appx::titles($p);}
static function injectJs(){return;}
static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){$p['db2']=self::$db2; return appx::del($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function create($p){
//$p['pub']=0;//default privacy
return appx::create($p);}

//subform
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}
static function subedit_form($r){return appx::subedit_form($r);}

//form
//static function fc_tit($k,$v){}
static function form($p){
//$p['html']='txt';
//$p['fctit']=1;
//$p['barfunc']='barlabel';
return appx::form($p);}

static function edit($p){
$p['collect']=self::$db2;
//$p['help']=1;
//$p['sub']=1;
//$p['execcode']=1;
return appx::edit($p);}

#sav
static function register($p){$id=val($p,'id');
$day=sql('date_format(date,"%y%m%d")',self::$db,'v',['id'=>$id]);
sqlsav(self::$db2,[$id,ses('uid'),$day,0]);
return self::play($p);}

static function remove($p){$id=val($p,'id'); $rid=self::$cb.$id;
if(!val($p,'ok')){
	$ret=aj($rid.'|doodle,remove|ok=1,id='.$id,lang('confirm deleting'),'btdel');
	$ret.=aj($rid.'|doodle,play|id='.$id,lang('cancel'),'btn');}
else{sqldel(self::$db2,['bid'=>$id,'uid'=>ses('uid')]); $ret=self::play($p);}
return $ret;}

static function check($p){$bid=val($p,'id');
$id=sql('id',self::$db2,'v',['bid'=>$bid,'uid'=>ses('uid'),'day'=>val($p,'day')]);
if(!$id)sqlsav(self::$db2,[$bid,ses('uid'),val($p,'day'),1]);
else sqlup(self::$db2,'ok',val($p,'go'),$id);
return self::play($p);}

#build
static function build($p){$id=val($p,'id');
$r=sql('tit,date,nbdays',self::$db,'ra',$id); //pr($r);
return $r;}

static function usrbt($p){$usr=sql('name','login','v',$p['uid']);//ico('user').
return $bt=bubble('tlex,profile|usr='.$usr.',small=1',$usr,'minicon',1);}

static function play($p){$id=val($p,'id'); $uid=ses('uid');
$ra=self::build($p); $cb=self::$cb;//pr($ra);
$ex=sql('uid',self::$db2,'v',['bid'=>$id,'uid'=>ses('uid')]);
$rb=sql('uid,day,ok',self::$db2,'kkv',['bid'=>$id]); //pr($rb);
$start=strtotime($ra['date']); $n=$ra['nbdays'];
setlng();//setlocale
//echo date('ymd',$start);
for($i=0;$i<$n;$i++)$re['_k'][]=(strftime('%a %e %b',$start+86400*$i));//utf8_encode
array_unshift($re['_k'],''); //pr($re);
if($rb)foreach($rb as $k=>$v){//$re[$k][]=$k;
	for($i=0;$i<$n;$i++){
		$day=date('ymd',($start+86400*$i)); //echo $day.' ';
		if(isset($v[$day])){
			if($v[$day]==1){$c='active'; $bt=ico('check'); $go=2;}
			elseif($v[$day]==2){$c='disactive'; $bt=ico('close'); $go=0;}
			else{$c=''; $bt=ico('minus'); $go=1;}}
		else{$c=''; $bt=ico('minus'); $go=1;}
		if($k==$uid)$re[$k][]=aj($cb.$id.'|doodle,check|id='.$id.',day='.$day.',go='.$go,$bt,'minicon '.$c);
		else $re[$k][]=span($bt,'minicon '.$c);}
	array_unshift($re[$k],self::usrbt(['uid'=>$k]));} //pr($re);
$ret=div($ra['tit'],'txt');
$ret.=mktable($re);
if(!$ex)$ret.=aj(self::$cb.$id.'|doodle,register|id='.$id,langp('register'),'btsav');
else $ret.=aj(self::$cb.$id.'|doodle,remove|id='.$id,langp('remove'),'btdel');
return $ret;}

static function stream($p){
//$p['t']=self::$cols[0];
return appx::stream($p);}

#call (read)
static function tit($p){
//$p['t']=self::$cols[0];
return appx::tit($p);}

static function call($p){
return appx::call($p);}

#com (edit)
static function com($p){return appx::com($p);}
static function uid($id){return appx::uid($id);}
static function own($id){return appx::own($id);}

#interface
static function content($p){
//self::install();
return appx::content($p);}

static function api($p){
return appx::api($p);}
}
?>