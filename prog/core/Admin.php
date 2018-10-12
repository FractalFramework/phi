<?php
class admin{

//profile
static function badger($p){
$r=sql('name','login','rv','where mail="'.ses('mail').'" and auth>1 order by name');//
foreach($r as $v){//$rb[]=aj('bdg|admin,badger_switch|usr='.$v,$v,'');
	$rb[$v]=aj('reload,bdg,loged_ok|login,badger|user='.$v,$v,'');} ksort($rb,SORT_NATURAL);
$ret=div(implode('',$rb),'');
if($usr=val($p,'usr'))$ret.=password('psw','').aj('|login',lang('login'),'btsav');
$ret.=div('','','bdg');
return $ret;}

static function login(){
$r[]=['','pop','login,com','user','login'];
//$r[]=['login','in','login,com|auth=2','user','login'];
$rb=lngs();//['en','es','fr'];
foreach($rb as $k=>$v)$r[]=['lang','j','returnVar,lng,reload|core,lang_set|lang='.$v,'flag',$v];
return $r;}

//$r[]=['root','mode','app','ico','name'];//modes : ['','pop','j','lk','in']
static function profile($p){$a=val($p,'a');
$usr=ses('user')?ses('user'):'profile'; $dev=ses('dev');
if($a!=$_SESSION['index']){$r[]=['','lk','/','home',''];
	$r[]=['','lk','/'.$a,'',$a];}
$r[]=[$usr,'j','popup|profile,edit','','edit profile'];
$n=sql('count(id)','login','v','where mail="'.ses('mail').'" and auth>1');
if($n>1)$r[]=[$usr.'/badger','in','admin,badger','','badger'];
$rb=lngs();//['en','es','fr'];
foreach($rb as $k=>$v)$r[]=[$usr.'/lang','j','returnVar,lng,reload|core,lang_set|lang='.$v,'flag',$v];
if(auth(6)){
	if($dev=='prod')$r[]=[$dev.'','j','ses,,reload||k=dev,v=prog','','prog'];
	else $r[]=[$dev.'','j','ses,,reload||k=dev,v=prod','','prod'];}
$r[]=[$usr,'pop','desktop|dir=/documents','','desktop'];
$r[]=[$usr.'/utils','','pad','','notes'];
$r[]=[$usr.'/utils','','convert','','convert'];
$r[]=[$usr.'/utils','','tickets','','tickets'];
$r[]=[$usr.'/utils/loadapp','in','loadapp,com','','loadapp'];
/*$r[]=[$usr.'/tlex','pop','art|id=6','tlex','welcome'];
$r[]=[$usr.'/tlex','pop','applist,tlex|','art','list of apps'];
$r[]=[$usr.'/tlex','pop','contact','','contact'];*/
if(auth(6)){
	$r[]=[$dev.'','pop','admin_lang','','lang'];
	$r[]=[$dev.'','pop','admin_icons','','pictos'];
	$r[]=[$dev.'','pop','admin_help','help','helps'];
	$r[]=[$dev.'/doc','pop','admin_labels','','labels'];
	$r[]=[$dev.'/doc','pop','admin_conn','','conn'];
	$r[]=[$dev.'/doc','pop','admin_lib','','lib'];
	$r[]=[$dev.'/doc','pop','admin_sys','','sys'];
	//if(nohttp($_SERVER['HTTP_HOST'])!='tlex.fr')
	$r[]=[$dev.'/doc','pop','update,loaddl','','update'];
	$r[]=[$dev.'','pop','devnote','','devnote'];
	$r[]=[$dev.'','j','popup,,xx|dev2prod','','push'];
	$r[]=[$dev.'','lk','?reset==','','reboot'];}
$r[]=[$usr,'j',',,reload|login,disconnect','','logout'];
//$r[]=['about','pop','core,help|ref=tlex,conn=1','tlex','welcome'];
//$r[]=['about','pop','core,help|ref=features,conn=1','art','features'];
$r[]=['about','pop','applist,tlex','art','applist'];
$r[]=['about','pop','core,help|ref=confidentiality,conn=1','info','confidentiality'];
$r[]=['about','pop','core,help|ref=developpers,conn=1','art','developpers'];
$r[]=['about','pop','devnote','','devnote'];
$r[]=['about','pop','contact','','contact'];
$r[]=['about','pop','core,help|ref=credits,conn=1','info','credits'];
$r[]=['about','pop','paypal','money','donations'];
return $r;}

//com
static function com(){
$keys='id,dir,type,com,picto,bt';// or auth=0 
$r=sql($keys,'desktop','id','where uid="'.ses('uid').'" or dir="/apps/tlex" order by dir');
if(is_array($r))foreach($r as $k=>$v)$r[$k][0]='root'.$r[$k][0];//add root
return $r;}

#content
static function content($p){$ret='';
$app=val($p,'app'); ses('app',$app); $own=ses('user');
$usr=val($p,'usr'); $id=val($p,'id',val($p,'th'));
if(is_numeric($usr)){$id=$usr; $usr='';}
//$ret=lk(host(),ico('star'),'btn abbt');//nav
$ret=menu::call(['app'=>'admin','method'=>$own?'profile':'login','drop'=>1,'a'=>$app]);
//if($app!='tlex')$ret.=lk('/'.$app,langp($app),'btn abbt');
//if($app && method_exists($app,'admin_bt'))$ret.=$app::admin_bt($usr); else 
if($app && method_exists($app,'admin'))$ret.=menu::call(['app'=>$app,'method'=>'admin','drop'=>1]);
return div($ret,'tpbl').div('','adminheight');}
}
?>