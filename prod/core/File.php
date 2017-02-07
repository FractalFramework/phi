<?php

class File{
	
	static function write($f,$content){
		$h=fopen($f,'w+'); $w=fwrite($h,$content); fclose($h);
		if($w===false)return 'error';}
		
	static function read($f){
		if($f)$fp=fopen($f,'r'); $ret='';
		if(isset($fp)){while(!feof($fp))$ret.=fread($fp,8192); fclose($fp);}
		return $ret;}

	static function get($f){
		ini_set('user_agent','Mozilla/5.0');
		$r=array('http'=>array('method'=>'GET','header'=>'User-agent: Mozilla/5.0','ignore_errors'=>1,'request_fulluri'=>true,'max_redirects'=>0));
		$context=stream_context_create($r);
		$h=get_headers($f,false);//$http_response_header
		if(strpos($h[0],'404'))return '404';
		return file_get_contents($f,false,$context);}
		
	static function curl($f){$ch=curl_init($f); //curl_setopt($ch,CURLOPT_URL,$f);
		$r=array('HTTP_ACCEPT: Something','HTTP_ACCEPT_LANGUAGE: fr, en, es','HTTP_CONNECTION: Something','Content-type: application/x-www-form-urlencoded','User-agent: Mozilla/5.0');
		curl_setopt($ch,CURLOPT_HTTPHEADER,$r);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_REFERER,'http://www.google.fr/');
		$ret=curl_exec($ch); curl_close($ch); return $ret;}
	
	static function gz($f,$fb){
		$w=self::write($fb,implode('',gzfile($f)));
		if($w===false)return 'error';}
	static function gunz($f,$d){return readgzfile($f);}
	
	static function writegz($f,$d){$gz=gzopen($f,'w9');
		gzwrite($gz,$d); return gzclose($gz);}
	static function readgz($f,$d){$zd=gzopen($f,'r');
		$d=gzread($zd,10000); gzclose($zd); return $d;}
	
	static function day($f,$format='ymd.His'){
		if(is_file($f))return date($format,filemtime($f));}
	static function size($f,$dateFormat=''){
		if(is_file($f))return round(filesize($f)/1024,1).' Ko';}
	
	static function fsize($params){
		$f=$params['fileRoot'];
		if(is_file($f))return self::size($f,'');
		else return 'file not found: '.$f;}
	static function fdate($params){
		$f=$params['fileRoot']; $format=val($params,'format','Ymd');
		if(is_file($f))return self::day($f,$format);
		else return 'file not found: '.$f;}
	static function readTxt($params){
		$ret=self::read($params['fileRoot']);
		return tag('pre','',$ret);}
	
	static function mkthumb($nm,$w,$h=''){if(!$h)$h=$w;
		$fa='img/full/'.$nm; //Dir::mkdir_r($fa);
		$fb='img/mini/'.$nm; Dir::mkdir_r($fb);
		$fc='img/medium/'.$nm; Dir::mkdir_r($fc);
		Img::thumb($fa,$fb,170,170,0);
		list($wa,$ha)=getimagesize($fa);
		if($wa>$w or $ha>$h)Img::thumb($fa,$fc,$w,$h,0);}
	
	/*static function saveimg($f,$dir,$w,$h=''){$er=1;
		if(substr($f,0,4)!='http')return;
		$xt=extension($f); if(!$xt)$xt='.jpg';
		$nm='tlx'.substr(md5($f),0,10); $h=$h?$h:$w;
		$fa='img/'.$dir.'/full/'.$nm.$xt; Dir::mkdir_r($fa);
		$ok=@copy($f,$fa);
		if(!$ok){$d=@file_get_contents($f); if($d)$er=File::write($fa,$d);}
		if($ok or !$er){if(is_file($fa))self::mkthumb($nm.$xt,$dir,$w,$h);
			return $nm.$xt;}}*/
	
	static function saveimg($f,$prf,$w,$h=''){$er=1;
		if(substr($f,0,4)!='http')return;
		$xt=extension($f); if(!$xt)$xt='.jpg';
		$nm=$prf.substr(md5($f),0,10); $h=$h?$h:$w;
		$fa='img/full/'.$nm.$xt; Dir::mkdir_r($fa);
		$ok=@copy($f,$fa);
		if(!$ok){$d=@file_get_contents($f); if($d)$er=File::write($fa,$d);}
		if($ok or !$er){if(is_file($fa))self::mkthumb($nm.$xt,$w,$h);
			return $nm.$xt;}}
}

?>