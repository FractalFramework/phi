<?php
class update{
static $private='6';
static $servr='tlex.fr';

static function dirs(){return ['prod','cfng/site.com.php','amt.php','api.php','boot.php','call.php','htaccess.txt','index.php','favicon.ico','readme.txt','version.txt'];}

static function archive(){$r=self::dirs(); $r[]='fonts';
	$f='fractalframework.tar';
	if(is_file($f.'.gz'))unlink($f.'.gz');
	$f=tar::buildFromList($f,$r);
	return lk('/'.$f,ico('download').$f,'btn');}

static function echo_r($r,$o=''){$ret=[];
	foreach($r as $k=>$v)if(is_array($v))$ret[]=self::echo_r($v,$o); else $ret[]=$v;
	return implode($o,$ret);}

static function mk_r($d){
	$r=explode(';',$d);
	if($r)foreach($r as $v){
		list($f,$date)=explode(':',$v);
		$ret[$f]=$date;}
	return $ret;}

#get dates (local or distant)
static function w_date($dr,$f){
	$fb=($dr?$dr.'/':'').$f;
	return $fb.':'.date('ymd.His',filemtime($fb));}

static function localfdates($p=''){$ret=array();
	$r=self::dirs(); $r[]='prog';
	foreach($r as $v){
		if(is_dir($v))$ret[$v]=walk('update','w_date',$v);
		elseif(is_file($v)){
			$dr=before($v,'/'); $f=after($v,'/');
			$ret[$v]=self::w_date('',$v);}}
if(isset($ret))return self::echo_r($ret,';');}

#load dl (client)
static function dlfile($p){$f=val($p,'file');
	if($f) return file_get_contents($f);}

//build list of files to dl
static function files2dl(){$ret=array();
	//local
	$d=self::localfdates();
	$local=self::mk_r($d); //pr($local);
	//distant
	$f='http://'.self::$servr.'/api/update/mth=localfdates';
	$d=files::get($f);
	$distant=self::mk_r($d); //pr($distant);
	if($distant)foreach($distant as $k=>$v)
		if(array_key_exists($k,$local)){if($v>$local[$k])$ret[]=$k;}
	//obsoletes
	if($local)foreach($local as $k=>$v)
		if(!array_key_exists($k,$distant))unlink($k);
	return $ret;}

static function preview($p){
	$ra=self::mk_r(self::localfdates());
	$distfdates=files::get('http://'.self::$servr.'/api/update/mth=localfdates');
	$rb=self::mk_r($distfdates);
	$ret[]=['file','local','distant'];
	if($rb)foreach($rb as $k=>$v)
		if(array_key_exists($k,$ra))$ret[]=[$k,$ra[$k],$v,$v>$ra[$k]?ico('warning'):''];
		else $ret[]=[$k,'',$v,''];
	return mktable($ret);}

//dl(distant)
static function builddl($p){
	$r=explode('|',get('files'));
	if($r)return tar::buildFromList('pub/dl/ffw.tar',$r);}

//dl(local)
static function loaddl(){
	$rid=randid('dl');
	if(self::$servr==nohttp($_SERVER['HTTP_HOST']))return;
	$r=self::files2dl(); //pr($r);
	if($r)foreach($r as $k=>$v){
		$f='http://'.self::$servr.'/api/update/mth=dlfile,file='.$v;
		$d=files::get($f); $er='';
		if($d)$er=files::write($v,$d); else $er=1;
		if($d && $er){unset($r[$k]); $rb[]=$v;}}
	$ret=hr().count($r).' '.lang('files updated').hr().self::echo_r($r,br());
	if(isset($rb))$ret.=hr().count($rb).' '.lang('errors').hr().self::echo_r($rb,br());
	return $ret;}

#interface
static function content($p){
	$f=val($p,'f'); $ret='';
	if(auth(4))$ret=aj('cbupd,,z|update,preview',langp('preview'),'btn').' ';
	if(auth(4))$ret.=aj('cbupd,,z|update,loaddl',langp('update software'),'btn').' ';
	if(auth(6))$ret.=aj('cbupd|upsql',langp('databases'),'btn');
	if(auth(6))$ret.=aj('cbupd|update,archive',langp('create archive'),'btn');
	return $ret.div('','','cbupd');}
}
?>