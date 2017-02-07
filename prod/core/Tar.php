<?php

class Tar{
	
	/**
	* @param string f: file resource (provided by eg. fopen)
	* @param string fa: path to file
	* @param string fb: path to file in archive, directory names must terminate by '/'
	*/
	static function addHeader($fp,$fa,$fb){
		$info=stat($fa);
		$ouid=sprintf("%6s ",decoct($info[4]));
		$ogid=sprintf("%6s ",decoct($info[5]));
		$omode=sprintf("%6s ",decoct(fileperms($fa)));
		$omtime=sprintf("%11s",decoct(filemtime($fa)));
		if(@is_dir($fa)){$type="5";
			 $osize=sprintf("%11s ",decoct(0));}
		else{$type='';
			 $osize=sprintf("%11s ",decoct(filesize($fa)));
			 clearstatcache();}
		$dmajor=''; $dminor=''; $gname=''; $linkname=''; $magic=''; $prefix='';
		$uname=''; $version='';
		$chunkbeforeCS=pack("a100a8a8a8a12A12",$fb,$omode,$ouid,$ogid,$osize,$omtime);
		$chunkafterCS=pack("a1a100a6a2a32a32a8a8a155a12",$type,$linkname,$magic,$version,$uname,$gname,$dmajor,$dminor ,$prefix,'');
		$checksum=0;
		for($i=0; $i<148; $i++)$checksum+=ord(substr($chunkbeforeCS,$i,1));
		for($i=148; $i<156; $i++)$checksum+=ord(' ');
		for($i=156,$j=0; $i<512; $i++,$j++)$checksum+=ord(substr($chunkafterCS,$j,1));
		fwrite($fp,$chunkbeforeCS,148);
		$checksum=sprintf("%6s ",decoct($checksum));
		$bdchecksum=pack("a8",$checksum);
		fwrite($fp,$bdchecksum,8);
		fwrite($fp,$chunkafterCS,356);
		return true;
	}
	
	/**
	* Writes file content to the tar file must be called after a addHeader
	* @param fp:file resource provided by fopen
	* @param fa: path to file
	*/
	static function writeContents($fp,$fa){
		if(@is_dir($fa))return;
		else{
			$size=filesize($fa);
			$padding=$size % 512 ? 512-$size%512 : 0;
			$f2=fopen($fa,"rb");
			while(!feof($f2))fwrite($fp,fread($f2,1024*1024));
			$pstr=sprintf("a%d",$padding);
			fwrite($fp,pack($pstr,''));
		}
	}
	
	/**
	* Adds 1024 byte footer at the end of the tar file
	* @param f: file resource
	*/
	static function addFooter($f){fwrite($f,pack('a1024',''));}
	
	static function tarFile($fp,$f){
	self::addHeader($fp,$f,$f); self::writeContents($fp,$f);}
	
	static function tarDir($f,$r){$fp=fopen($f,'w+');
	foreach($r as $v)self::tarFile($fp,$v);
	self::addFooter($fp);
	fclose($fp);}
	
	static function read($dr,$o=''){
	$fp=opendir($dr); static $i; $ret=array();
	while($d=readdir($fp)){$drb=$dr.'/'.$d; $i++;
	if(is_dir($drb) && $d!='..' && $d!='.' && $d!='_notes' && !$o)
		$ret=array_merge($ret,self::read($drb));
	elseif(is_file($drb) && $d!='.php')$ret[$i]=$drb;}
	return $ret;}
	
	static function gz($f){$s=file_get_contents($f); 
	$ok=file_put_contents($f.'.gz',gzencode($s,9));
	if($ok)unlink($f);
	return $f.'.gz';}
	
	static function buildFromDir($f,$dir){
	$r=self::read($dir);
	self::tarDir($f,$r);
	$f=self::gz($f);
	return $f;}
	
	static function buildFromList($f,$list){
	if(!is_array($list))return;
	$fp=fopen($f,'w+');
	foreach($list as $v){
		if(is_dir($v)){$r=self::read($v);
			foreach($r as $vb)self::tarFile($fp,$vb);}
		elseif(is_file($v))self::tarFile($fp,$v);}
	self::addFooter($fp);
	fclose($fp);
	$f=self::gz($f);
	return $f;}
}

?>