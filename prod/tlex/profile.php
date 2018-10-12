<?php
class profile{
static $private='1';
static $db='profile';
static $default_clr='e6ecf0';//1da1f2//f2f2f2
static $roles=['human','group','industry','institution','non-terrestrial'];

//install
static function install(){
sqlcreate(self::$db,array('puid'=>'int','pusr'=>'var','pname'=>'var','status'=>'var','clr'=>'var','avatar'=>'var','banner'=>'var','web'=>'var','gps'=>'var','location'=>'var','privacy'=>'int','oAuth'=>'var','ntf'=>'int','role'=>'int'),1);}

static function injectJs(){return '';}
static function headers(){}

#tools
static function init_clr($p){
	$clr=sesr('clr',$p['usr']); if($clr)return $clr;
	$clr=sql('clr',self::$db,'v','where pusr="'.$p['usr'].'"');
	if(!$clr)$clr=self::$default_clr;
	//ses('clr'.$p['usr'],$clr);
	return sesr('clr',$p['usr'],$clr);}

//banner		
static function banner_save($p){$f=val($p,'bkgim');
	if(substr($f,0,4)=='http')$f=files::saveimg($f,'prf','300','100','banner');
	sqlup(self::$db,'banner',$f,ses('uid'),'puid');
	return self::standard(['usr'=>ses('user'),'uid'=>ses('uid'),'big'=>val($p,'big')]);}

static function banner_edit($p,$big){$ret='';
	$im=val($p,'banner'); $usr=val($p,'pusr');
	$f='/img/medium/'.$im;
	//if(is_file($f))$ret.=img($f).br();
	$ret.=input('bkgim',$im,30,lang('url',1)).' ';
	$ret.=aj('prfl|profile,banner_save|usr='.$usr.',big='.$big.'|bkgim',langp('save'),'btsav').' ';
	$ret.=upload::call('bkgim');
	return $ret;}

static function banner($r,$big){
	$ban='img/full/'.$r['banner'];
	$clr=$r['clr'];
	if(is_file($ban)){$sz=0;
		list($w,$h)=getimagesize($ban);//if($w>1000)$sz=$h-280/2; if($sz<0)$sz=0;
		//$sz='background-position:center '.$sz.'px;';
		$sty='background-color:#'.$clr.'; background-image:url(/'.$ban.');';}
	else $sty='background-image:linear-gradient(#97c2ff,#'.$clr.');';
	if($big)$sty.=' height:360px;';
	$ret=div(val($r,'cntban'),'banner','',$sty);
	if($r['banner'])return imgup($ban,$ret);
	else return $ret;}

//avatar
static function avatar_im($im,$sz){//mini,full
	if($im)return 'img/'.$sz.'/'.$im;}
	
static function avatar_save($p){$f=val($p,'avtim'); $usr=val($p,'pusr');
	if(substr($f,0,4)=='http')$f=files::saveimg($f,'prf','140','140','avatar');
	sqlup(self::$db,'avatar',$f,ses('uid'),'puid'); $p['avatar']=$f;
	return self::avatar($p,val($p,'big'));}

static function avatar_edit($p,$big){
	$im=val($p,'avatar'); $usr=val($p,'pusr'); $ret=''; $f='/img/mini/'.$im;
	//if(is_file($f))$ret=img($f).br();
	$ret.=input('avtim',$im,30,lang('url',1)).' ';
	$prm='pusr='.$usr.',big='.$big.',clr='.diez($p['clr'],1);
	$ret.=aj('avt|profile,avatar_save|'.$prm.'|avtim',langp('save'),'btsav').' ';
	$ret.=upload::call('avtim');
	return $ret;}

static function avatar_big($p){$im=val($p,'im');
	$f=self::avatar_im($im,'full');
	return img($f);}

static function avatar($p,$big){
	$usr=$p['pusr']; $im=$p['avatar']; $clr=($p['clr']);//diez
	$f=self::avatar_im($im,$big?'full':'mini');
	$bt=self::divim($f,$big?'avatarbig':'avatar',$clr);
	$ret=imgup(self::avatar_im($im,'full'),$bt);
	return $ret;}

static function avatarsmall($p){$im=val($p,'avatar');
	$clr=self::init_clr(['usr'=>$p['name']]);
	$f=self::avatar_im($im,'mini',$clr);
	return self::divim($f,'avatarsmall',$clr);}

static function divim($f,$c,$clr){
	if($clr)$clr='background-color:#'.$clr.'; ';
	return span('',$c,'',$clr.'background-image:url(\'/'.$f.'\');');}

//status
static function status_save($p){$id=val($p,'id');
	$rk=['pname','status','web','gps','clr','role'];
	//ses('clr'.$p['usr'],$p['clr']);
	$r=vals($p,$rk); sqlups(self::$db,$r,$id);
	sesr('clr',$p['usr'],$r['clr']);
	return self::standard($p);}

//authorize levels//by
static function roles($d){
	$w='inner join login on '.self::$db.'.puid=login.id 
	inner join tlex_ab on login.name=tlex_ab.usr 
	where ab="'.ses('uid').'"';
	//$r=sql('puid,login.name,role',self::$db,'rr',$w); p($r);
	if(auth(6))$n=7; else $n=4;
	foreach(self::$roles as $k=>$v)if($k<$n)$r[]=$v;
	return $r;}

static function sky(){$ret='';
$r=sql('tit,css','sky','kv','where uid='.ses('uid').' or pub>2'); //p($r);
foreach($r as $k=>$v)$ret.=tag('a',['onclick'=>atj('val',['-'.$k,'clr']),'style'=>'height:40px; width:100px; display:inline-block; background-image:'.$v],$k);
return div($ret,'');}

static function status_edit($p){
	if($v='id')$ret=hidden($v,$p[$v]);
	if($v='pname')$ret.=input($v,$p[$v],'',lang('name',1)).br();
	if($v='status')$ret.=tag('textarea',['id'=>$v,'placeholder'=>lang('presentation',1),'maxlength'=>255],$p[$v]).br();
	if($v='role')$ret.=select($v,self::roles($v),$p[$v],2).br();
	if($v='web')$ret.=input($v,$p[$v],'',lang('web',1)).br();
	if($v='clr'){$clr=$p[$v]?$p[$v]:val($p,'clr'); $clrb=clrneg($clr,1);
		$ret.=tag('input',['type'=>'text','id'=>$v,'value'=>$clr,'size'=>30,'placeholder'=>lang('color',1)],'',1).toggle('cklr|profile,sky',ico('snowflake-o')).pickbt($v).br();}//,'style'=>'background:#'.$clr.'; color:#'.$clrb,'onclick'=>'applyclr(this)'
	$ret.=div('','','cklr');
	$ret.=aj('prfl|profile,status_save|usr='.$p['pusr'].'|id,pname,status,web,gps,clr,role',langp('save'),'btsav');
	return $ret;}

static function username($p){
	$usr=val($p,'pusr',ses('user')); $name=val($p,'pname');
	if(val($p,'privacy'))$name.=ico('lock',14,'grey');
	$ret=lk('/u/'.$usr,$name,'usrnam').' '.span('@'.$usr,'grey').' ';
	return $ret;}

static function status($r){
	$rol=val($r,'role',0);
	$rol=profile::$roles[$rol];
	$ret['role']=langp($rol);
	if($web=val($r,'web'))
		$ret['site']=lk(http($web),ico('link',12).$web,'grey',1);
	$ret['gps']=self::gps($r);
	$ret['txt']=val($r,'status');
	return implode(' ',$ret);}

//gps
static function gpsav($p){$gps=val($p,'gps');
	$id=sql('id',self::$db,'v','where puid='.ses('uid'));
	sqlup(self::$db,'gps',$gps,$id);
	if($gps)$loc=gps::com(['coords'=>$gps]); else $loc='';
	sqlup(self::$db,'location',$loc,$id);
	return self::gps(['pusr'=>ses('user'),'gps'=>$gps,'location'=>$loc]);}

static function gps($r){$ret='';
	if($r['gps'] && $r['location'])
		$ret=popup('map,call|coords='.$r['gps'],pic('location').$r['location'],'grey');
	elseif($r['pusr']==ses('user'))
		$ret=btj(span(pic('location'),'','gpsloc'),'geo()','grey');
	return $ret;}

//mail_edit
static function mail_edit($p){
	if(val($p,'sav')){sqlup('login','mail',$p['mail'],ses('uid')); sez('mail',$p['mail']);}
	$mail=sql('mail','login','v',['id'=>ses('uid')]);
	$ret=input('mail',$mail,30,lang('name',1)).' ';
	$ret.=aj('prml,,z|profile,mail_edit|sav=1|mail',langp('save'),'btsav');
	return $ret;}

//notifs
static function ntfbt($p){$state=val($p,'ntf'); $sav=val($p,'sav');
	//$r=vals($r['ntf'],[0,1,2,3]);
	if($sav){$state=$state==1?'0':'1';
		sqlup(self::$db,'ntf',$state,ses('uid'),'puid');}
	if($state==0){$ic='toggle-on'; $bt='on'; $hlp=help('notifs_on','valid');}
	else{$ic='toggle-off'; $bt='off'; $hlp=help('notifs_off','alert');}
	return aj('prnt|profile,ntfbt|sav=1,ntf='.$state,ico($ic,22).lang($bt)).div($hlp);}

//privacy
static function privbt($p){$state=val($p,'privacy'); $sav=val($p,'sav');
	if($sav){$state=$state==1?'0':'1';
		sqlup(self::$db,'privacy',$state,ses('uid'),'puid');}
	if($state==1){$ic='toggle-on'; $bt='private'; $hlp=help('privacy_on','alert');}
	else{$ic='toggle-off'; $bt='public'; $hlp=help('privacy_off','valid');}
	return aj('prvc|profile,privbt|sav=1,privacy='.$state,ico($ic,22).lang($bt)).div($hlp);}

//oAuth
static function oAuthsav($p){$ret=keygen::build([]);
	if($id=val($p,'id'))sqlup(self::$db,'oAuth',$ret,$id);
	return $ret;}

static function oAuth($p){$srv=$_SERVER['HTTP_HOST'];
	$ret=span($p['oAuth'],'grey','oath').' ';
	$ret.=aj('oath|profile,oAuthsav|id='.$p['id'],langp('gen oAuth'),'btn').' ';
	$ret.=tag('h4','',lang('call timeline'));
	$ret.=div('http://'.$srv.'/api/tlex/tm='.ses('user'),'console');
	$ret.=tag('h4','',lang('call id'));
	$ret.=div('http://'.$srv.'/api/tlex/id=312','console');
	$ret.=tag('h4','',lang('post telex'));
	$ret.=div('http://'.$srv.'/api.php?oAuth='.$p['oAuth'].'&msg=hello','console');
	return $ret;}

static function modifpass($p){
	$op=val($p,'oldpsw'); $np=val($p,'newpsw');
	if($op && $np){
		$ok=sql('id','login','v','where id='.ses('uid').' and password=password("'.$op.'")');
		if($ok){
		qr('update login set password=password("'.$np.'") where id="'.ses('uid').'"');
		//update('login','password','password("'.$np.'")',ses('uid'));
			return help('new password saved');}}
	$ret=input_label('oldpsw','',lang('old password'));
	$ret.=input_label('newpsw','',lang('new password'));
	$ret.=aj('mdfp|profile,modifpass||oldpsw,newpsw',lang('save'),'btsav');
	return $ret;}

static function deleteaccount($p){$ret='';
	$prm='rmprf|profile,deleteaccount|id='.$p['id'];
	$open=sql('auth','login','v','where name="'.ses('user').'"');
	if(val($p,'confirm')){
		//sqlup('profile','privacy',2,ses('uid'),'puid');
		sqlup('login','auth',1,ses('uid'));
		return help('account disactivated');}
	elseif(val($p,'del')){$prm.=',confirm=1';
		$ret.=help('tlex_remove_account','alert').br();
		$ret.=aj($prm,langp('confirm deleting'),'btdel');}
	elseif(val($p,'restore')){
		sqlup('login','auth',2,ses('uid'));
		$ret.=aj($prm.',del=1',langp('remove account'),'btdel');}
	elseif($open==1)$ret.=aj($prm.',restore=1',langp('restore account'),'btdel');
	else $ret.=aj($prm.',del=1',langp('remove account'),'btdel');
	return div($ret,'','rmprf');}

//edit
static function edit($p){
	$cols='id,puid,pusr,pname,status,clr,avatar,banner,web,gps,location,privacy,oAuth,ntf,role';
	$r=sql($cols,self::$db,'ra','where puid='.ses('uid'),0);
	$ret=tag('h2','',lang('status'));
	$ret.=div(self::status_edit($r),'board');
	$ret.=tag('h2','',lang('banner'));
	$ret.=div(self::banner_edit($r,val($p,'big')),'board');
	$ret.=tag('h2','',lang('avatar'));
	$ret.=div(self::avatar_edit($r,val($p,'big')),'board');
	$ret.=tag('h2','',lang('location'));
	if($r['gps'])$del=aj('prfloc|profile,gpsav',pic('delete')); else $del='';
	$ret.=div(self::gps($r).$del,'board','prfloc');
	$ret.=tag('h2','',lang('mail'));
	$ret.=div(self::mail_edit($r),'board','prml');
	$ret.=tag('h2','',lang('notifications'));
	$ret.=div(self::ntfbt($r),'board','prnt');
	$ret.=tag('h2','',lang('privacy'));
	$ret.=div(self::privbt($r),'board','prvc');
	$ret.=tag('h2','','api');
	$ret.=div(self::oAuth($r),'board');
	$ret.=tag('h2','',lang('twitter api')).hlpbt('twitterapi');
	$ret.=div(app('admin_twitter'),'board');
	$ret.=tag('h2','',lang('modif password'));
	$ret.=div(self::modifpass($r),'board','mdfp');
	$ret.=tag('h2','',lang('remove account'));
	$ret.=div(self::deleteaccount($r),'board');
	return div($ret,'','');}

//build
static function datas($usr){
	$cols='puid,pusr,pname,status,clr,avatar,banner,web,gps,location,privacy,oAuth,ntf,role';
	$r=sql($cols,self::$db,'ra','where pusr="'.$usr.'"');
	if(!$r && $usr)$r=self::create($usr);
	$clr=sesr('clr',$usr);
	if(!$r['clr'])$r['clr']=sesrif('clr',$usr,$clr?$clr:self::$default_clr);
	return $r;}

static function follow($p){
	$usr=val($p,'usr'); $sm=val($p,'small'); $wait=val($p,'wait');
	if(val($p,'approve')){
		$bt=aj('tlxbck|tlxcall,follow|approve='.$usr,langp('approve'),'btsav');
		$bt.=aj('tlxbck|tlxcall,follow|refuse='.$usr,langp('refuse'),'btdel');
		$ret=div($bt,'followbt');}
	else $ret=tlex::followbt(['usr'=>$usr,'small'=>$sm,'wait'=>$wait]);
	return $ret;}

static function build($p){
	$usr=val($p,'usr'); $uid=val($p,'uid'); $wait=val($p,'wait');
	$big=val($p,'big'); $sm=val($p,'small'); $fc=val($p,'face');//modes
	$r=self::datas($usr); //pr($r);
	$ret=vals($r,['puid','pusr','role']);
	//$wait=sql('wait','tlex_ab','v','where ab="'.$usr.'"');//pending
	$ret['banner']=div(self::banner($r,$big),'banr');
	$ret['avatar']=span(self::avatar($r,$big),'','avt');
	if(ses('user') && ses('user')!=$usr)$ret['follow']=self::follow($p);
	else $ret['follow']='';
	//$ret['subscribe']=tlex::subscribt($usr,$uid,$r['role']);
	$ret['username']=self::username($r);
	if(!$fc)$ret['status']=self::status($r); else $ret['status']='';
	return $ret;}

static function small($p){$usr=val($p,'usr');
	$r=self::datas($usr);
	//$r['cntban']=tlex::avatar($r);
	$r['cntban']=div(self::username($r),'bansmall');
	$ret=self::banner($r,'');
	return $ret;}

static function standard($p){
	$r=self::build($p);
	$ret=div($r['username'].$r['status']);
	$ret=div($r['banner'].$r['avatar'].$r['follow'].$ret);//,'','prfl'
	return div($ret,'profile');}

static function big($p){$usr=val($p,'usr');
	$r=self::build(['usr'=>$usr,'big'=>'1']);
	if(ses('user')!=$usr)$subsc=div($r['follow'],'right').div('','clear'); else $subsc='';
	//$subsc.=tlex::subscribt($usr,$r['puid'],$r['role']);
	//$subsc.=$r['subscribe'];
	$ret[0]=$r['banner'].div($subsc,'subscrban');
	$ret[1]=$r['avatar'].div($r['username'].$r['status'],'pane','prfl');
	return $ret;}

//create	
static function create($usr){//$uid=ses('uid');
	$id=sql('id',self::$db,'v','where pusr="'.$usr.'"');
	$uid=sql('id','login','v','where name="'.$usr.'"');
	if(!$id && $usr && $uid==ses('uid')){
		$kg=keygen::build();
		//$clr=sesif('clr'.$usr,self::$default_clr);
		$clr=sesrif('clr',$usr,profile::$default_clr);//clrand()
		$r=['puid'=>$uid,'pusr'=>$usr,'pname'=>$usr,'status'=>'','clr'=>$clr,'avatar'=>'','banner'=>'','web'=>'','gps'=>'','location'=>'','privacy'=>0,'oAuth'=>$kg,'ntf'=>0,'role'=>0];
		$r['id']=sqlsav(self::$db,$r);
		return $r;}}

//com
static function com($usr,$o=''){
	$r=sql('pname,avatar,status,clr',self::$db,'rw','where pusr="'.$usr.'"');
	$f=self::avatar_im($r[1],'mini');
	$ret=self::divim($f,'avatarsmall',diez($r[3]));
	if($o==2)$ret.=span($r[0],'btxt');
	if(!$o)$ret.=lk('/u/'.$usr,$r[0],'btxt');
	return $ret;}

static function name($uid,$o=''){
	$ret=sql('pname',self::$db,'v','where puid="'.$uid.'"');
	if($o)$usr=sql('name','login','v','where id="'.$uid.'"');
	if($o)$ret=lk('/u/'.$usr,$ret,'btxt');
	return $ret;}

//interface
static function content($p){
	//self::install();
	$usr=val($p,'user',ses('user')); $id=val($p,'id');
	if(ses('uid'))self::create($usr);
	$ret=self::standard(['id'=>$id,'usr'=>$usr]);
	return $ret;}
}
?>
