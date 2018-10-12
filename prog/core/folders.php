<?php
class folders{//extends explorer
static $private='2';

function __construct($b=''){if($b)self::$b=$b;}
//static function root(){$b=self::$b; if(!auth(6))$b.='/'.(auth(2)?ses('user'):'public'); return $b;}
static function fu($u){return 'disk/'.$u;}
static function secu($u){if(strpos($u,ses('user'))!==false or auth(6))return 1;}
static function bt($f){return popup('explorer|f='.$f.',b=',ico('database'));}

static function init($f){$u=self::fu($f);
if(!is_dir($u))mkdir_r($u);
if(!is_file($u))file_put_contents($u,'');
return $u;}

static function write($f,$d){
$u=self::init($f);
if(is_string($d))file_put_contents($u,$d);
opcache_invalidate($u);}

static function read($f){$u=self::fu($f);
if(is_file($u))return file_get_contents($u);}

}