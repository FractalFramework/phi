<?php
class product{
static $private='0';
static $a='product';
static $db='product';
static $cb='bnk';
static $cols=['typ','tit','descr','img','price','rate','state','status'];
static $typs=['int','var','text','var','int','int','int','int'];
static $db2='product_stock';
static $t='tit';
static $open=1;
static $credits=['red','blue','green'];//mass,time,space
static $status=['used','for lent','for rent','for sale','loan','rented','sold','destroyed'];
static $roles=['man','corp','rsrc','cmd'];

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));
	//red,blue,green credits//role: profile status
	sqlcreate('product_stock',['uid'=>'int','role'=>'int','red'=>'int','blue'=>'int','green'=>'int'],1);
	sqlcreate('product_moves',['aid'=>'int','val'=>'int','to'=>'int'],1);
	sqlcreate('product_criters',['aid'=>'int','val'=>'int'],1);
	sqlcreate('product_featurs',['cid'=>'int','val'=>'int'],1);
	//sqlcreate('product_crons',['aid'=>'int','val'=>'int','to'=>'int','at'=>'int'],1);
	sqlcreate('product_contr',['cid'=>'int','if'=>'var'],1);}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	add_head('csscode','
	.coin{background:#ffffff; border:1px solid #fff; display:inline-block; padding:4px 6px;}
	.coin:hover{box-shadow:0,0,6px,rgba(0,0,0,0.4);}
	.red,.blue,.green{text-align:center;}
	.red{background:#ff4444; color:white;} .red:hover{background:#ff4444;}
	.blue{background:#4444ff; color:white;} .blue:hover{background:#4444ff;}
	.green{background:#44ff44; color:black;} .green:hover{background:#44ff44;}
	.instit{background:#f4f4f4; color:black;}
	.money{background:#f4f4f4; margin:6px;}');
	add_head('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function modif($p){//pr($p);
	return appx::modif($p);}

static function form($p){$ret='';
	$cols=sqlcols(self::$db,4); $cls=implode(',',array_keys($cols));
	foreach(self::$credits as $k=>$v)$rty[$k]=lang($v.'_descr');
	foreach($cols as $k=>$v){$val=val($p,$k);
		if($k=='typ')$bt=radio($k,$rty,$typ=$val,'');
		elseif($k=='img')$bt=input($k,$val).upload::call($k);
		elseif($k=='rate'){
			if($typ>0)$bt=bar($k,$val,10,0,100,'inn','');
			else $bt=hidden($k,$val);}
		elseif($k=='state')$bt=bar($k,$val,10,0,100,'inn','');
		elseif($k=='status')$bt=select($k,self::$status,$val,2);
		elseif($v=='int')$bt=input($k,$val,'','','1',5);
		else $bt=input($k,$val,63,'','',255);
		$ret.=input_row($k,$bt,$k);}
	$ret=div($ret,'table');
	return $ret;}

static function edit($p){
	return appx::edit($p);}

static function add($p){
	$typ=$p['typ'];
	$ret=self::form($p);
	return $ret;}

static function create($p){$ret='';
	$ret=div(aj(self::$cb.'|product,add|typ=red',langp('add'),'btn red'),'coin');
	$ret.=div(aj(self::$cb.'|product,add|typ=blue',langp('add'),'btn blue'),'coin');
	$ret.=div(aj(self::$cb.'|product,add|typ=green',langp('add'),'btn green'),'coin');
	return div($ret,'');}

#role
static function savrole($p){$set=val($p,'set',0);
	if($set)sqlup(self::$db2,'role',$set,ses('uid'),'uid');
	return lang(self::$roles[$set]);}

static function setrole($p){$ret='';
	$role=sql('role',self::$db2,'v',['uid'=>ses('uid')]);
	foreach(self::$roles as $k=>$v){$c=$k==$role?'active':'';
		$ret.=aj('role|product,savrole|set='.$k,lang($v),$c);}
	return div($ret,'list');}

static function playrole($r){$d=$r['role']; //p($r);
	$ra=self::$roles; $role=$ra[$d];
	$ret=div(lang($role),'','role');
	$ret.=bubble('product,setrole',lang('set role'),'small');
	return div($ret,'coin');}

#build
static function build($p){
	//$cols=sqlcols(self::$db,1);
	$cols=implode(',',self::$cols);
	return sql('id,'.$cols,self::$db,'ra',$p['id']);}

#account
static function create_stock(){
	$r=[ses('uid'),'0','0','0','0'];
	sqlsav(self::$db2,$r);
	return $r;}

static function accounts($p){$ret=''; $typ=$p['typ'];
	$r=sql('all',self::$db,'rr',['uid'=>ses('uid'),'typ'=>$typ]); //pr($r);
	return $ret;}

static function coin($typ,$n){
	$ret=div(lang($typ.'_money'),'btn');
	$ret.=div($n,'btit');
	//$ret.=aj(self::$cb.'|product,add|typ='.$typ,langpi('add'),'btn');
	//$ret.=aj('account|product,account|typ='.$typ,langpi('view'),'btn');
	return div(div($ret,'money'),'coin '.$typ);}

static function account(){
	$r=sql('all',self::$db2,'ra',['uid'=>ses('uid')]); //pr($r);
	if(!$r)$r=self::create_stock();
	$ret=self::playrole($r);
	foreach(self::$credits as $v)$ret.=self::coin($v,$r[$v]);
	$ret.=div('','','account');
	return $ret;}

#play
static function play($p){
	$r=self::build($p); $a=self::$a; //pr($r);
	$typ=self::$credits[$r['typ']];
	$ret=div($r['tit'],'tit');
	$ret.=div($r['descr'],'txt');
	$rt[]=[lang('type'),div(lang($typ.'_descr'),'coin '.$typ)];
	$rt[]=[lang('photo'),build::mini($r['img'])];
	$rt[]=[lang('price'),$r['price']];
	if($r['typ']>0)$rt[]=$rt[]=[lang('rate'),$r['rate']];
	$rt[]=[lang('state'),$r['state'].'/100'];
	$rt[]=[lang('status'),lang(self::$status[$r['status']])];
	$ret.=mktable($rt);
	return $ret;}

static function stream($p){
	$p['t']='tit';
	//$ret=self::account();
	$ret=aj(self::$cb.'|product,account',langpi('stock'),'btn');
	$ret.=appx::stream($p);
	//$ret=self::play($p);
	return $ret;}

#call (read)
static function tit($p){
	$p['t']='tit';
	return appx::tit($p);}

static function call($p){
	return appx::call($p);}

#com (edit)
static function com($p){
	return appx::com($p);}

#interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>
