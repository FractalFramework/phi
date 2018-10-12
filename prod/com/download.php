<?php
class download{

static function admin(){
	$r[]=['','pop','core,help|ref=download_app','','help'];
	return $r;}
	
static function createtar($f){
	$r=['prod','prog','fonts','cnfg/site.com.php','disk/db/sys','usr/tlex/','tar','amt.php','api.php','boot.php','call.php','htaccess.txt','index.php','favicon.ico','readme.txt','license.txt','pub'];
	return tar::buildFromList($f,$r);}

#content
static function content($prm){
	$f=val($prm,'fileName','fractalframework');
	$f.='.tar'; $fgz=$f.'.gz';
	if(is_file('/'.$fgz))unlink($fgz);
	$url=self::createtar($f);
	$ico=ico('download');
	return lk('/'.$fgz,$ico.$url,'btn');
	return $ret;}
}
?>