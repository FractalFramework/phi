<?php
class json{//extends explorer
static $private=0;
//static $b='json';

//function __construct($b=''){if($b)self::$b=$b;}
//static function root(){$b=self::$b; if(!auth(6))$b.='/'.(auth(2)?ses('user'):'public'); return $b;}
static function fu($u){return 'disk/'.$u.'.json';}
static function secu($u){if(strpos($u,ses('user'))!==false or auth(6))return 1;}
static function bt($f){return popup('explorer|b=usr,f='.$f.'.php',ico('database'));}

static function init($f){$u=self::fu($f);
if(!is_dir($u))mkdir_r($u);
if(!is_file($u))file_put_contents($u,'{}');
return $u;}

static function k($u,$k){$r=self::read($u); if(isset($r[$k]))return $r[$k];}
static function filters($u,$o){$r=self::read($u); if(isset($o['sort']))sort($r); return $r;}

static function write($f,$r){$f=before($f,'.');
$u=self::init($f); if(!$r)return;
//$d=json_r($r);
$d=json_encode($r);//,JSON_PRETTY_PRINT,JSON_UNESCAPED_UNICODE
if(self::secu($u))file_put_contents($u,$d);//FILE_APPEND | LOCK_EX
opcache_invalidate($u);}

static function read($f){$f=before($f,'.');
$u=self::fu($f); if(!is_file($u))return;
$d=file_get_contents($u);
$r=json_decode(($d),true);
return $r;}

static function save($p){$f=$p['f']; $k=$p['k']; $v=val($p,'v'.$k);
list($a,$b)=explode('-',$k); $u=before($f,'.');
$r=self::read($u); $r[$a][$b]=$v; self::write($u,$r);
return self::bt($p);}

static function reorder($r){
if(isset($r['_']))$rb['_']=array_shift($r); $i=0;
foreach($r as $k=>$v){$i++; $rb[$i]=$v;}
return $rb;}

static function call($p){$f=$p['f'];
$r=self::read($f);
return mktable($r);}

}