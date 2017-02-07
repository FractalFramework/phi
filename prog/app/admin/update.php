<?php

class update{
	//used by system
	static $private='6';
	static $servr='tlex.fr';
	
	static function echo_r($r,$o=''){$ret=[];
		foreach($r as $k=>$v)if(is_array($v))$ret[]=self::echo_r($v,$o); else $ret[]=$v;
	return implode($o,$ret);}
	
	static function dirs(){$r=['prod','index.php','call.php','amt.php','api.php','cfng/site.com.php','cfng/_twitter_oAuth.php','htaccess.txt','favicon.ico','readme.txt','releases.txt'];//,'icon'
	return $r;}
	
	static function mk_r($d){
		$r=explode(';',$d);
		foreach($r as $v){
			list($f,$date)=explode(':',$v);
			$ret[$f]=$date;}
	return $ret;}
	
	#get dates (local or distant, following p)
	static function w_date($dr,$f){
		$fb=($dr?$dr.'/':'').$f;
		return $fb.':'.date('ymd.His',filemtime($fb));}
	
	static function localfdates($p=''){
		$ret=array();
		if($p==1)$r=['usr/dl']; else $r=self::dirs();
		foreach($r as $v){
			if(is_dir($v))$ret[$v]=Dir::walk('update','w_date',$v);
			elseif(is_file($v)){
				$dr=before($v,'/'); $f=after($v,'/');
				$ret[$v]=self::w_date('',$v);
			}
		}
	if(isset($ret))return self::echo_r($ret,';');}
	
	#create gz (server)
	static function creategz(){//$ret='';
		Dir::mkdir_r('usr/dl');
		$local=self::localfdates();
		$r=self::mk_r($local);
		foreach($r as $f=>$dt){$ok=1;
			$fb='usr/dl/'.str_replace('/','-',$f).'.txt';
			$gz=File::day($fb);
			if($dt>$gz)$ok=File::write($fb,implode('',file($f)));
			if(!$ok)$ret[]='ok: '.$f.':'.($dt-$gz);
			else $ret[]='no: '.$f;}
	return implode(br(),$ret);}
	
	#load dl (client)
	static function w_dl($f,$fb){//fb: files of usr archive
		$d=@file_get_contents('http://'.self::$servr.'/'.$fb);
		$f=str_replace('prod/','prog/',$f);
		if($_SERVER['HTTP_HOST']!=self::$servr)
			if($d)if(auth(6))$ok=File::write($f,$d);
		if(isset($ok))return 'ok:'.$f;}
	
	//build list of files to dl
	static function files2dl(){
		$ret=array();
		//local
		$d=self::localfdates();
		$local=self::mk_r($d); //pr($local);
		//distant
		$f='http://'.self::$servr.'/api.php?app=update&mth=localfdates&p=1';//dates of archives
		$d=File::read($f);
		$distant=self::mk_r($d); //pr($distant);
		//$n1=count($local); $n2=count($distant);
		if($distant)foreach($distant as $k=>$v){
			$kb=substr(str_replace('-','/',$k),7,-4);//original name
			if($kb){
				//changing this can kill updates ability of client
				if(array_key_exists($kb,$local)){if($v>$local[$kb])$ret[$kb]=$k;}
				else $ret[$kb]=$k;
			}
		}
	return $ret;}
	
	//dl
	static function loaddl(){$rb=[];
		//select recents
		$r=self::files2dl(); //pr($r);
		if($r)foreach($r as $k=>$v){$ok=self::w_dl($k,$v); if(!$ok)$rb[]=$k;}
		return count($rb).' files updated'.hr().self::echo_r($rb,br());}
	
	#interface
	static function content($p){
		$f=val($p,'f'); $ret='';
		if(auth(1))$ret=aj('cbupd|update,loaddl',lang('update'),'btn').' ';
		if(auth(6))$ret.=aj('cbupd|update,creategz',lang('publish'),'btn');
		return $ret.div('','','cbupd');}
}
?>