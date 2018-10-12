<?php

class batchurl{	
static $private=6;
static $a='batchurl';
static $db='batchurl';
static $cols=['dom'];
static $typs=['var'];

function __construct(){$r=['a','db','cols'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
appx::install(array_combine(self::$cols,self::$typs));}

static function admin(){
$r[]=['','j','popup|batchurl,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=batchurlpp','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=batchurl','code','Code'];
return $r;}

static function injectJs(){return '
function batchtime(){
var n=getbyid("step").value; //alert(n);
if(n<10000)ajaxCall("div,batchurl|batchurl,call","p="+n);//
setTimeout("batchtime()",3000);}
setTimeout("batchtime()",10);//
';}
static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

static function titles($p){
$d=val($p,'appMethod');
$r['content']='welcome';
$r['build']='model';
if(isset($r[$d]))return lang($r[$d]);}

#build
/*static function build($p){$id=val($p,'id');
$r=sql('all',self::$db,'ra',$id);
return $r;}*/

#dl
//56 columns : https://www.rave-survey.org/project/documentation/dr5/rave_tgas/
//astrometric_weight_al = 34
//phot_g_mean_flux = 47
//phot_g_mean_mag = 50 (mag)
//radius_val = 88
//lum = 91 //tot=93
static function fscan($f){$h=fopen($f,'r');
while($r=fgets($h,4096))if($r){$ra=explode(',',$r);//each line with parallax
//list($gid,$ad,$dec,$par,$mag,$radius,$lum)=valk($ra,[1,5,7,9,50,88,91]);
//echo $gid.'-'.$ad.'-'.$dec.'-'.$par.'-'.$tad.'-'.$tdc.'-'.$lum;
$gid=str_replace('batchurl DR2 ','',$ra[1]);//$ra[12],$ra[14],
if($ra[9])$rb[]=[$gid,$ra[5],$ra[7],$ra[9],$ra[50],$ra[88],$ra[91]];}//>0.01 or $ra[9]<-0.01
fclose($h); array_shift($rb);
return $rb;}

static function ff(){
$f='usr/batchurl_files.txt';
$u='https://www.ovh.com/fr/order/domain/#/legacy/domain/search?domain=';//apps.com
$ua='usr/batchurl.txt';
$d=file_get_contents($ua);
$r=explode("\n",$d);
if($r)foreach($r as $k=>$v){$vb=segment($v,'href="','"');
$vb=str_replace($u,'',$vb);}
pr($rb);
if($rb)file_put_contents($f,implode("\n",$rb));
//$r=self::emulate(); //pr($r);
return $rb;}

static function dlgz($f,$id){$er='';
$u='https://www.ovh.com/fr/order/domain/#/legacy/domain/search?domain=';//apps.com
$d=files::curl($u);
$ret=segment($v,$s,$e);
sqlsav(self::$db,[$ret],'',1);
return $fb.' '.count($r).' objects added';}

static function batch($r){$i=0; $w=10; $n=12; //5
foreach($r as $k=>$v){$i++; if($i>=$n*$w && $i<($n+1)*$w)$rb[]=self::dlgz($v);}// sleep(20);
return $rb;}

#read
static function call($p){
$id=val($p,'p'); $ret=''; //echo $n.' - ';
//if(!$d)$r=self::ff(); pr($r);
//$ret=self::batch($r);
if(!$ok && auth(6))$ret=self::dlgz($d,$id); $n=$id+1;
return lk('/batchurl/'.$n,$n,'btsav').' '.span($ret,'small').hidden('step',$n);}

static function com(){
return self::content($p);}

#content
static function content($p){//p($p);
//self::install();
$p['p']=val($p,'param',val($p,'p')); $n=0;
if($p['p'])$ret=self::call($p);
//$dr='usr/batchurl/batchurlSource_000-000-';
//for($i=0;$i<256;$i++)if(file_exists($dr.str_pad($i,3,'0',STR_PAD_LEFT).'.txt'.$i.''))$n=$i;
else $ret=input('inp1',1).aj('batchurl|batchurl,call||inp1',lang('send'),'btn');
//else $ret=self::call($p);
return div($ret,'pane','batchurl');}
}
?>