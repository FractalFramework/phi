<?php
#Fractal license GNU/GPL
session_start();
require('boot.php');
$app=get('appName');
$mth=get('appMethod');
$p=_jrb(get('params'));
$p['appName']=$app;
$p['appMethod']=$mth;
if(isset($p['verbose']))pr($p);
#request
$content=app($app,$p);
//$a=new $app; $content=$a->$mth($p);
#render
$ret=build_head();
if(get('popup'))$ret.=build::popup($content,$p);
elseif(get('pagup'))$ret.=build::pagup($content,$p);
elseif(get('imgup'))$ret.=build::imgup($content);
elseif(get('bubble'))$ret.=build::bubble($content);
elseif(get('menu'))$ret.=build::menu($content);
elseif(get('drop'))$ret.=build::menu($content);
elseif(get('ses'))sez($p['k'],$p['v']);
else $ret.=$content;
echo $ret;
sqlclose();
?>