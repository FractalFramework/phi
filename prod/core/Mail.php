<?php

class mail{

static function html($to,$subject,$msg,$from){ $n="\r\n";//PHP_EOL
$enc=ses('enc')?'utf8':'iso-8859-1';//according to cfng
$msg='<html><head><title>'.$subject.'</title>
<link href="http://'.$_SERVER['HTTP_HOST'].'/css/global.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset='.$enc.'"></head><body>
'.tag('div',['id'=>'page','style'=>'margin:20px;'],$msg).'
<br><br></body></html>';
$head='MIME-Version: 1.0'.$n;//text/html
$head.='Content-Type:multipart/alternative; charset="'.$enc.'" boundary="-----='.md5(rand()).'"';//
$head='To: '.$to.$n;
$head='From: '.$from.$n;
$head.='Reply-To: '.$from.$n;
$head.='Date: '.date("D, j M Y H:i:s").$n;
$head.='X-mailer: PHP/' . phpversion();
$subject=html_entity_decode($subject); $msg=html_entity_decode($msg);
$subject=decode($subject); $msg=decode($msg);
$ok=mail($to,$subject,$msg,$head);
if($ok)return 'mail_sent'; else return 'mail_fail';}

static function txt($to,$subject,$msg,$from){
$subject=html_entity_decode($subject); 
$head='From: '.$from."\n";
$msg="\n\n".$msg."\n\n";
$subject=html_entity_decode($subject); $msg=html_entity_decode($msg);
$subject=decode($subject); $msg=decode($msg);
$ok=mail($to,$subject,$msg,$head);
if($ok)return 'mail_sent'; else return 'mail_fail';}

static function send($to,$subject,$msg,$from,$format=''){
if($format=='html')return self::html($to,$subject,$msg,$from);
else return self::txt($to,$subject,$msg,$from);}
}