<?php
class db{//extends explorer
static $private=0;
//static $b='db';

//function __construct($b=''){if($b)self::$b=$b;}
//static function root(){$b=self::$b; if(!auth(6))$b.='/'.(auth(2)?ses('user'):'public'); return $b;}
static function fu($u){return 'disk/'.$u.'.php';}
static function secu($u){if(strpos($u,ses('user'))!==false or auth(6))return 1; else echo '!';}
static function bt($f){return popup('explorer|b=usr,f='.$f.'.php',ico('database'),'btn');}

static function init($f){$u=self::fu($f);
if(!is_dir($u))mkdir_r($u);
if(!is_file($u))file_put_contents($u,self::dump(['_'=>['col1']],$f));
return $u;}

static function k($u,$k){$r=self::read($u); if(isset($r[$k]))return $r[$k];}
static function filters($u,$o){$r=self::read($u); if(isset($o['sort']))sort($r); return $r;}

static function dump($r,$p){$ret='';
if(is_array($r))foreach($r as $k=>$v){$rb='';
	if(is_array($v))foreach($v as $ka=>$va)$rb[]="'".addslashes(stripslashes($va))."'";
	if($rb)$rc[]=(is_numeric($k)?$k:"'".$k."'").'=>['.implode(',',$rb).']';}
if($rc)return "<?php //tlex/db/".$p."\n".'$r=['.implode(',',$rc).'];';}

static function edit($p){$f=$p['f']; $k=$p['k']; $v=$p['v'];
$ret=input('v'.$k,$v).aj($k.'|db,sav|f='.$f.',k='.$k.'|v'.$k,langpi('modif'),'btsav');
$ret.=aj($k.'|db,bt|f='.$f.',k='.$k.',v'.$k.'='.$v,langpi('close'),'btn');
return $ret;}

static function rq($c,$f,$w=''){$ret='';//0,1|1=a,2=b
$rc=explode(',',$c); $rw=atr($w); $n=count($rw); $r=self::read($f);
if($r)foreach($r as $k=>$rb){if($w){$ok=0;//find where
		foreach($rw as $ka=>$va){if($rb[$ka]==$va)$ok+=1;}
		if($ok==$n)$rd[$k]=$rb;}}
if($c && is_array($rd))foreach($rd as $k=>$rb){//slct cols
	foreach($rb as $kb=>$vb){if(in_array($kb,$rc))$ret[$k][$kb]=$vb;}}
else $ret=$rd;
return $ret;}

static function reorder($r){
if(isset($r['_']))$rb['_']=array_shift($r); $i=0;
foreach($r as $k=>$v){$i++; $rb[$i]=$v;}
return $rb;}

static function findhead($r){$rb=array_shift($r); $k=key($rb);
if(!is_numeric($k)){$rc['_']=array_keys($rb); $rc[1]=array_values($rb);}
elseif($k==0)$rc['_']=array_values($rb); else $rc=$rb;
foreach($r as $k=>$v)$rc[]=$v;
return $rc;}

static function findhead0($r){$rb=array_shift($r); $k=key($rb);
if(!is_numeric($k) or $k==0)$rb['_']=array_values($rb);
return array_merge($rb,$r);}

static function write($f,$r){$f=before($f,'.');
$u=self::init($f); if(!$r)return;
//if(!isset($r['_']))$r=self::findhead($r);
if(isset($r[0]))$r=self::reorder($r);
$d=self::dump($r,$f);
if(self::secu($u))file_put_contents($u,$d);
opcache_invalidate($u);}

/*static function save($p){$f=$p['f']; $k=$p['k']; $v=val($p,'v'.$k);
list($a,$b)=explode('-',$k); $u=before($f,'.');
$r=self::read($u); $r[$a][$b]=$v; self::write($u,$r);
return self::bt($p);}*/

static function save($app,$id,$r){$f=explorer::nod($app,$id); db::write($f,$r);}

static function read($f,$o=''){
$f=before($f,'.'); $u=self::fu($f);
if(is_file($u))include $u;
if($o && isset($r['_']))unset($r['_']);
if(isset($r))return $r;}

static function api($p){$f='usr/'.$p['f'];
$r=self::read($f);
return json_encode($r,true);}

static function call($p){$f=$p['f'];
$r=self::read($f);
return mktable($r);}

}