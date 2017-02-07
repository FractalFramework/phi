<?php
class Mail{

	static function mailHtml($subject,$msg){
		$http=$_SERVER['HTTP_HOST'];
		$css='css/global.css';
		return '<html><head>
		<title>'.$subject.'</title>
		<link href="http://'.$http.'/css/global.css" rel="stylesheet" type="text/css">
		</head><body>
		'.tag('div',array('style'=>'margin:10px;'),$msg).'
		<br><br></body></html>';}
		
	static function sendHtml($to,$subject,$msg,$from){
		$msg=self::mailHtml($subject,$msg); $n="\n";
		$date=date("D, j M Y H:i:s");
		$head='From: '.$from.'
		Cc:
		Bcc:
		Reply-To:'.$from.'
		Date:'.date("D, j M Y H:i:s").'
		Content-Type:multipart/alternative;
		charset="utf-8"
		boundary="-----='.md5(rand()).'"';
		$ok=mail($to,$subject,$msg,$head);
		if($ok)return 'mail_sent'; else return 'mail_fail';}
		
	static function sendText($to,$subject,$msg,$from){
		$subject=html_entity_decode($subject); 
		$head='From: '.$from."\n"; $msg="\n\n".$msg."\n\n";
		$ok=mail($to,$subject,$msg,$head);
		if($ok)return 'mail_sent'; else return 'mail_fail';}
		
	static function send($to,$subject,$msg,$from,$format=''){
		if($format=='html')return self::sendHtml($to,$subject,$msg,$from);
		else return self::sendText($to,$subject,$msg,$from);}
	
}