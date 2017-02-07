<?php

class profile{
	static $private='1';
	static $db='profile';
	
	//install
	static function install(){
	Sql::create('profile',array('puid'=>'int','pname'=>'var','status'=>'var','clr'=>'var','avatar'=>'var','banner'=>'var','web'=>'var','gps'=>'var','location'=>'var','privacy'=>'int','oAuth'=>'var'),1);}
	
	static function injectJs(){return '';}
	static function headers(){
		//Head::add('csslink','/css/telex.css');
		Head::add('jscode',self::injectJs());}
	
	#builders	
	//banner		
	static function banner_save($p){$f=val($p,'bkgim');
		$f=File::saveimg($f,'profile','300','100');
		Sql::update(self::$db,'banner',$f,ses('uid'),'puid');
		return self::read(['usr'=>ses('user'),'uid'=>ses('uid'),'big'=>val($p,'big')]);}
	
	static function banner_edit($p,$big){$ret='';
		$im=val($p,'banner'); $ret='';
		$f='/img/profile/medium/'.$im;
		//if(is_file($f))$ret.=img($f).br();
		$ret.=input('bkgim','',30,lang('url',1)).' ';
		$ret.=aj('prfl|profile,banner_save|usr='.val($p,'usr').',big='.$big.'|bkgim',langp('save'),'btsav');
		return $ret;}
	
	static function banner($r,$big){
		$ban='img/profile/full/'.$r['banner'];
		if(is_file($ban))
			$sty='background-color:#'.$r['clr'].'; background-image:url(/'.$ban.');';
		else $sty='background-image:linear-gradient(#'.$r['clr'].',#97c2ff);';
		if($big)$sty.=' height:320px;';
		$ret=div('','banner','',$sty);
		if($r['banner'])return imgup($ban,$ret);
		else return $ret;}
	
	//avatar
	static function avatar_im($im,$sz){//mini,full
		if(!$im)return 'icon/person-8x.png'; else return 'img/profile/'.$sz.'/'.$im;}
		
	static function avatar_save($p){$f=val($p,'urlim');
		$f=File::saveimg($f,'profile','140','140');
		Sql::update(self::$db,'avatar',$f,ses('uid'),'puid');
		return self::avatar(val($p,'usr'),$f,val($p,'big'));}
	
	static function avatar_edit($p,$big){
		$im=val($p,'avatar'); $ret='';
		$f='/img/profile/mini/'.$im;
		//if(is_file($f))$ret=img($f).br();
		$ret.=input('urlim','',30,lang('url',1)).' ';
		$ret.=aj('avt|profile,avatar_save|usr='.val($p,'usr').',big='.$big.'|urlim',langp('save'),'btsav').br();
		return $ret;}

	static function avatar_big($p){$im=val($p,'im');
		$f=self::avatar_im($im,'full');
		return img($f);}

	static function avatar($usr,$im,$big){
		$f=self::avatar_im($im,$big?'full':'mini');
		$ret=self::divim($f,$big?'avatarbig':'avatar',ses('clr'));
		$ret=imgup(self::avatar_im($im,'full'),$ret);
		return $ret;}
	
	static function divim($f,$c,$clr){
		if($clr)$clr='background-color:#'.$clr.'; ';
		return div('',$c,'',$clr.'background-image:url(\'/'.$f.'\');');}
	
	//status
	static function status_save($p){$id=val($p,'id');
		$rk=['pname','status','clr','web','gps','location'];
		foreach($rk as $v)if($val=val($p,$v))Sql::update(self::$db,$v,$val,$id);
		return self::read($p);}
	
	static function status_edit($p){
		if($v='id')$ret=hidden($v,$p[$v]);
		if($v='pname')$ret.=input($v,$p[$v],30,lang('name',1)).br();
		if($v='status')$ret.=tag('textarea',array('id'=>$v,'style'=>'width:322px; height:94px;','placeholder'=>lang('presentation',1),'maxlength'=>255),$p[$v]).br();
		if($v='web')$ret.=input($v,$p[$v],30,lang('web',1)).br();
		if($v='clr'){$clr=$p[$v]?$p[$v]:val($p,'clr'); $clrb=invert_color($clr,1);
			$ret.=tag('input',['type'=>'text','id'=>$v,'value'=>$clr,'size'=>30,'style'=>'background:#'.$clr.'; color:#'.$clrb,'onkeyup'=>'affectclr(this)'],'',1).br();}
		$ret.=aj('prfl,,x|profile,status_save|usr='.$p['usr'].'|id,pname,status,web,gps,clr',langp('save'),'btsav');
		return $ret;}
	
	static function username($p){
		$usr=val($p,'usr',ses('user')); $name=val($p,'pname');
		$ret=div(href('/'.$usr,$name),'usrnam');
		$ret.=div(href('/'.$usr,'@'.$usr),'grey');
		return $ret;}
	
	static function status($r){
		$ret=div(nl2br(val($r,'status')),'statusdiv');
		if($web=val($r,'web'))$ret.=div(href(http($web),$web.' '.pic('link',12),'grey','',1),'statusdiv');
		$ret.=div(self::gps($r),'statusdiv');
		return div($ret,'statustxt');}
	
	//gps
	static function gpsav($p){$gps=val($p,'gps');
		$id=Sql::read('id',self::$db,'v','where puid='.ses('uid'));
		Sql::update(self::$db,'gps',$gps,$id);
		$loc=Gps::com(['coords'=>$gps]);
		Sql::update(self::$db,'location',$loc,$id);
		return $loc;}
	
