<?php

class gaia_a{	
static $private=6;
static $db='_gaia';
static $a='gaia';
static $cols=['gid','ra','dc','parallax','pmra','pmdec','mag'];//uid used for gaia id
static $typs=['bint','double','double','double','double','double','double'];

static function install($p=''){
appx::install(array_combine(self::$cols,self::$typs));}

static function admin(){
	$r[]=['','j','popup|gaia_a,content','plus',lang('open')];
	$r[]=['','pop','core,help|ref=gaia_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=gaia','code','Code'];
	return $r;}

static function injectJs(){return '
function batchtime(){
	var n=getbyid("step").value; //alert(n);
	ajaxCall("div,gaiaa|gaia_a,call","p="+n);//if(n<256)
	setTimeout("batchtime()",7000);}
//setTimeout("batchtime()",10);
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
//astrometric_weight_al = 35
//phot_g_mean_flux = 49
//phot_g_mean_mag = 51 (mag)
static function fscan($f){$h=fopen($f,'r');
while($r=fgets($h,4096))if($r){$ra=explode(',',$r);//each line with parallax
	//list($gid,$ad,$dec,$par,$tad,$tdc,$lum)=valk($ra,[1,4,6,8,10,12,51]);
	//echo $gid.'-'.$ad.'-'.$dec.'-'.$par.'-'.$tad.'-'.$tdc.'-'.$lum;
	if($ra[8])$rb[]=['',$ra[1],$ra[4],$ra[6],$ra[8],$ra[10],$ra[12],$ra[51]];}
fclose($h); array_shift($rb);//
return $rb;}

//dr1 20*256+110=5230 - 817731 entries
static function emulate(){
for($i=0;$i<21;$i++){
	$a=str_pad($i,3,'0',STR_PAD_LEFT);
	for($ib=0;$ib<256;$ib++){
		$b=str_pad($ib,3,'0',STR_PAD_LEFT);
		$r[]='GaiaSource_000-'.$a.'-'.$b.'.csv.gz';}}
return $r;}

static function ff(){
$f='usr/dav/MD5SUM.txt';
//$d=file::read($f); $r=explode("\n",$d); pr($r);
//$r=file($f,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES); pr($r);
//$d=files::readgz($u.$f); $er=files::write($fb,$d);//filesize unknown
//$er=files::gz($u.$f,$fb);//too much memory
//$r=self::fscan($f); pr($r);
$r=self::emulate(); //pr($r);
return $r;}

static function dlgz($f){$er='';
$u='http://cdn.gea.esac.esa.int/Gaia/gdr1/gaia_source/csv/';
//$u='http://cdn.gea.esac.esa.int/Gaia/gdr2/gaia_source/csv/';
//GaiaSource_000-000-000.csv.gz
$fb='usr/gaia/'.substr($f,0,-3); mkdir_r($fb); //echo $fb.br();
$fc=substr($fb,0,-4).'.txt';
if(!is_file($fb.'.gz') && !is_file($fc))copy($u.$f,$fb.'.gz');
if(is_file($fb.'.gz') && !is_file($fb)){$d=files::readgz($fb.'.gz'); $er=files::write($fb,$d);}
if(is_file($fb) && !is_file($fc)){$r=self::fscan($fb); sqlsav2(self::$db,$r); $er=files::write($fc,$fb); unlink($fb.'.gz'); unlink($fb);}//lock
return $fb.' '.count($r).' objects added';}

static function batch($r){$i=0; $w=10; $n=12; //5
foreach($r as $k=>$v){$i++; if($i>=$n*$w && $i<($n+1)*$w)$rb[]=self::dlgz($v);}// sleep(20);
return $rb;}

#read
static function call($p){
	$r=self::ff();
	//$ret=self::batch($r);
	$n=val($p,'p'); //echo $n.' - ';
	if(auth(6))$ret=self::dlgz($r[$n]); $nb=$n+1;
	return lk('/gaia_a/'.$nb,$nb,'btsav').' '.span($ret,'small').hidden('step',$nb);}

static function com(){
	return self::content($p);}

#content
static function content($p){//p($p);
	//self::install();
	$p['p']=val($p,'param',val($p,'p')); $n=0;
	if($p['p'])$ret=self::call($p);
	//$dr='usr/gaia/GaiaSource_000-000-';
	//for($i=0;$i<256;$i++)if(file_exists($dr.str_pad($i,3,'0',STR_PAD_LEFT).'.txt'.$i.''))$n=$i;
	else $ret=input('inp1',$p['p']).aj('gaiaa|gaia_a,call||inp1',lang('send'),'btn');
return div($ret,'pane','gaiaa');}
}
?>