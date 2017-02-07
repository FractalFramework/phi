<?php
//telex@2016
//http://opensource.org/licenses/gpl-license.php GNU Public License
session_start();
require('boot.php');
chrono('');
#inputs
$app=get('app');
if(!$app)$app=$_SESSION['index'];
sez('app',$app);
#@url: /app/model/p1=v1/p2=v2
$params=_jrb(get('params'),1);
sez('applng',$app);
Lang::$app=$app;//context for Lang
require($_SESSION['dev'].'/index.php');
?>