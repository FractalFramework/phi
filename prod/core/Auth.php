<?php

class Auth {
	static $db='login';
	static $mailAdmin='bot@tlex.fr';
	static $noregister='0';
	
	static function install(){
		$r=array('name'=>'var','password'=>'var','auth'=>'int','mail'=>'var','ip'=>'var');
		Sql::create(self::$db,$r);}
	
	static function updateIp($uid){
		$r=array('name'=>'var','password'=>'var','ip'=>'var');
		Sql::update(self::$db,'ip',ip(),$uid);}
	
	static function logout(){
		unset($_SESSION['user']); unset($_SESSION['uid']); unset($_SESSION['auth']);
		setcookie('user','',0); setcookie('uid','',0);
		if(self::$noregister)return 'loged_private';
		else return 'loged_out';}
	
	static function recovery($user,$mail){
		$mail=Sql::read('mail',self::$db,'v','where name="'.$user.'" and mail="'.$mail.'"');
		if(!$mail)return 'unknown_user';
		$id=Sql::read('id',self::$db,'v','where name="'.$user.'"');
		$rid=randid('rcvd'); ses('recoveryId',$id); ses('recoveryRid',$rid); ses('recoveryUsr',$user);
		$title=lang('reset_pswd');
		$msg='http://'.$_SERVER['HTTP_HOST'].'/app/login/recovery:'.$rid;
		Mail::send($mail,$title,$msg,self::$mailAdmin,'text');
		return 'recovery_mailsent';}
	
	static function register($user,$pass,$mail,$auth){$ip=ip();
		if(!filter_var($mail,FILTER_VALIDATE_EMAIL))return 'register_fail_mail';
		if(Sql::read('id','login','v','where name="'.$user.'"'))return 'register_fail_aex';
		$r=array($user,'PASSWORD("'.$pass.'")',$auth,$mail,$ip);
		$uid=Sql::insert(self::$db,$r);
		if($uid)self::activateSession($uid,$user,$auth);
		self::activateCookie($uid,$user);
		$title=lang('register');
		$msg=lang('register_success');
		if($uid>0){Mail::send($mail,$title,$msg,self::$mailAdmin,'text');
			return 'loged';}
		else return 'register_error';}

	static function activateSession($uid,$user,$auth){
		sez('uid',$uid); sez('user',$user); sez('auth',$auth);}

	static function activateCookie($uid,$user){
		cookie('uid',$uid); cookie('user',$user);}

	static function getUserFromCookie(){
		if(isset($_COOKIE['user']))
		return Sql::read('id,name,auth',self::$db,'ra','where id="'.$_COOKIE['uid'].'"');}

	static function getUserFromIp(){
		return Sql::read('id,name,auth',self::$db,'ra','where ip="'.ip().'"');}

	static function getUserByUid($uid){
		return Sql::read('name',self::$db,'v','where id="'.$uid.'"');}

	static function getUidOfUser($user){
		return Sql::read('id',self::$db,'v','where name="'.$user.'"');}
	
	static function logon($uid,$user,$auth){
		self::activateSession($uid,$user,$auth);
		self::activateCookie($uid,$user);
		return 'loged_ok';}
	
	static function login($user='',$pass=''){
		//self::install();
		$uid=ses('uid'); if($uid)return 'loged';
		$user=normalize($user);
		$pass=normalize($pass);
		//$uid=cookie('uid'); if($uid)$state='cookie_found';//login with cookies
		if(self::$noregister)$state='loged_private'; else $state='loged_out';
		if($user){
			$uid=self::getUidOfUser($user);
			if($uid && $user && !$pass){//recognize
				$ra=self::getUserFromIp();
				if(isset($ra['name']) && $ra['name']==$user)
					$state=self::logon($ra['id'],$ra['name'],$ra['auth']);}
			elseif($user && $pass){
				$sql='where name="'.$user.'" and password=PASSWORD("'.$pass.'")';
				$rb=Sql::read('id,ip,auth',self::$db,'ra',$sql);
				if($rb){
					$state=self::logon($rb['id'],$user,$rb['auth']);
					if($rb['ip']!=ip())self::updateIp($uid);}
				elseif($uid)$state='bad_password';
				else $state='unknown_user';}
			else $state='unknown_user';}
		return $state;}
	
	static function autolog(){
		$r=self::getUserFromCookie();
		if(isset($r['name']))self::activateSession($r['id'],$r['name'],$r['auth']);
		else $r=self::getUserFromIp();
		if(isset($r['name']))self::activateSession($r['id'],$r['name'],$r['auth']);}
	
	static function logbt($o=''){
		if($o){$mode='menu'; login::$css='';} else{$mode='pagup'; login::$css='btn abbt';}
		//if(!ses('time'))self::autolog();
		$bt=ses('user')?ses('user'):lang('login');
		return Ajax::j($mode.',,1|login|auth=2,o='.$o,pic('user').' '.$bt,login::$css);}//level2 default

}
