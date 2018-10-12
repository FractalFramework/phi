<?php
session_start();
$ix=$_GET['ix']; $nb=$_GET['nb'];
if(isset($_GET['mem'])){$_SESSION['mem'][$ix][$nb]=$_GET['mem']; echo $ix.'-'.$nb;}
?>