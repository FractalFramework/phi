<?php

class artwork{
static $private='0';
static $a='artwork';
static $db='artwork';
static $cb='bok';
static $cols=['tit'];
static $db2='artwork_arts';
static $conn=0;

function __construct(){
	$r=['a','db','cb','cols','db2'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(['tit'=>'var']);
	sqlcreate(self::$db2,['bid'=>'int','conn'=>'var'],1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function injectJs(){
	$d='function format(p,o){document.execCommand(p,false,o?o:null);}';
	return ;}

static function headers(){
	add_head('csscode','');
	add_head('jscode',self::injectJs());}

static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}

//edit
static function del($p){
	$p['db2']=self::$db2;
	return appx::del($p);}

//save
static function form($p){return appx::form($p);}
static function create($p){return appx::create($p);}

//subform
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}

static function dskslct($p){$a=self::$a; $cb=self::$cb; $dr=val($p,'dir'); $bck=before($dr,'/');
$r=sql('type,com,picto,bt','desktop','','where uid="'.ses('uid').'" and dir like "/documents%" order by dir asc'); $ret='';
if($r)foreach($r as $k=>$v){list($typ,$com,$ic,$bt)=$v;
	$app=before($com,','); $aid=after($com,'=');
	if($typ=='img')$app=$typ; if($aid=='')$conn=$app.':url'; else $conn=$aid.'/'.$app;
	$ret.=aj($cb.'subedt|'.$a.',subedit_form|bid='.$p['bid'].',conn='.$conn,ico($ic).$bt,'');}
return div($ret,'list');}

/*static function dskslct0($p){$a=self::$a; $cb=self::$cb; $dr=val($p,'dir'); $bck=before($dr,'/');
$r=sql('type,com,picto,bt','desktop','','where uid="'.ses('uid').'" and dir like "/documents%" order by dir asc'); $ret='';
//$ret=aj($cb.'edit|'.$a.',subedit|bid='.$p['bid'].',conn='.$p['conn'],langp('back'),'btn');
if($r)foreach($r as $k=>$v)$rb[$dir][$k]=$v;
if($r)foreach($rb as $dr=>$vr)foreach($vr as $k=>$v){
	list($typ,$com,$ic,$bt)=$v; $app=before($com,','); $aid=after($com,'=');
	if($typ=='img')$app=$typ; elseif($typ=='')$app='url'; $conn=$aid.'/'.$app;
	$ret.=aj($cb.'subedt|'.$a.',subedit_form|bid='.$p['bid'].',conn='.$conn,ico($ic).$bt,'');}
return div($ret,'list');}*/

static function subedit_form($r){$cls=sqlcols(self::$db2,6); $a=self::$a; $cb=self::$cb;
	$ret=aj($cb.'subedt|'.$a.',dskslct|bid='.$r['bid'].',conn='.$r['conn'],langp('select'),'btsav');
	$ret.=hidden('bid',$r['bid']); $conn=str_replace('/',':',$r['conn']);
	$ret.=div(input('conn',$conn,63,lang('connector')));
	if($conn)$ret.=conn::reader($conn);
	return $ret;}

//appx
static function edit($p){$p['sub']=1;
	return appx::edit($p);}

//play
static function build($p){$id=val($p,'id');
	$ra=sqljoin('name,tit',self::$db,'login','uid','ra',$id);
	$rb=sql('conn',self::$db2,'rr',['bid'=>$id]);
	return [$ra,$rb];}

static function reader($p){$id=val($p,'id');
	$r=sqljoin('name,tit',self::$db,'login','uid','ra',$id);
	$rb=sql('id,conn',self::$db2,'kv',['bid'=>$id]);
	$ret=tag('h1','',$r['tit']);
	$ret.=div(lk('/'.$r['name'],ico('user').$r['name']),'btit');
	foreach($rb as $k=>$v)$ret.=conn::reader($v);
	return $ret;}

static function play($p){
	return div(self::reader($p));}

//stream
static function stream($p){
	return div(appx::stream($p),'');}

//call
static function txt($p){$id=val($p,'id');
	if($id)$txt=sql('conn',self::$db,'v',$id);
	if($txt)return conn::read(['msg'=>$txt,'ptag'=>1]);}

static function tit($p){$id=val($p,'id');
	if($id)return sql('tit',self::$db,'v',$id);}

static function call($p){
	return div(self::play($p),'',self::$cb.$p['id']);}

static function com($p){
	return appx::com($p);}

#content
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>
