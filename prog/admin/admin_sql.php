<?php

class admin_sql{
static $private=7;
var $db='';

static function del_row($p){
$b=$p['b']; $nm=val($p,'nm',0);
//sqldel($b,$nm);
return self::read($b);}

static function sav_row($p){
$b=$p['b']; $ka=val($p,'k',0); $n=$p['n']; $rid=$p['rid'];
$ra=sqlcols($b,7);
foreach($ra as $k=>$v)$rb[$v]=val($p,$rid.$k,''); //pr($rb);
sqlups($b,$rb,$ka);
return self::read($b);}

static function edit_row($p){$b=$p['b']; $ka=$p['k'];
$r=sql('*',$b,'id'); $ra=sqlcols($b,7); $n=count($ra); if($r[$ka])$r=$r[$ka]; $rid=randid();
for($i=0;$i<$n;$i++)$rb[]=$rid.$i; if($n)$prm=implode(',',$rb); else $prm='';
$bt=aj('asl,,x|admin_sql,sav_row|b='.$b.',k='.$ka.',n='.$n.',rid='.$rid.'|'.$prm,langp('save'),'btsav');
$bt.=aj('asl,,x|admin_sql,del_row|b='.$b.',nm='.$ka,langp('del'),'btdel');
if($ra)foreach($ra as $k=>$v)$ret[]=[$v,goodinput($rb[$k],val($r,$k))];
return $bt.mktable($ret);}

static function read($b){
$r=sql('*',$b,'id');
$ret=build::editable($r,'admin_sql,edit_row|b='.$b.',');
return div($ret,'','asl');}

static function content($p){$ret='';
$r=query('show tables','rv'); $b=val($p,'b');
$ret=batch($r,'asq|admin_sql|b=$v',$b);
if($b)$ret.=self::read($b);
return div($ret,'board','asq');}

}