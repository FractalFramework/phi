<?php
//fractalframework@2017
//http://opensource.org/licenses/gpl-license.php
session_start();
$start=array_sum(explode(' ',microtime()));
require('boot.php');
$app=get('app');
if(!$app)$app=$_SESSION['index'];
sez('app',$app);
#/app/p1=v1,p2=v2
$p=_jrb(get('p'));
sez('applng',$app);
require($_SESSION['dev'].'/index.php');
//Sql::close();
sqlclose();
?>