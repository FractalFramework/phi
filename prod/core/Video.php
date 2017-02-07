<?php 

class Video{

static function provider($f){
$f=nohttp($f); $fa=domain($f); $fb=substr($fa,0,strpos($fa,'.'));
$r=array('youtube','youtu','dailymotion','vimeo','rutube');
if(in_array($fb,$r)!==false)return $fb;}

static function extractid($f,$fb){switch($fb){
	case('youtube')://if(strpos($f,'channel')!==false)return http($f);
	$p=strpos($f,'v='); $f=substr($f,$p+2); $pe=strpos($f,'&');
		if($pe!==false)$ret=substr($f,0,$pe); else $ret=$f; break;
	case('youtu'):$p=strpos($f,'/'); $f=substr($f,$p+1); $pe=strpos($f,'?');
		if($pe!==false)$ret=substr($f,0,$pe); else $ret=$f; break;
	case('dailymotion'):$ret=segment($f,'video/','-');
		if(!$ret)$ret=substr($f,strpos($f,'video/')+6); break;
	case('vimeo'):$ret=substr($f,strrpos($f,'/')+1); break;
	case('rutube'):$ret=segment($f,'tracks/','.'); break;}
if(isset($ret))return $ret;}

static function player($f,$p){$w='640px'; $h='400px';
	if($p=='youtube')return iframe('http://www.youtube.com/embed/'.$f.'?border=0&version=3&autohide=1&showinfo=0&rel=0&fs=1',$w,$h);
	elseif($p=='daily')return iframe('http://www.dailymotion.com/embed/video/'.$f,$w,$h);
	elseif($p=='vimeo'){return iframe('http://player.vimeo.com/video/'.$f,$w,$h);}
	elseif($p=='rutube')return '<embed src="http://video.rutube.ru/'.$f.'" type="application/x-shockwave-flash" wmode="window" width="100%" height="auto" allowFullScreen="true">';
	elseif(strpos($f,'.mp4'))return video($f);}

static function call($prm){
	$p=val($prm,'p'); $id=val($prm,'id');
	if($p && $id)return self::player($id,$p);}

static function mkconn($f){
	$p=self::provider($f); if($p)$id=self::extractid($f,$p);
	if($p && $id)return '['.$id.'*'.$p.':video]';}

static function bt($f){
	$p=self::provider($f); $id=self::extractid($f,$p);
	if($p && $id)return Ajax::j('popup|Video,call|p='.$p.',id='.$id,picto('video').' '.$p,'btn');
	else return href($f,domain($f)."&nbsp;".picto('get'),'btxt','',1);}

static function play($f){
	$p=self::provider($f);
	$id=self::extractid($f,$p);
	if($p)return self::player($id,$p);}

}
?>