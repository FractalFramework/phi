<?php
class Mail{

	static function mailHtml($subject,$msg){
		//$msg=wordwrap($msg,70,"\r\n");
		return '<html><head>
		<title>'.$subject.'</title>
		<link href="http://'.$_SERVER['HTTP_HOST'].'/css/global.css" rel="stylesheet" type="text/css">
		</head><body>
		'.tag('div',array('style'=>'margin:10px;'),$msg).'
		<br><br></body></html>';}
		
	static function sendHtml($to,$subject,$msg,$from){
		$msg=self::mailHtml($subject,$msg); $n="\n";//PHP_EOL
		$head='From: '.$from.$n;
		$head.='Reply-To: '.$from.$n;
		$head.='Date: '.date("D, j M Y H:i:s").$n;
		$head.='X-Mailer: PHP/' . phpversion();
		$head.='Content-Type:multipart/alternative; charset="'.ses('enc').'" boundary="-----='.md5(rand()).'"';
		$subject=html_entity_decode($subject); $msg=html_entity_decode($msg);
		$subject=decode($subject); $msg=decode($msg);
		$ok=mail($to,$subject,$msg,$head);
		if($ok)return 'mail_sent'; else return 'mail_fail';}
		
	static function sendText($to,$subject,$msg,$from){
		$subject=html_entity_decode($subject); 
		$head='From: '.$from."\n";
		$msg="\n\n".$msg."\n\n";
		$subject=html_entity_decode($subject); $msg=html_entity_decode($msg);
		$subject=decode($subject); $msg=decode($msg);
		$ok=mail($to,$subject,$msg,$head);
		if($ok)return 'mail_sent'; else return 'mail_fail';}
		
	static function send($to,$subject,$msg,$from,$format=''){
		if($format=='html')return self::sendHtml($to,$subject,$msg,$from);
		else return self::sendText($to,$subject,$msg,$from);}
	
}