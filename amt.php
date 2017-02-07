<?php
session_start();
if(isset($_GET['mem'])){$_SESSION['mem'][$_GET['nb']]=$_GET['mem']; echo $_GET['nb'];}
?>