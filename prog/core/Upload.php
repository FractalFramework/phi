<?php 

class upload{

/**/static function closebt($ret,$f,$rid){
$cl=btj(ico('close'),atj('closebt',[$f,$rid]));
return span($ret.$cl,'icones','bt'.$rid);}

static function goodir($xt){
if(stristr('.m4a.mpg.mp4.wmv.mov',$xt)!==false)$dir='video';
elseif(stristr('.rar.zip.tar.gz',$xt)!==false)$dir='archive';
elseif(stristr('.txt.docx',$xt)!==false)$dir='word';
elseif(stristr('.pdf',$xt)!==false)$dir='pdf';
elseif(stristr('.jpg.png.gif',$xt)!==false)$dir='img';
elseif(stristr('.mp3.mid',$xt)!==false)$dir='audio'; 
return $dir;}

static function progress($p){$rid=substr($p['rid'],0,-2); //pr($_FILES); p($p);
echo $sz=filesize($_FILES[$rid]['tmp_name']);}

static function save($p){$error=''; $rid='upfile'.val($p,'rid'); $ty=val($p,'ty'); 
$f=$_FILES[$rid]['name']; $f_tmp=$_FILES[$rid]['tmp_name'];
if(!$f)return 'no file uploaded ';
$xt=ext($f); $f=normalize(before($f,'.'));
$goodxt='.mp4.m4a.mov.mpg.mp3.wav.wmv.swf.flv.jpg.png.gif.pdf.txt.docx.rar.zip.tar.gz.mid';
if(stristr($goodxt,$xt)===false)$error=$xt.'=forbidden; authorized='.$goodxt.br();
$fsize=$_FILES[$rid]['size']/1024; $uplimit=250000;
if($fsize>=$uplimit || $fsize==0)$error.=$fsize.'<250Mo ';
if(!$ty)$ty=self::goodir($xt);
if($ty=='img')$dir='img/full/'; else $dir='disk/'.$ty.'/';
if(!is_dir($dir))mkdir_r($dir);
//$fa=substr(md5($f),0,8).$xt;//defined by js
$fa=$f.$xt; $fb=$dir.$fa;
if(is_uploaded_file($f_tmp) && !$error){
	if(!move_uploaded_file($f_tmp,$fb))$error.='not saved';
	else{$ico=$ty=='img'?'file-image-o':'file-'.$ty.'-o';
		tlxcall::keepsave(['p1'=>$fa,'com'=>$ty,'tit'=>$f]);}
	//if($xt=='.tar' or $xt=='.gz')unpack_gz($fb,$rep);
}
else $error.='upload refused: '.$fa;
if($error)$ret=div($error,'alert');
elseif($ty=='img'){files::mkthumb($fa,590); $ret=img('/img/mini/'.$fa,72,72);}
elseif($ty=='audio')$ret=ico('file-audio-o',24).$fa;
elseif($ty=='video')$ret=ico('file-video-o',24).$fa;
//if(val($p,'getinp'))$ret=$fb;
$ret=self::closebt($ret,$f,$p['rid']);
return $ret;}

static function call($rid,$o=''){
return '<form id="upl'.$rid.'" action="" method="POST" onchange="upload(\''.$rid.'\',\''.ses('user').'\')"><label class="uplabel"><input type="file" id="upfile'.$rid.'" name="upfile'.$rid.'" multiple />'.ico('upload').'</label></form>'.($o?'':span('','',$rid.'up'));}
	
}
?>