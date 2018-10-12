<?php
session_start();
require('boot.php'); //print_r($_GET);
//api/app/a:1,b:2
if($app=get('app')){$p=get('p');
	if(is_numeric($p))$prm['id']=$p; elseif(strpos($p,'='))$prm=_jrb($p,'='); else $prm=$p;
	if(isset($prm['mth']) && method_exists($app,$prm['mth']))$ret=$app::$prm['mth']($prm);
		//$ret=app($app,$prm['p'],$prm['mth']);
	elseif(method_exists($app,'api')){$a=new $app(); $ret=$a::api($prm);}}
elseif($app=get('frame')){$p=get('p');
	if(is_numeric($p))$prm['id']=$p; elseif(strpos($p,'='))$prm=_jrb($p,'='); else $prm=$p;
	if(method_exists($app,'iframe')){$a=new $app(); $ret=$a::iframe($prm);}}
//api.php?oAuth=xxx&msg=hello
elseif($oAuth=get('oAuth'))$ret=tlxcall::post(['oAuth'=>$oAuth]);
echo $ret;
?>