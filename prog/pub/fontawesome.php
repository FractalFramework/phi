<?php

class fontawesome{
//used by system
static $private='1';

static function injectJs(){
return '';}
static function headers(){
//add_head('csslink','/css/font-awesome.css');
add_head('jscode',self::injectJs());}

static function admin(){
$r[]=array('','pop','fontawesome,search','','search');
$r[]=array('','pop','core,help|ref=fontawesome','','help');
return $r;}

static function varslight(){return '';}
static function vars(){
$d=file_get_contents('prog/css/fa.css');
$r=explode('.fa-',$d); $_POST['nb']=count($r);
foreach($r as $v){
	$pos=strpos($v,':before');
	if($pos!==false)$ret[]=substr($v,0,$pos);}
return $ret;}

/*static function picto($d,$s='',$c=''){if($c)$c=' '.$c;
if(is_numeric($s))$s='font-size:'.$s.'px;';
return span('','fa fa-'.$d.$c,'',$s);}*/

static function patch(){$ret='';
$r=sql('id,icon','icons','kv',''); pr($r);
//foreach($r as $k=>$v)if(substr($v,-2,2)=='-o')sqlup('icons','icon',substr($v,0,-2),$k);
return $ret;}

//builder
static function build($p){
$inp=val($p,'inp1','');
$r=self::vars();
$ret='';//$_POST['nb'];
//$ret=self::patch();
foreach($r as $v){
	if(($inp && strpos($v,$inp)!==false) or !$inp or $inp==$v)
		//$ret.=ico($v,32).':'.$v.br();
		$ret.=tag('span','class=icon',ico($v,32).br().$v).' ';}
return $ret;}

static function com($p){$id=val($p,'id'); $ret='';
$r=self::vars();
foreach($r as $v)$ret.=btj(ico($v,20),'insert(\'['.$v.':pic]\',\''.$id.'\'); Close(\'popup\');').' ';
return $ret;}

/*static function comlight($p){$id=val($p,'id'); $ret='';
$d=self::varslight(); $r=explode('¬',$d);
foreach($r as $v)$ret.=btj(ico($v,24),'insert(\'['.$v.':pic]\',\''.$id.'\'); Close(\'popup\');').' ';
return $ret;}*/

static function search($p){
$ret=input('inp1','',val($p,'p1'),'1');
$ret.=aj('popup|fontawesome,build||inp1',lang('search'),'btn');
return $ret;}

//interface
static function content($p){
$p['rid']=randid('faw');
$p['p1']=val($p,'param',val($p,'p1'));//unamed param before
//$ret=hlpbt('fontawesome');
//$ret.=self::search($p).br();
//$ret.=div('use ico(\'tv\');');
$ret=self::build($p);
$ret.=div('','clear');
return div($ret,'',$p['rid']);}
}
?>
