<?php
class login{
static $private='0';
static $authlevel='2';
static $css='btn';
static $sz='30';

static function headers(){
	add_head('csscode','#cbklg{margin:0px;}');}

//install
static function install(){
	sqlcreate('login',['name'=>'var','password'=>'var','auth'=>'int','mail'=>'var','ip'=>'var']);}

//recover
static function recover($p){
	$user=val($p,'user');
	$mail=val($p,'mail');
	$state=auth::recovery($user,$mail);
	return self::reaction($state,$user);}

static function recoverform($p){
	$user=val($p,lang('nickname'));
	$ret=input('mail','',self::$sz,lang('mail'));
	$j='cbklg|login,recover|user='.$p['user'].',time='.time().'|mail';
	$ret.=aj($j,lang('recover_pswd'),'btdel');
	return $ret;}

static function recoverBtn($user=''){
	$j='cbklg|login,recoverform|user='.$user;
	return aj($j,lang('forgotten_pswd'),'btdel');}

static function recoverVerif($reco){
	if($reco!=ses('recoveryRid') or !ses('recoveryUsr'))return 'recovery_fail';
	return 'recovery_set';}

static function recoverValidation($user){
	if(ses('user') or !ses('recoveryUsr'))return lang('error');
	$ret=input('recpsw','',self::$sz,'',1);
	$ret.=aj('recocbk|login,recoverSave||recpsw',lang('set as new password'),'btsav');
	return div($ret,'','recocbk');}

static function recoverSave($p){
	$pswd=val($p,'recpsw');
	$user=ses('recoveryUsr');
	if(!$id=ses('recoveryId'))return 'recovery_fail';
	if($user && $pswd)qr('update login set password=PASSWORD("'.$pswd.'") where id='.$id);
	sez('recoveryUsr'); sez('recoveryRid'); sez('recoveryId');
	return auth::login($user,$pswd);}

//register
static function register($p){
	$user=val($p,'user'); $pass=val($p,'pass'); $mail=val($p,'mail'); $auth=val($p,'auth');
	if($user && $pass && $mail){
		$state=auth::register($user,$pass,$mail,$auth);
		profile::create($user);}
	else $state='register_fail';
	return self::reaction($state,$user);}

static function verifusr($p){
	return sql('id','login','v','where name="'.$p['user'].'"');}

static function superadmin(){
	$ex=sql('count(id)','login','v','');
	if(!$ex)return 6; else return ses('authlevel');}

static function registerform($p){$ret='';
	$user=val($p,'user');
	//$cntx=val($p,'cntx');
	$ret=tag('input',['id'=>'user','placeholder'=>lang('nickname',1),'size'=>self::$sz,'maxlength'=>20,'onkeyup'=>'verifchars(this); verifusr(this);'],'',1).span(lang('user used'),'alert hide','usrexs').br();
	$ret.=password('pass','',self::$sz,lang('password',1));
	//$ret.=aj('lgkg|keygen,build',ico('key')).div('','','lgkg');
	$ret.=div(input('mail','',self::$sz,lang('mail',1)));
	$auth=self::superadmin();//first user
	$ret.=hidden('auth',$auth);
	//$ret.=hidden('cntx',$cntx);
	$j='reload,cbklg,register_ok|login,register|time='.time().'|user,pass,mail,auth';
	$ret.=aj($j,langp('register'),'btsav');
	return $ret;}

static function registerBtn($user=''){
	$j='cbklg|login,registerform|user='.$user;
	return aj($j,langp('register'),self::$css);}

//logout
static function disconnect(){
	$state=auth::logout();
	return self::reaction($state);}

static function logoutBtn($user){
	$ret=tag('span','class=small',$user).' ';
	$j='div,cbklg,reload|login,disconnect';
	return aj($j,langp('logout'),self::$css);}

static function loged($user){
	return span(lang('logok').' '.$user.' (auth:'.ses('auth').')','valid');}

//login
static function authentificate($p){
	$user=val($p,'user');
	$pass=val($p,'pass');
	$state=auth::login($user,$pass);
	if($state=='loged_ok')return $state;//expected for reload
	return self::reaction($state,$user);}

static function badger($p){$user=val($p,'user');
	$r=sql('id,auth','login','ra','where name="'.$user.'" and mail="'.ses('mail').'"');
	if($r){profile::init_clr(['usr'=>$user]);
		ses('user',$user); ses('uid',$r['id']); ses('auth',$r['auth']);
		return 'loged_ok';}}

static function loginform($p){$ret=''; $user=val($p,'user');
	if(!$user)$user=sql('name','login','v','where ip="'.ip().'"');
	$ret=div(input('user',$user?$user:'',self::$sz,lang('user',1)));
	$ret.=div(password('pass','',self::$sz,'*****'));
	$j='reload,cbklg,loged_ok|login,authentificate|time='.time().'|user,pass';
	$ret.=div(aj($j,langp('login'),self::$css));
	$ret.=div(aj('cbklg|login,registerform||user',langp('register'),self::$css));
	return $ret;}

static function loginBtn($user=''){
	if(!$user)$user=cookie('user');
	$j='cbklg|login,loginform|user='.$user;
	return aj($j,langp('login'),self::$css);}

//alerts
static function reaction($state,$user=''){
	$alert=lang($state);
	switch($state){
		case('loged'):$ret=self::loged($user).self::logoutBtn($user); break;
		case('loged_ok'):$ret=self::loged($user).self::logoutBtn($user); break;
		case('loged_out'):$ret=self::loginform($user); break;
		case('loged_private'):$ret=self::loginform($user); break;
		case('bad_password'):$ret=self::loginform($user).self::recoverBtn($user); break;
		case('unknown_user'):$ret=self::loginform($user); break;
		case('register_ok'):$ret=$state; break;//used for reload
		case('register_fail'):$ret=self::loginform($user); break;
		case('register_error'):$ret=self::registerBtn($user); break;
		case('register_fail_mail'):$ret=self::registerBtn($user); break;
		case('register_fail_aex'):$ret=self::registerBtn($user); break;
		case('recovery_mailsent'):$ret=help('recovery_mailsent'); break;
		case('recovery_set'):$ret=self::recoverValidation($user); break;
		default:$ret=span($alert,'small'); break;
	}
	if($alert && $state!='loged' && $state!='loged_ok' && $state!='loged_out' && $state!='register_ok')$ret=div($alert,'alert').$ret;
	return $ret;}

//tlex
static function com($p){$ret=''; $user='';
	if(val($p,'o'))self::$css='btn abbt';
	$auth=val($p,'auth');
	ses('authlevel',$auth?$auth:self::$authlevel);
	$state=auth::login('','');
	if($state=='ip_found')$user=auth::getUserByUid(ses('uid'));
	elseif($state=='cookie_found')$user=cookie('user');
	elseif($state=='loged')$user=ses('user');
	$ret.=self::reaction($state,$user);
	return div($ret,'paneb','cbklg');}

//content
static function content($p){$ret='';
	//auth::install();
	//self::install();
	$user=val($p,'user');
	$pass=val($p,'pass');
	$auth=val($p,'auth',2);
	if(val($p,'o'))self::$css='';
	ses('authlevel',$auth?$auth:self::$authlevel);
	$state=auth::login($user,$pass);
	if($reco=val($p,'recovery'))$state=self::recoverVerif($reco);
	if($state=='ip_found')$user=auth::getUserByUid(ses('uid'));
	elseif($state=='cookie_found')$user=cookie('user');
	elseif($state=='loged')$user=ses('user');
	$ret.=self::reaction($state,$user);
	return div($ret,'paneb','cbklg');}
}
?>