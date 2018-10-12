<?php
class admin_apps{
static $private='6';
	
//content
static function content($p){$ret='';
$r[]=array('app','/','privacy');
$r=read_dir(ses('dev').'/app');
if(is_array($r))foreach($r as $dir=>$files){
	if(is_array($files) && $dir)foreach($files as $k=>$file)if(!is_array($file)){
	$app=before($file,'.');
	$lk=lk('/'.$app,$app);
	$private=isset($app::$private)?$app::$private:0;
	$ret[]=array($lk,$dir,$private);}}
return mktable($ret);}
}
?>