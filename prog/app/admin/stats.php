<?php

class stats{
static $private='6';
static $db='stats';
	
static function install(){
	Sql::create(self::$db,['uid'=>'int','app'=>'var','prm'=>'var','day'=>'var','ip'=>'var'],1);}

#logs
static function logs(){
	system('cp -a /var/log/apache2 /home/tlex/usr');}

static function read(){
	//return File::read('/usr/apache2/error.log');//access.log
}
static function mktab(){
	return '';
}
	
#operations
static function save($app,$prm){
	$r['uid']=ses('uid');
	$r['app']=$app;
	if(isset($prm['id']))$r['prm']=$prm['id'];
	elseif(isset($prm['usr']))$r['prm']=$prm['usr'];
	else $r['prm']=get('params');
	$r['day']=date('ymd');
	$r['ip']=ip();
	Sql::insert(self::$db,$r);}

#reader
static function pages_by_user($uid){
	$r=Sql::read('page',self::$db,'rw',['uid'=>$uid]);
	return Build::table($r);}

static function users_by_page($page){
	$r=Sql::read('uid',self::$db,'rw',['page'=>$page]);
	return Build::table($r);}

static function graph($page){
	$r=Sql::read('count(uid)',self::$db,'rv','group by day');
	return Build::table($r);}

static function live($p){$rid=val($p,'rid');
	$r=Sql::read('uid,app,prm,ip,date_format(up,"%H:%i %d/%m")',self::$db,'','order by id desc limit 100');
	$bt=aj($rid.'|stats,live|ip='.$rid,pico('refresh'));
	return $bt.Build::table($r);}
	
//interface
static function content($p){
	self::install();
	$p['rid']=randid('md');
	if($uid=val($p,'uid'))$ret=self::pages_by_user($uid);
	elseif($page=val($p,'page'))$ret=self::users_by_page($p);
	elseif($graph=val($p,'graph'))$ret=self::graph($p);
	else $ret=self::live($p);
	//$ret=self::read();
	return div($ret,'board',$p['rid']);}
}

?>