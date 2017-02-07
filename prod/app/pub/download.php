<?php

class download{

	static function update($f){
		$r=['prog/app','prog/core','prog/js','prog/css','prog/telex','index.php','call.php','lib.php','amt.php','api.php','cnfg/_connect.php','cnfg/_twitter_oAuth.php','.htaccess','favicon.ico','readme.txt'];
		return Tar::buildFromList($f,$r);}

	#content
	static function content($prm){
		$f=val($prm,'fileName','fractalframework');
		$f.='.tar'; $fgz=$f.'.gz';
		if(is_file($fgz))unlink($fgz);
		$url=self::update($f);
		$ico=pic('download');
		return href($fgz,$ico.$url,'btn');
	return $ret;}

}

?>