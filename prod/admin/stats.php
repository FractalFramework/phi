<?php

class stats{
static $private=0;
static $db='stats';
	
static function install(){
	sqlcreate(self::$db,['uid'=>'int','app'=>'var','prm'=>'var','day'=>'var','ip'=>'var'],1);}

#logs
static function logs(){
	system('cp -a /var/log/apache2 /home/tlex/usr');}

static function read(){
	//return files::read('/pub/apache2/error.log');//access.log
}
static function mktab(){
	return '';}
	
#operations
static function save($app,$prm){
	$r['uid']=ses('uid');
	$r['app']=$app;
	if(isset($prm['id']))$r['prm']=$prm['id'];
	elseif(isset($prm['usr']))$r['prm']=$prm['usr'];
	else $r['prm']=get('params');
	$r['day']=date('ymd');
	$r['ip']=ip();
	$id=sqlsav(self::$db,$r);}

#reader
static function pages_by_user($uid){
	$r=sql('page',self::$db,'rw',['uid'=>$uid]);
	return mktable($r);}

static function users_by_page($page){
	$r=sql('uid',self::$db,'rw',['page'=>$page]);
	return mktable($r);}

static function graph($page){
	$r=sql('count(uid)',self::$db,'rv','group by day');
	return mktable($r);}

static function live($p){$rid=val($p,'rid');
	$r=sql('uid,app,prm,ip,date_format(up,"%H:%i %d/%m")',self::$db,'','order by id desc limit 200');
	//$r=sqljoin('name,count(stats.ip) as nb,app,prm,stats.ip,date_format(stats.up,"%H:%i %d/%m/%Y") as date',self::$db,'login','uid','','group by stats.ip order by stats.id desc');
	$bt=aj($rid.'|stats,live|ip='.$rid,pic('refresh'));
	return $bt.mktable($r);}
	
//interface
static function content($p){
	//self::install();
	$p['rid']=randid('md');
	if($uid=val($p,'uid'))$ret=self::pages_by_user($uid);
	elseif($page=val($p,'page'))$ret=self::users_by_page($p);
	elseif($graph=val($p,'graph'))$ret=self::graph($p);
	else $ret=self::live($p);
	//$ret=self::read();
	return div($ret,'board',$p['rid']);}
}

?>