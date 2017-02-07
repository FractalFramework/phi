<?php

class Img{

	//force LH, cut and center
	static function scale($w,$h,$wo,$ho,$s){$hx=$wo/$w; $hy=$ho/$h; $yb=0; $xb=0;
		if($s==2){$xb=($wo/2)-($w/2); $yb=($ho/2)-($h/2); $wo=$w; $ho=$h;}
		elseif($hy<$hx && $s){$xb=0; $yb=($ho-($h*$hx))/2; $ho=$ho/($hy/$hx);}//reduce_h
		elseif($hy>$hx && $s){$xb=($wo-($w*$hy))/2; $wo=$wo/($hx/$hy);}//reduce_w
		elseif($hy<$hx){$xb=($wo-($w*$hy))/2; $wo=$wo/($hx/$hy);}//adapt_h
		elseif($hy && $hx){$xb=0; $ho=$ho/($hy/$hx);}//adapt_w
	return array($w,$h,$wo,$ho,$xb,$yb);}
	
	static function thumb($in,$out,$w,$h,$s){$xa=0; $ya=0;
		$w=$w?$w:170; $h=$h?$h:100; list($wo,$ho,$ty)=getimagesize($in); 
		list($w,$h,$wo,$ho,$xb,$yb)=self::scale($w,$h,$wo,$ho,$s);
		if(is_file($in))if(filesize($in)/1024>5000)return;
		$img=imagecreatetruecolor($w,$h);
		if($ty==2){$im=imagecreatefromjpeg($in);
			imagecopyresampled($img,$im,$xa,$ya,$xb,$yb,$w,$h,$wo,$ho);
			imagejpeg($img,$out,100);}
		elseif($ty==1){$im=imagecreatefromgif($in); self::imgalpha($img);
			imagecopyresampled($img,$im,$xa,$ya,$xb,$yb,$w,$h,$wo,$ho);
			imagegif($img,$out);}
		elseif($ty==3){$im=imagecreatefrompng($in); self::imgalpha($img);
			imagecopyresampled($img,$im,$xa,$ya,$xb,$yb,$w,$h,$wo,$ho);
			imagepng($img,$out);}
	return $out;}
	
	static function imgalpha($img){//imagefilledrectangle($im,0,0,$w,$h,$wh);
		$c=imagecolorallocate($img,255,255,255); imagecolortransparent($img,$c);
		imagealphablending($img,false); 
		imagesavealpha($img,true);}
	
	static function hexrgb_r($d){
		for($i=0;$i<3;$i++)$r[]=hexdec(substr($d,$i*2,2)); return $r;}
	
	static function hexrgb($d,$o=''){$r=self::hexrgb_r($d);
		return 'rgba('.$r[0].','.$r[1].','.$r[2].','.$o.')';}
	
	static function read($p){
		return img('/'.val($p,'f'));}
	
}