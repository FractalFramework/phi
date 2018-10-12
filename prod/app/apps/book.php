<?php

class book{
static $private='0';
static $a='book';
static $db='book';
static $cb='bok';
static $cols=['tit'];
static $db2='book_chap';
static $conn=0;

function __construct(){
	$r=['a','db','cb','cols','db2'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(['tit'=>'var']);
	sqlcreate(self::$db2,['bid'=>'int','chapter'=>'var','txt'=>'text'],1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function injectJs(){
	$d='function format(p,o){document.execCommand(p,false,o?o:null);}';
	return ;}

static function headers(){
	//add_prop('og:title',self::$title);
	//add_prop('og:description',self::$description);
	//add_prop('og:image',self::$image);
	add_head('csscode','
	.book{padding:60px; font-family:Times New Roman,serif; text-align:justify; font-size:20px;}
	.btxt{text-align:justify;}
	.book h1, .book h2{text-align:left;}
	.bookcover{margin:20px text-align:center;}
	.booknfo{padding:10px 0; text-align:center;}');
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

static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}
static function subedit_form($r){
	$ret=hidden('bid',$r['bid']);
	$ret.=div(input('chapter',$r['chapter'],63,lang('chapter'),'',255));
	$ret.=divarea($r['txt'],'editarea','txt');
	return $ret;}

//appx
static function edit($p){
	$p['sub']=1;
	return appx::edit($p);}

//play
static function build($p){$id=val($p,'id');
	$ra=sqljoin('name,tit',self::$db,'login','uid','ra',$id);
	$rb=sql('chapter,txt',self::$db2,'rr',['bid'=>$id]);
	return [$ra,$rb];}

static function play0($p){$a=self::$a;
	list($ra,$rb)=self::build($p);
	$ret=vue::read($ra,'[[(tit):h1][(name)*class=btit:div]*class=booknfo:div]');
	$ret.=vue::read_r($rb,'[[[(chapter):h3](txt)*class=txt:div]:div]');
	return div($ret,'book');}

static function cover($p){$id=val($p,'id');
	$r=sqljoin('name,tit',self::$db,'login','uid','ra',$id);
	$rb=sql('id,chapter',self::$db2,'kv',['bid'=>$id]);
	$ret=tag('h1','',$r['tit']);
	$ret.=div(lk('/'.$r['name'],ico('user').$r['name']),'btit');
	foreach($rb as $k=>$v)$ret.=aj(self::$cb.$id.'|book,play|id='.$id.',chapter='.$k,$v,'licon');
	return div($ret,'');}

static function nav($p){
	$id=val($p,'id'); $idb=val($p,'chapter');
	$cb=self::$cb; $ret=''; $prev=''; $next='';
	$r=sql('id',self::$db2,'rv','where bid='.$id.' order by id asc');
	foreach($r as $k=>$v)
		if($v==$idb){if(isset($r[$k-1]))$prev=$r[$k-1]; if(isset($r[$k+1]))$next=$r[$k+1];}
	if($prev)$ret.=aj($cb.$id.'|book,play|id='.$id.',chapter='.$prev,langp('previous'),'btn');
	if($next)$ret.=aj($cb.$id.'|book,play|id='.$id.',chapter='.$next,langp('next'),'btn');
	return $ret;}

static function reader($p){$id=val($p,'id'); $idb=val($p,'chapter'); $cb=self::$cb;
	$r=sql('tit',self::$db,'ra',$id);
	$rb=sql('chapter,txt',self::$db2,'ra',$idb);
	$ret=div(aj($cb.$id.'|book,call|id='.$id,$r['tit']),'btit booknfo');
	$tit=tag('h2','',$rb['chapter']);
	$ret.=div($tit.$rb['txt'],'btxt');
	$ret.=div(self::nav($p),'btit booknfo');
	return div($ret,'');}

static function play($p){$id=val($p,'id'); $idb=val($p,'chapter');
	if(!$idb)$ret=self::cover($p);
	else $ret=self::reader($p);
	return div($ret,'book');}

//stream
static function stream($p){
	return div(appx::stream($p),'');}

//call
static function txt($p){$id=val($p,'id');
	if($id)$txt=sql('txt',self::$db,'v',$id);
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