	static function gps($r){$ret=''; $bt='';
		$loc=$r['location']?$r['location']:lang('location');
		if($r['gps'])$bt=popup('map,com|coords='.$r['gps'],pico('location'),'grey');
		if($r['puid']==ses('uid'))$ret=btj(span($loc,'','gpsloc'),'geo()','grey');
		elseif($r['location'])$ret=span($r['location'],'grey');
		return $ret.' '.$bt;}
	
	//privacy
	static function privbt($p){$state=val($p,'privacy'); $sav=val($p,'sav');
		if($sav){$state=$state==1?'0':'1'; Sql::update('profile','privacy',$state,ses('uid'),'puid');}
		if($state==1){$ic='toggle-on'; $bt='private'; $hlp=help('privacy_on');}
		else{$ic='toggle-off'; $bt='public'; $hlp=help('privacy_off');}
		return aj('prvc|profile,privbt|sav=1,privacy='.$state,pic($ic,22).lang($bt)).div($hlp);}
	
	//oAuth
	static function oAuthsav($p){
		$ret=keygen::build([]);
		if($id=val($p,'id'))Sql::update('profile','oAuth',$ret,$id);
		return $ret;}
	
	static function oAuth($p){
		$ret=span($p['oAuth'],'grey','oath').' ';
		$ret.=aj('oath|profile,oAuthsav|id='.$p['id'],langp('gen oAuth'),'btn').' ';
		$ret.=div('http://tlex.fr/api.php?app=Api&mth=call&prm=tm:'.ses('user'),'valid');
		$ret.=div('http://tlex.fr/api.php?app=Api&mth=post&&msg=hello&prm=oAuth:'.$p['oAuth'],'valid');
		return $ret;}
	
	//com
	static function com($usr,$o=''){
		$r=Sql::read_inner('pname,avatar,status','profile','login','puid','rw','where name="'.$usr.'"');
		$f=self::avatar_im($r[1],'mini');
		$ret=self::divim($f,'avatarsmall','');
		if($o==2)$ret.=span($r[0],'btxt');
		if(!$o)$ret.=href('/'.$usr,$r[0],'btxt');
		return $ret;}
	
	//edit
	static function profile_edit($p){
		$cols='id,puid,pname,status,clr,avatar,banner,web,gps,location,privacy,oAuth';
		$r=Sql::read($cols,self::$db,'ra','where puid='.ses('uid')); $r['usr']=ses('user');
		//$ret=aj('prfl|profile,read|usr='.$p['usr'],langp('close'),'btn');
		$ret=tag('h2','',lang('status'));
		$ret.=self::status_edit($r);
		$ret.=tag('h2','',lang('avatar'));
		$ret.=self::avatar_edit($r,val($p,'big'));
		$ret.=tag('h2','',lang('banner'));
		$ret.=self::banner_edit($r,val($p,'big'));
		$ret.=tag('h2','',lang('location'));
		$ret.=div(self::gps($r).aj('prfloc|profile,gpsav|gps=,location=',pico('delete')),'','prfloc');
		$ret.=tag('h2','',lang('privacy'));
		$ret.=div(self::privbt($r),'','prvc');
		$ret.=tag('h2','','Api');
		$ret.=self::oAuth($r);
		return div($ret,'paneb','','width:440px;');}
	
	//read
	static function read($p){
		$usr=val($p,'usr'); $uid=val($p,'uid'); $big=val($p,'big');
		$subscribe=''; $follow=''; $map='';
		$cols='puid,pname,status,clr,avatar,banner,web,gps,location,privacy';
		$r=Sql::read_inner($cols,'profile','login','puid','ra','where name="'.$usr.'"');
		if(!$r && $usr && $usr==ses('user'))$r=self::create($usr);
		if(!$r)$r=['usr'=>$usr,'status'=>'','clr'=>ses('clr'),'avatar'=>'','banner'=>'','web'=>'','gps'=>'','location'=>'','privacy'=>'','oAuth'=>'']; else $r['usr']=$usr;
		if(!$clr=val($r,'clr'))$r['clr']=sesif('clr'.$usr,'7ba8fd'); else ses('clr'.$usr,$clr);//clr
		$banner=div(self::banner($r,$big),'banr');//banner
		$avatar=span(self::avatar($usr,$r['avatar'],$big),'','avt');//avatar
		if(ses('user') && ses('user')!=$usr)$follow=telex::followbt(['usr'=>$usr]);//follow
		$subscribe=div($follow.telex::subscribt($usr,$uid),'subscrban');//subscribe
		$username=div(self::username($r),'username');
		$status=div(self::status($r),'status');
		if($big)return array($banner.$subscribe,$avatar.div($username.$status,'board'));
		return $banner.$avatar.$follow.div($username.$status);}
	
	//create	
	static function create($usr){$uid=ses('uid');
		$id=Sql::read('id',self::$db,'v','where puid='.$uid);
		if(!$id && $uid){
			$clr=sesif('clr'.$usr,Clr::random());
			$r=['puid'=>ses('uid'),'pname'=>$usr,'status'=>'','clr'=>$clr,'avatar'=>'','banner'=>'','web'=>'','gps'=>'','location'=>'','privacy'=>'','oAuth'=>keygen::build()];
			$r['id']=Sql::insert(self::$db,$r);
			return $r;}}
	
	//interface
	static function content($p){
		$usr=val($p,'user',ses('user')); $id=val($p,'id');
		//self::install();
		if(ses('uid'))self::create($usr);
		//$ret=Form::com(['table'=>'profile','id'=>$id]);
		//$ret=self::edit(['pname'=>1,'id'=>$id]);
		$ret=self::read(['id'=>$id,'usr'=>$usr]);
		return $ret;
	}
}
?>
