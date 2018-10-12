<?php
class pkr{
static $private=0;
static $a='pkr';
static $db='pkr';
static $cb='pkr';
static $cols=['tit'];
static $typs=['var'];
static $conn=0;
static $db2='pkr_vals';
static $open=0;
static $qb='';//db
static $clr=['p'=>'peak','c'=>'clover','h'=>'heart','d'=>'diam'];//db
static $crd=[1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8,8=>9,9=>10,'a'=>'J','b'=>'Q','c'=>'K','d'=>'A'];

function __construct(){
$r=['a','db','cb','cols','db2','conn'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
sqlcreate(self::$db2,['bid'=>'int','cards'=>'var'],1);//'uid'=>'int',
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; return appx::admin($p);}
static function titles($p){return appx::titles($p);}
static function injectJs(){return;}
static function headers(){
add_head('csscode','.card,.card2{border:1px solid black; padding:2px; margin:2px; size:20px;}
.card{color:black;} .card2{color:red;}
.card .philum{color:black;} .card2 .philum{color:red;}');
add_head('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){
$p['db2']=self::$db2;
return appx::del($p);}

static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function create($p){return appx::create($p);}

//subform
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}
static function subedit_form($r){return appx::subedit_form($r);}

//form
static function form($p){
return appx::form($p);}

static function edit($p){
$p['collect']=self::$db2;
return appx::edit($p);}

static function add($p){
sqlsav(self::$db2,[$p['id'],$p['cdr']]);
return self::play($p);}

static function addone($p){
$d=sql('cards',self::$db2,'v',$p['id']);
sqlup(self::$db2,$d.$p['cdr'],$p['id']);
return self::play($p);}

static function dellast($p){
$d=sql('cards',self::$db2,'v',$p['id']);
sqlup(self::$db2,substr($d,0,2),$p['id']);
return self::play($p);}

#build
static function card($n,$c){$ret='';
if($n)$ret.=self::$crd[$n];
if($c)$ret.=picto(self::$clr[$c]);
$s=$c=='h'||$c=='d'?'card2':'card';
return span($ret,$s);}

static function compose($p){$ret='';
$id=val($p,'id'); $cdn=val($p,'cdn'); $cdr=val($p,'cdr');
foreach(self::$crd as $k=>$v)
	$ret.=aj('curcard|pkr,compose|id='.$id.',cdn='.$k.',cdr='.$cdr,$v,'card');
foreach(self::$clr as $k=>$v){
	$c=$k=='h'||$k=='d'?'card2':'card';
	$ret.=aj('curcard|pkr,compose|id='.$id.',cdn='.$cdn.',cdr='.$k,picto($v),$c);}
$ret.=' :: '.self::card($cdn,$cdr);
$ret.=aj(self::$cb.'|pkr,addone|id='.$id.',cdn='.$cdn.',cdr='.$cdr,picto('add'),'btsav');
$ret.=aj(self::$cb.'|pkr,dellast|id='.$id,picto('del'),'btdel');
$ret.=aj(self::$cb.'|pkr,add|id='.$id.',cdn='.$cdn.',cdr='.$cdr,picto('add'),'btsav');
return $ret;}

static function display($v){$ret=''; $a=strlen($v);
for($i=0;$i<$a;$i+=2){$n=substr($v,$i,1); $c=substr($v,$i+1,1);
	$ret.=$n.picto(self::$clr[$c]);}
return $ret;}

static function build($p){
//$t=sql('tit',self::$db,'v',$p['id']);
$r=sql('cards',self::$db2,'rv',['bid'=>$p['id']]); //pr($r);
//if($r)foreach($r as $k=>$v)$r[$k]=self::display($v);
return $r;}

static function usrform($r,$id){$ret='';
if($r)foreach($r as $k=>$v)$ret.=div(self::display($v),'');
//$ret.=input('crd','').aj(self::$cb.'|pkr,add|id='.$id,pic('save'),'btnsav');
$ret.=div(self::compose(['id'=>$id]),'','curcard');
return $ret;}

static function play($p){
$r=self::build($p); //pr($r);
$ret=self::usrform($r,$p['id']);
return $ret;}

static function stream($p){
//$p['t']=self::$cols[0];
return appx::stream($p);}

#call (read)
static function tit($p){
//$p['t']=self::$cols[0];
return appx::tit($p);}

static function call($p){
return div(self::play($p),'',self::$cb);}

#com (edit)
static function com($p){return appx::com($p);}
static function uid($id){return appx::uid($id);}
static function own($id){return appx::own($id);}

#interface
static function content($p){
self::install();//
return appx::content($p);}

static function api($p){
return appx::api($p);}
}
?>