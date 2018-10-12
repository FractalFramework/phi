<?php
class admin_apps{
static $private='6';
	
//content
static function content($p){$ret='';
$r=applist::comdir();
if($r)foreach($r as $k=>$v){$app=$v[4];
	$lk=lk('/'.$app,$app);
	$lk=popup($app,$app,'btxt');
	$private=isset($app::$private)?$app::$private:0;
	$ret[]=[$lk,$v[0],$private];}
return div(mktable($ret),'board');}
}
?>