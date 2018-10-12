<?php
#fractalframework/tlex
//setlocale(LC_ALL,'fr_FR.utf8');//kill rgba ability?
if(isset($p['api']) or $noadmin)$admin='';
else $admin=app('admin',['app'=>$app]);
$enc=ses('enc')?'utf8':'iso-8859-1';//according to cfng
//add_head('code','<base'.atb('href',$_SERVER['HTTP_HOST']).' />');
add_head('meta',['attr'=>'http-equiv','prop'=>'Content-Type','content'=>'text/html; charset='.$enc]);
add_head('tag',['title','',lang('Tlex').($app?'-'.$app:'')]);
add_head('rel',['name'=>'shortcut icon','value'=>'/favicon.ico']);
add_name('generator','fractalframework');
add_name('version','1704');
add_name('viewport','user-scalable=no, initial-scale=1, width=device-width');
add_head('csslink','/css/global.css');
add_head('csslink','/css/apps.css');
add_head('csslink','/css/pictos.css');
add_head('csslink','/css/fa.css');
add_head('jslink','/js/ajax.js');
add_head('jslink','/js/utils.js');
//add_head('jslink','/js/bab.js');

/**/
$own=ses('user'); $usr=val($p,'usr',$own);
$clr=sesr('clr',$usr?$usr:$own);
if(!$clr && $usr)$clr=profile::init_clr(['usr'=>$usr?$usr:$own]);
if(substr($clr,0,1)=='-'){$c=substr($clr,1); $sky=ses('sky'.$c);
	if(!$sky)$sky=ses('sky'.$c,sql('css','sky','v','where tit="'.$c.'"'));
	$bclr='background-image:'.$sky.';';}
else{
	$hex=$clr?hexrgb($clr,0.7):'rgba(119,0,119,0.7)';
	$clr0=clrneg($clr,1); $hex0='rgba(0,0,0,0.15)';//$clr?hexrgb($clr0,0.1):
	$clr1=clrb($clr,-100); $hex1='rgba(0,0,0,0)';//$clr?hexrgb($clr1,0.2):
	$clr2=clrb($clr,100); $hex2='rgba(0,0,0,0)';//$clr?hexrgb($clr2,0.2):
	$bclr='background-color:#'.$clr.'; color:'.$clr0.';';//#'.$clr.'
	$bclr.='background-image:
	linear-gradient(to bottom,rgba(0,0,0,0),'.$hex0.'),
	linear-gradient(to left,rgba(0,0,0,0),'.$hex1.'),
	linear-gradient(to right,rgba(0,0,0,0),'.$hex2.');
	a.bicon {color:#'.$clr0.';}
	a.bicon .pic{color:#'.$clr0.';}';}

if(get('popup'))add_head('csscode','.container{'.$bclr.'}');
else add_head('csscode','body{'.$bclr.'}');
//.pane,.paneb,.panec,.paned{color:#'.$clr0.' background-color:'.$hex0.';}
//.lisb a, .lisb a:hover,.lisb .pic{color:#'.$clr0.';}

if(!ses('updated') && auth(6))app('upgrade','');
#content
$content=app($app,$p);
stats::save($app,$p);
#render
$ret=generate();
$ret.='<body onmousemove="popslide(event)" onmouseup="closebub(event)">'."\n";//
$ret.=tag('div',['id'=>'closebub','onclick'=>'bubClose()'],'');
$ret.=tag('div',['id'=>'admin'],$admin);
$ret.=tag('div',['id'=>'page'],$content);
$ret.=tag('div',['id'=>'popup'],'');
if(auth(6))$ret.=div(round(array_sum(explode(' ',microtime()))-$start,5),'chrono');
$ret.='</body>';
echo $ret;
?>