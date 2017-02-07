<?php

class login{
	static $private='0';
	static $authlevel='1';
	static $css='btn';

	static function headers(){
		Head::add('csscode','#cbklg{display:inline;}');}
	
	//install
	static function install(){
		Sql::create('login',array('name'=>'var','password'=>'var','auth'=>'int','mail'=>'var','ip'=>'var','priv'=>'int'));}
	
	//recover
	static function recover($p){
		$user=val($p,'user');
		$mail=val($p,'mail');
		$state=Auth::recovery($user,$mail);
		return self::reaction($state,$user);}
	
	static function recoverForm($p){
		$user=val($p,lang('user'));
		$ret=input('mail','',26,lang('mail'));
		$btn=lang('recover_pswd');
		$ret.=aj('cbklg|login,recover|user='.$p['user'].',time='.time().'|mail',$btn,'btdel');
		return $ret;}
	
	static function recoverBtn($user=''){
		$btn=lang('forgotten_pswd');
		return aj('cbklg|login,recoverForm|user='.$user,$btn,'btdel');}
	
	static function recoverVerif($reco){
		if($reco!=ses('recoveryRid') or !ses('recoveryUsr'))return 'recovery_fail';
		return 'recovery_set';}
	
	static function recoverValidation($user){
		if(ses('user') or !ses('recoveryUsr'))return lang('error');
		$ret=input('recpsw','','18','',1);
		$ret.=aj('recocbk|login,recoverSave||recpsw',lang('set as new password'),'btsav');
		return div($ret,'','recocbk');}
	
	static function recoverSave($p){
		$pswd=val($p,'recpsw');
		$user=ses('recoveryUsr');
		if(!$id=ses('recoveryId'))return 'recovery_fail';
		if($user && $pswd)Sql::query('update login set password=PASSWORD("'.$pswd.'") where id='.$id);
		sez('recoveryUsr'); sez('recoveryRid'); sez('recoveryId');
		return Auth::login($user,$pswd);}
	
	//register
	static function register($p){
		$user=val($p,'user');
		$pass=val($p,'pass');
		$mail=val($p,'mail');
		$auth=val($p,'auth');
		if($user && $pass && $mail)
			$state=Auth::register($user,$pass,$mail,$auth); 
		else $state='register_fail';
		return self::reaction($state,$user);}
	
	static function verifusr($p){
		return Sql::read('id','login','v','where name="'.$p['user'].'"');}
	
	static function registerForm($p){$ret='';
		$user=val($p,lang('user')); $sz='28';
		//$cntx=val($p,'cntx');
		$ret=tag('input',['id'=>'user','placeholder'=>$user?$user:lang('user',1),'size'=>$sz,'maxlength'=>20,'onkeyup'=>'verifchars(this); verifusr(this);'],'',1).span(lang('user used'),'alert hide','usrexs').br();
		$ret.=password('pass',lang('password',1),$sz,1);
		$ret.=aj('lgkg|keygen,build',pic('key')).div('','','lgkg');
		$ret.=div(input('mail','',$sz,lang('mail',1)));
		$ret.=hidden('auth',ses('authlevel'));
		//$ret.=hidden('cntx',$cntx);
		$btn=langp('register');
		$ret.=aj('div,cbklg,reload|login,register|time='.time().'|user,pass,mail,auth',$btn,'btsav');
		return $ret;}
	
	static function registerBtn($user=''){
		$btn=langp('register');
		return aj('cbklg|login,registerForm|user='.$user,$btn,self::$css);}
	
	//logout
	static function disconnect(){
		$state=Auth::logout();
		return self::reaction($state);}
	
	static function logoutBtn($user){
		$ret=tag('span','class=small',$user).' ';
		$btn=langp('logout');
		return aj('div,cbklg,reload|login,disconnect',$btn,self::$css);}
	
	static function loged($user){
		return span(lang('logok').' '.$user.' (auth:'.ses('auth').')','valid');}
	
	//login
	static function authentificate($p){
		$user=val($p,'user');
		$pass=val($p,'pass');
		$state=Auth::login($user,$pass);
		if($state=='loged_ok')return $state;//expected for reload
		return self::reaction($state,$user);}
	
	static function loginForm($p){$ret=''; $user=val($p,'user');
		if(!$user)$user=Sql::read('name','login','v','where ip="'.ip().'"');
		if($user)$ret=input('user',$user,12); else $ret=input('user','user',8,1);
		$ret.=password('pass','*****',10,1);
		$j='reload,cbklg,loged_ok|login,authentificate|time='.time().'|user,pass';
		$ret.=aj($j,langp('login'),self::$css);
		return $ret;}
	
	static function loginBtn($user=''){
		if(!$user)$user=cookie('user');
		$btn=langp('login');
		return aj('cbklg|login,loginForm|user='.$user,$btn,self::$css);}
	
	//alerts
	static function reaction($state,$user=''){
		$login=self::loginBtn($user); 
		$alert=lang($state);
		//$reload=href('/app/'.ses('app'),langp('reload'),self::$css);
		switch($state){
			case('loged'):$ret=self::loged($user).self::logoutBtn($user); break;
			case('loged_ok'):$ret=self::loged($user).self::logoutBtn($user); break;
			case('loged_out'):$ret=$login.self::registerBtn($user); break;
			case('loged_private'):$ret=$login; break;
			case('bad_password'):$ret=$login.self::recoverBtn($user); break;
			case('unknown_user'):$ret=$login.self::registerBtn($user); break;
			case('register_fail'):$ret=$login.self::registerBtn($user); break;
			case('register_error'):$ret=self::registerBtn($user); break;
			case('register_fail_mail'):$ret=self::registerBtn($user); break;
			case('register_fail_aex'):$ret=self::registerBtn($user); break;
			case('recovery_mailsent'):$ret=help('recovery_mailsent'); break;
			case('recovery_set'):$ret=self::recoverValidation($user); break;
			default:$ret=span($alert,'small'); break;
		}
		if($alert && $state!='loged' && $state!='loged_ok' && $state!='loged_out')
			$ret=span($alert,'alert').$ret;
		//if($state=='loged_ok')reload('/');
		return $ret;}
	
	//content
	static function com($p){$ret=''; $user='';
		$auth=val($p,'auth');
		if(val($p,'o'))self::$css='btn abbt';
		ses('authlevel',$auth?$auth:self::$authlevel);
		$state=Auth::login('','');
		if($state=='ip_found')$user=Auth::getUserByUid(ses('uid'));
		elseif($state=='cookie_found')$user=cookie('user');
		elseif($state=='loged')$user=ses('user');
		$ret.=self::reaction($state,$user);
	return div($ret,'','cbklg');}
	
	//content
	static function content($p){$ret='';
		//Auth::create();//create table
		//self::install();
		$user=val($p,'user');
		$pass=val($p,'pass');
		$auth=val($p,'auth');
		if(val($p,'o'))self::$css='';
		ses('authlevel',$auth?$auth:self::$authlevel);
		$state=Auth::login($user,$pass);
		if($reco=val($p,'recovery'))$state=self::recoverVerif($reco);
		if($state=='ip_found')$user=Auth::getUserByUid(ses('uid'));
		elseif($state=='cookie_found')$user=cookie('user');
		elseif($state=='loged')$user=ses('user');
		$ret.=self::reaction($state,$user);
	return div($ret,'','cbklg');}

}

?>