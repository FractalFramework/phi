<?php
class pointer{
static $private=0;
static $a='pointer';
static $db='pointer';
static $cb='ddl';
static $cols=['tit'];
static $typs=['var'];
static $conn=0;
static $db2='pointer_cases';
static $db3='pointer_valid';
static $open=0;
static $qb='';

//first col,txt,answ,com(settings),code,day,clr,img,nb,cl,pub
//$db2 must use col "bid" <-linked to-> id

function __construct(){
$r=['a','db','cb','cols','db2','conn'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
sqlcreate(self::$db2,['bid'=>'int','col'=>'var'],1);
sqlcreate(self::$db3,['cid'=>'int','uid'=>'int'],1);
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
//$p['collect']=self::$db2;
//$p['help']=1;
$p['sub']=1;
//$p['execcode']=1;
return appx::edit($p);}

#sav
static function register($p){$id=val($p,'id'); $ret='';
$r=sql('id',self::$db2,'rv',['bid'=>$id]);
if($r)foreach($r as $k=>$v)$rs[]=[$v,ses('uid')]; //pr($rs);
if($r)sqlsav2(self::$db3,$rs);
return self::play($p);}

static function pointer($p){$bid=val($p,'bid');
$id=sql('id',self::$db3,'v',['cid'=>$bid,'uid'=>ses('uid')]);
if(!$id)sqlsav(self::$db3,[$p['bid'],ses('uid')]);
else sqldel(self::$db3,$id);
return self::play($p);}

#build
static function build($p){$id=val($p,'id');
//$r=sqlin('tit,'.self::$db2.'.id',self::$db,self::$db2,'bid','kr',$id,1);
$r=sql('tit',self::$db,'ra',$id); //pr($r);
return $r;}

static function usrbt($p){$usr=sql('name','login','v',$p['uid']);
return $bt=bubble('tlex,profile|usr='.$usr.',small=1',ico('user').$usr,'minicon',1);}

static function play($p){$id=val($p,'id');
$r=self::build($p); //pr($r);
$ex=sqlin('uid',self::$db2,self::$db3,'cid','v',['bid'=>$id,'uid'=>ses('uid')]);
$ra=sql('id,col',self::$db2,'kv',['bid'=>$id]); //pr($ra);
$rb=sqlin('col,uid,'.self::$db3.'.id,cid',self::$db2,self::$db3,'cid','rr',['bid'=>$id]); //pr($rb);
if($rb)foreach($rb as $k=>$v)$rc[$v['uid']][$v['cid']]=$v['id']; //pr($rc);
if($ra)foreach($ra as $k=>$v)$re['_k'][]=$v; array_unshift($re['_k'],'');//pr($re);
if($rc)foreach($rc as $k=>$v)if($k){//$re[$k][]=$k;
	foreach($ra as $ka=>$va){
		if(isset($v[$ka])){$c='active'; $bt=ico('pointer');} else{$c='disactive'; $bt=ico('close');}
		$re[$k][]=aj(self::$cb.$id.'|pointer,pointer|bid='.$ka.',id='.$id,$bt,'minicon '.$c);}
	if($re[$k])array_unshift($re[$k],self::usrbt(['uid'=>$k]));} //pr($re);
$ret=div($r['tit'],'tit');
$ret.=mktable($re);
if(!$ex)$ret.=aj(self::$cb.$id.'|pointer,register|id='.$id,langp('register'),'btsav');
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