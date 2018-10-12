<?php

class gaia2_a{	
static $private=6;
static $a='gaia';
static $db='_gaia2';
static $db2='_gaia2_index';
static $cols=['gid','ra','dc','parallax','mag','radius','lum'];//uid used for gaia id
static $typs=['bint','double','double','double','double','double','double'];

function __construct(){
$r=['a','db','cols','db2'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
sqlcreate(self::$db2,['f'=>'var','k'=>'int'],1);
appx::install(array_combine(self::$cols,self::$typs));}

static function admin(){
	$r[]=['','j','popup|gaia2_a,content','plus',lang('open')];
	$r[]=['','pop','core,help|ref=gaia2_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=gaia','code','Code'];
	return $r;}

static function injectJs(){return '
function batchtime(){
	var n=getbyid("step").value; //alert(n);
	if(n<10000)ajaxCall("div,gaiaa|gaia2_a,call","p="+n);//
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
	$gid=str_replace('Gaia DR2 ','',$ra[1]);//$ra[12],$ra[14],
	if($ra[9])$rb[]=[$gid,$ra[5],$ra[7],$ra[9],$ra[50],$ra[88],$ra[91]];}//>0.01 or $ra[9]<-0.01
fclose($h); array_shift($rb);
return $rb;}

static function ff(){
$f='usr/gaia2_files.txt';
$u='http://cdn.gea.esac.esa.int/Gaia/gdr2/gaia_source/csv/';
$ua='usr/gaia2/Index of _Gaia_gdr2_gaia_source_csv_.html';
//$d=file_get_contents($f); if($d)return explode("\n",$d);
//$r=explore($u);
$d=file_get_contents($ua);
$r=explode("\n",$d);
if($r)foreach($r as $k=>$v){$vb=segment($v,'href="','"');
	$vb=str_replace($u,'',$vb);
	if($vb){$rb[]=$vb; sqlsav(self::$db2,[$vb,0]);}}
pr($rb);
if($rb)file_put_contents($f,implode("\n",$rb));
//$r=self::emulate(); //pr($r);
return $rb;}

static function dlgz($f,$id){$er='';
$u='http://cdn.gea.esac.esa.int/Gaia/gdr2/gaia_source/csv/';
//GaiaSource_1000172165251650944_1000424567594791808.csv.gz
$fb='usr/gaia2/'.substr($f,0,-3); mkdir_r($fb); echo $fb.br();
if(!is_file($fb.'.gz'))copy($u.$f,$fb.'.gz');
if(is_file($fb.'.gz') && !is_file($fb)){$d=files::readgz($fb.'.gz'); $er=files::write($fb,$d);}
if(is_file($fb)){
	$r=self::fscan($fb);
	sqlsav2(self::$db,$r,0,1); 
	//$er=files::write($fc,$fb);
	sqlup(self::$db2,'ok',1,$id);
	unlink($fb.'.gz'); unlink($fb);}//lock
return $fb.' '.count($r).' objects added';}

static function batch($r){$i=0; $w=10; $n=12; //5
foreach($r as $k=>$v){$i++; if($i>=$n*$w && $i<($n+1)*$w)$rb[]=self::dlgz($v);}// sleep(20);
return $rb;}

#read
static function call($p){
	$id=val($p,'p'); $ret=''; //echo $n.' - ';
	$d=sql('f',self::$db2,'v',$id);
	//if(!$d)$r=self::ff(); pr($r);
	$ok=sql('ok',self::$db2,'v',$id);
	if($ok)list($id,$d)=sql('id,f',self::$db2,'rw','where id>'.$id.' and ok=0 limit 1');
	//$ret=self::batch($r);
	if(!$ok && auth(6))$ret=self::dlgz($d,$id); $n=$id+1;
	return lk('/gaia2_a/'.$n,$n,'btsav').' '.span($ret,'small').hidden('step',$n);}

static function com(){
	return self::content($p);}

static function repair(){
	$min=1805172020; $max=1805172150;
	for($i=$min;$i<$max;$i++)$r[]='z__gaia2_'.$i;
		qr('DROP TABLE '.implode(',',$r),1);
	}

#content
static function content($p){//p($p);
	//self::install();
	//self::repair();
	$p['p']=val($p,'param',val($p,'p')); $n=0;
	if($p['p'])$ret=self::call($p);
	//$dr='usr/gaia/GaiaSource_000-000-';
	//for($i=0;$i<256;$i++)if(file_exists($dr.str_pad($i,3,'0',STR_PAD_LEFT).'.txt'.$i.''))$n=$i;
	else $ret=input('inp1',1).aj('gaiaa|gaia2_a,call||inp1',lang('send'),'btn');
	//else $ret=self::call($p);
return div($ret,'pane','gaiaa');}
}
?>