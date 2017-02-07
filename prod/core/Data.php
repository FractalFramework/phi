<?php

class Data{
	
	static function file($root){
		return 'db/'.$root.'.txt';
	}
	
	static function makeDirs($root){
		$dirs=explode('/',$root); $rep='';
		if(count($dirs)>10)return;
		foreach($dirs as $k=>$v){
			$rep.=$v.'/'; 
			if(strpos($v,'.'))$v=''; 
			if(!is_dir($rep) && $v){
				if(!mkdir($rep))
					echo $v.':not_created ';
			}
		}
	}
	
	static function del($root){
		if(is_file($f=self::file($root)))unlink($f); //echo $f;
	}
	
	static function init($root){
		$dir=before($root,'/');
		$file=self::file($root);
		if(!is_dir('db/'.$dir))
			self::makeDirs($file);
		if(!is_file($file))
			file_put_contents($file,'');
	}
	
	static function valueFromKey($root,$key){
		$datas=self::read($root); //pr($datas);
		if(isset($datas[$key]))return $datas[$key];
	}
	
	static function filters($root,$filters){
		$datas=self::read($root);
		if(isset($filter['sort']))sort($datas);
		return $datas;
	}
	
	static function read($root,$create='',$conn=''){
		if($create)self::init($root);
		$f=self::file($root); if(!is_file($f))return;
		$base=file_get_contents($f);
		if($conn)$datas=stringToArray($base,'¬','|');
		else $datas=json_decode(utf8_decode($base),true);
		return $datas;
	}
	
	static function write($root,$datas,$conn=''){
		self::init($root);
		if($conn)$base=arrayToString(str_replace('|','',$datas),'¬','|');
		//else $base=json_encode($datas);//,JSON_PRETTY_PRINT,JSON_UNESCAPED_UNICODE
		else $base=json_r($datas);
		file_put_contents(self::file($root),$base);//FILE_APPEND | LOCK_EX
	}
	
	static function add($root,$row){
		self::init($root);
		$datas=self::read($root);
		$datas[]=$row;
		self::write($root,$datas);
	}
	
}