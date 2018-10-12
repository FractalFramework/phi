<?php

class core{
static function help($p){return helpcom($p);}
static function helpget($p){return helpget($p);}
static function mkbcp($p){return mkbcp($p);}
static function rsbcp($p){return rsbcp($p);}
static function lang_set($p){return lang_set($p);}
static function clrpick($p){return clrpick($p);}
static function img($p){return img('/'.val($p,'f'));}
static function txt($p){return div(val($p,'txt'),'board');}
static function voc($p){return voc(val($p,'txt'),val($p,'ref'));}
static function app($p){return app(val($p,'app'),val($p,'p'));}}

#sql
function connect(){require($_SESSION['connect']); ses('dbq',$dbq);}
function qr($sql,$z=''){if($z)echo $sql; $rq=mysqli_query(ses('dbq'),$sql);
if($rq==null)echo mysqli_error(ses('dbq')).br().$sql.hr(); return $rq;}
function qfar($r){if($r)return mysqli_fetch_array($r);}
function qfas($r){if($r)return mysqli_fetch_assoc($r);}
function qfrw($r){if($r)return mysqli_fetch_row($r);}
function query($sql,$p,$z=''){if($z)echo $sql; $rq=qr($sql,$z);
if($rq){$ret=sqlformat($rq,$p); mysqli_free_result($rq); return $ret;}}

function sqlformat($rq,$p){$ret=[];
if($p=='rq')return $rq;
if($p=='ry')return qfar($rq);
if($p=='ra')return qfas($rq);
if($p=='rw')return qfrw($rq);
if($p=='v'){$r=qfrw($rq); return $r[0];}
if($p=='rr'){while($r=mysqli_fetch_assoc($rq))$ret[]=$r; return $ret;}
while($r=mysqli_fetch_row($rq))if($r[0])switch($p){
	case('k'):$ret[$r[0]]=1; break;
	case('rv'):$ret[]=$r[0]; break;
	case('kv'):$ret[$r[0]]=$r[1]; break;
	case('kr'):$ret[$r[0]][]=$r[1]; break;
	case('kk'):$ret[$r[0]][$r[1]]=1; break;
	case('vv'):$ret[]=[$r[0],$r[1]]; break;
	case('kkc'):$ret[$r[0]][$r[1]]=1; break;
	case('kkv'):$ret[$r[0]][$r[1]]=$r[2]; break;
	case('kkr'):$ret[$r[0]][$r[1]][]=$r[2]; break;
	case('kkk'):$ret[$r[0]][$r[1]][$r[2]]=1; break;
	case('kvv'):$ret[$r[0]]=[$r[1],$r[2]]; break;
	case('id'):$k=array_shift($r); $ret[$k]=$r; break;
	case('kad'):if(isset($ret[$r[0]]))$ret[$r[0]]+=1; else $ret[$r[0]]=1; break;
	default:$ret[]=$r; break;}
return $ret;}

function rqcols($d,$b){//cols
if(is_array($d))$d=implode(',',$d);
elseif(substr($d,-6)=='timeup')$d=substr($d,0,-6).'unix_timestamp('.$b.'.up) as time';
elseif(substr($d,-6)=='dateup')$d=substr($d,0,-6).'date_format('.$b.'.up,"%d/%m/%Y") as date';
elseif($d=='all')$d=sqlcols($b,3);
return $d;}

//sql('id','qda','rv','where id=""');
function sql($d,$b,$p='',$q='',$z=''){
$d=rqcols($d,$b); $q=setq($q,$b);
if($b)$rq=qr('select '.$d.' from '.$b.' '.$q,$z);
if(!empty($rq)){$ret=sqlformat($rq,$p); mysqli_free_result($rq); return $ret;}}

//join b2 to b1, associating b2.$key to b1.id
function sqljoin($d,$b1,$b2,$key,$p,$q='',$z=''){//b2 is on the right, let left empty
$q='inner join '.$b2.' on '.$b1.'.'.$key.'='.$b2.'.id '.setq($q,$b1);
return sql($d,$b1,$p,$q,$z);}

function sqlin($d,$b1,$b2,$key,$p,$q='',$z=''){//b1 is first
$q='right join '.$b1.' on '.$b2.'.'.$key.'='.$b1.'.id '.setq($q,$b1);
return sql($d,$b2,$p,$q,$z);}

//insert
function escape($v){
if(ses('enc'))$v=($v); else $v=html_entity_decode($v);//utf8_decode
return mysqli_real_escape_string(ses('dbq'),stripslashes($v));}
function read_from_array($r,$o=''){
foreach($r as $k=>$v)$rb[]=$k.'="'.escape($v).'"';
return 'where '.implode(' '.($o?'or':'and').' ',$rb);}
function setq($q,$b){
if(is_array($q))return read_from_array($q);
elseif(is_numeric($q))return 'where '.$b.'.id="'.$q.'"';
else return $q;}

function insert_from_array2($r,$o=''){//[{1,'hello'],[2,hey]]
foreach($r as $k=>$v)$rb[]=insert_from_array($v,$o);
return implode(',',$rb);}
function sqlsav2($b,$r,$o='',$x='',$z=''){
if(auth(6) && $x){sqlbcp($b,date('ymdHi')); trunc($b);}
$sql='insert into '.$b.' ('.sqlcols($b,$o?3:1).') values '.insert_from_array2($r,$o);
$rq=qr($sql,$z); return mysqli_insert_id(ses('dbq'));}

function insert_from_array($r,$o=''){
foreach($r as $k=>$v){
	if(substr($v,0,8)=='PASSWORD')$rb[$k]=$v;
	else $rb[$k]='"'.escape($v).'"';}
if($o)return '('.implode(',',$rb).')';//NULL,
else return '(NULL,'.implode(',',$rb).',"'.date('Y-m-d H:i:s',time()).'")';}

function sqlsav($b,$r,$z='',$o=''){
$sql='insert into '.$b.' values '.insert_from_array($r,$o);
$rq=qr($sql,$z); return mysqli_insert_id(ses('dbq'));}
function sqlup($b,$d,$v,$id,$col='',$z=''){$col=$col?$col:'id';//update
qr('update '.$b.' set '.$d.'="'.escape($v).'" where '.$col.'="'.$id.'"',$z);}
function sqlups($b,$r,$id,$col='',$z=''){$com=''; $col=$col?$col:'id';
foreach($r as $k=>$v)$rb[]=$k.'="'.escape($v).'"';
qr('update '.$b.' set '.implode(',',$rb).' where '.$col.'="'.$id.'"',$z);}
function sqlsavup($b,$r,$z=''){$ex=sql('id',$b,'v',$r);
if(!$ex)$ex=sqlsav($b,$r); return $ex;}
function sqldel($b,$id,$col=''){$col=$col?$col:'id';
if(is_array($id))$w=read_from_array($id); else $w='where '.$col.'="'.$id.'"';
qr('delete from '.$b.' '.$w);}

#app
function app($app,$p='',$mth=''){$ret='';
if(isset($p['prm']))$p=_jrb($p['prm']);//when calling not by ajax
if(!is_array($p) && strpos($p,'{')!==false)$p=json_decode($p,true);
if(!empty($p['appMethod']))$mth=$p['appMethod']; 
else $mth=$mth?$mth:'content';
if(method_exists($app,$mth)){
	$private=isset($app::$private)?$app::$private:0; $auth=ses('auth'); if(!$auth)$auth=0;
	if($auth>=$private){$a=new $app; $ret=$a->$mth($p);
		if(method_exists($app,'headers'))
			if(!get('appName') or isset($p['headers']))
				if(!isset(storage::$alx[$app])){storage::$alx[$app]=1; $a->headers();}}
	else $ret=help('need auth 1','paneb');}
else return div(hlpxt('no app loaded').' : '.$app.'::'.$mth,'paneb');
return $ret;}

function ifrapp($app,$id){
return iframe(host().'/frame/'.$app.'/'.$id);}

#builders
function mktable($r,$head='',$keys=''){$i=0; $tr='';
if(is_array($r))foreach($r as $k=>$v){$td=''; $i++;
	$tag=($head && $i==1)||$k=='_'?'th':'td';
	if($keys)$td.=tag($tag,'',$k?$k:'');
	if(is_array($v))foreach($v as $ka=>$va)
		$td.=tag($tag,['id'=>$k.'-'.$ka],$va);
	else $td.=tag($tag,'',$v);
	if($td)$tr.=tag('tr',['id'=>'k'.$k],$td);}
$ret=tag('tbody','',$tr);//
return div(tag('table','',$ret),'','','overflow:auto;');}

//taxonomy
function taxo_clean($r,$rb){
	foreach($rb as $k=>$v)if(isset($r[$v]))unset($r[$v]);
	return $r;}

function taxo_find($rx,$ra,$rb){$ret='';
	foreach($rb as $k=>$v){
		if(isset($ra[$k])){
			if(is_array($ra[$k])){
				$rb=taxo_find($rx,$ra,$ra[$k]);
				$rx=$rb[0];
				$ret[$k]=$rb[1];}
			else $ret[$k]=$ra[$k];
			$rx[]=$k;}
		else $ret[$k]=$v;}
	return [$rx,$ret];}

function taxonomy($r){$ra=$r; $rx=''; $ret='';
	foreach($r as $k=>$v){
		if(is_array($v)){
			$rb=taxo_find($rx,$ra,$v);
			$rx=$rb[0];
			$ret[$k]=$rb[1]?$rb[1]:$v;}
		else $ret[$k]=$v;}
	$ret=taxo_clean($ret,$rx);
	return $ret;}

#clr
function clrs(){return json::read('json/system/colors');}//get
function clrget($d){$r=sesfunc('clrs','',0); if(isset($r[$d]))return $r[$d];}//read
function clrand(){$r=sesfunc('clrs'); if(is_array($r))$r=array_values($r); return $r[rand(0,139)];}
function btclr($k,$v){return span('','clr','','background-color:#'.$v.';');}//,['title'=>$k]
/*function clrpick0($p){$r=sesfunc('clrs'); $id=$p['id']; $ret=''; $mode=val($p,'ins')?'insert':'val';	
foreach($r as $k=>$v)$ret.=btj(btclr($k,$v),atj($mode,[$v,$id])).' '; return $ret;}*/
function pickbt($id){return autoggle('cklr','core,clrpick|n=12,id='.$id,pic('clr'));}
function clrpick($p){$id=$p['id']; $bt=''; $ret='';//16777214/65536/256
$ai=val($p,'a','00'); //$bi=val($p,'b','00'); $ci=val($p,'c','00');
$n=val($p,'n',16); $m=ceil(256/$n);//6.3496042078728
for($ia=0;$ia<$n;$ia++){$a=dechex($ia*$m); $a=str_pad($a,2,'0',STR_PAD_LEFT);
	//$ret.=aj('cklr|core,clrpick|id='.$id.',n='.$n.',a='.$a,btclr($a,''.$a.$a.$a));} $ret.=br();
	for($ib=0;$ib<$n;$ib++){$b=dechex($ib*$m); $b=str_pad($b,2,'0',STR_PAD_LEFT);
		for($ic=0;$ic<$n;$ic++){$c=dechex($ic*$m); $c=str_pad($c,2,'0',STR_PAD_LEFT);
			$r[]=$a.$c.$b;}}}//
foreach($r as $k=>$v)$ret.=tag('a',['class'=>'clr','onclick'=>atj('affectclr',[$v,$id]),'style'=>'background-color:#'.$v.';','title'=>$v],'');
return div($ret,'','cklr');}

#db
function db_read($nd,$k='',$kb='',$h=''){$r=db::read($nd);
if($k && isset($r[$k]))$ret=$r[$k]; if($kb && isset($ret[$kb]))$ret=$ret[$kb]; else $ret=$r;
if($h && isset($ret['_']))unset($ret['_']);
return $ret;}
function db_write($nd,$r,$k='',$kb=''){
if($k){$r=db::read($nd); if(!isset($r[$k]))return; if($kb)$r[$k][$kb]=$r; else $r[$k]=$r;}
db::write($dr.'/'.$nd,$r);}
//function db_bt($f){return db::bt($f);}

#dir
function read_dir($dir){if(!is_dir($dir))return;
$r=scandir($dir); $ret=[];
foreach($r as $k=>$v){if(!in_array($v,['.','..','_notes'])){
	if(is_dir($dir.'/'.$v))$ret[$v]=read_dir($dir.'/'.$v);
	else $ret[]=$v;}}
return $ret;}

function dir_scan($dir){$ret='';
if(is_dir($dir))$r=scandir($dir); if(!isset($r))return;
foreach($r as $k=>$v)if($v!='.' && $v!='..' && $v!='_notes'){
	if(is_dir($dir.'/'.$v))$ret[$v]=$v; else $ret[$k]=$v;}
return $ret;}

function explore($dr,$p='',$o=''){//unused
$r=scandir($dr,0); static $i; $ret=[];
foreach($r as $k=>$f){$drb=$dr.'/'.$f; $i++;
if(is_dir($drb) && $f!='..' && $f!='.' && $f!='_notes'){
	if($p=='dirs')$ret[$f]=$f; if(!$o)$ret+=explore($drb,$p,$o);}
if($p!='dirs')if(is_file($drb))$ret[$i]=$drb;}
return $ret;}

function remove_dir($dr){
if(!ses('uid'))return;
$dir=opendir($dr); $ret='';
while($f=readdir($dir)){$ret[]=$drb=$dr.'/'.$f;
if(is_dir($drb) && $f!='..' && $f!='.'){remove($drb); rmdir($drb);}
elseif(is_file($drb))unlink($drb);} rmdir($dr);
return $ret;}

function mkdir_r($u){$ret='';
$nu=explode('/',$u); if(count($nu)>12)return;
if(strpos($u,'Warning')!==false)return;
foreach($nu as $k=>$v){$ret.=$v.'/'; if(strpos($v,'.'))$v='';
if($v && !is_dir($ret) && !mkdir($ret))echo '('.$v.':no)';}}

function rmdir_r($dr){
if(!auth(6))return; $dir=opendir($dr); $ret=$dr.br();
while($f=readdir($dir)){$drb=$dr.'/'.$f;
if(is_dir($drb) && $f!='..' && $f!='.'){rmdir_r($drb); if(is_dir($drb))rmdir($drb);}
elseif(is_file($drb)){unlink($drb); $ret.=$drb.br();}} rmdir($dr); return $ret;}

//walk
/*apply a function to the files of a dir
$res=walk('dir','walkMethod','db','',1);
$res=walk('','walkfunc','db',read_dir('db'),1);*/
function walkMethod($dir,$file){
return $dir.'/'.$file;}

function walk($app,$method,$dir,$r='',$recursive=''){
if(!$r)$r=read_dir($dir);
$ret=[]; if(substr($dir,-1)=='/')$dir=substr($dir,0,-1);
if($r)foreach($r as $k=>$v)
	if(is_array($v)){
		$rb=walk($app,$method,$dir.'/'.$k,$v,$recursive);
		if($recursive)$ret[$k]=$rb; else $ret=array_merge($ret,$rb);}
	elseif(is_file($dir.'/'.$v))
		if($app)$ret[$k]=$app::$method($dir,$v);
		elseif($method)$ret[$k]=$method($dir,$v);
return $ret;}

#head
function headerhtml(){return '<!DOCTYPE html>
<html lang="fr" xml:lang="fr">'.n();}
function meta($attr,$prop,$d=''){
return '<meta '.$attr.'="'.$prop.'"'.($d?' content="'.$d.'"':'').'>';}
function csslink($u){
if(strrchr($u,'.')=='.css')return '<link href="/'.ses('dev').$u.'" rel="stylesheet" type="text/css">';}
function jslink($u){if(substr($u,0,4)!='http')$root='/'.ses('dev'); else $root='';
return '<script src="'.$root.$u.'"></script>';}
function csscode($code){return '<style type="text/css">'.$code.'</style>';}
function jscode($code){return '<script type="text/javascript">'.$code.'</script>';}
function add_head($action,$r){storage::$head[][$action]=$r;}//add
function add_prop($p,$v){storage::$head[]['meta']=['attr'=>'property','prop'=>$p,'content'=>$v];}
function add_name($p,$v){storage::$head[]['meta']=['attr'=>'name','prop'=>$p,'content'=>$v];}
function build_head(){$ret=''; $r=storage::$head; //p($r);
if($r)foreach($r as $k=>$v){if(is_array($v))$va=current($v);
	switch(key($v)){
		case('code'):$ret.=$va."\n"; break;
		case('csslink'):if($va)$ret.=csslink($va)."\n"; break;
		case('jslink'):if($va)$ret.=jslink($va)."\n"; break;
		case('csscode'):if($va)$ret.=csscode($va)."\n"; break;
		case('jscode'):if($va)$ret.=jscode($va)."\n"; break;
		case('rel'):$v=$v['rel'];
			$ret.='<link rel="'.$v['name'].'" href="'.$v['value'].'">'."\n"; break;
		case('meta'):$v=$v['meta']; $ret.=meta($v['attr'],$v['prop'],$v['content'])."\n"; break;
		case('tag'):$v=$v['tag']; $ret.=tag($v[0],$v[1],$v[2]); break;}}
	return $ret;}

function generate(){return headerhtml().tag('head','',build_head());}

#help
/*function help_read($ref){$lg=lng();
return sql('txt','help','v','where ref="'.$ref.'" and lang="'.$lg.'"');}*/
function helpget($p){$lg=lng(); $bt='';//val($p,'lang',lng())
$r=sql('id,txt','help','rw','where ref="'.$p['ref'].'" and lang="'.$lg.'"');//if(!$r)return $p['ref'];
if(!$r[0] && $p['ref'])$r[0]=sqlsav('help',[$p['ref'],'',$lg]);
if(auth(6))$bt=aj('popup|admin_help,edit|to=hlpxd,id='.$r[0].',headers=1',ico('edit')).' ';
if(isset($r[1]))$txt=val($p,'conn')?conn::call($r[1]):nl2br($r[1]); else $txt=$p['ref'];
if(val($p,'brut'))return val($r,1); else return div($bt.$txt,val($p,'css'),'hlpxd');}
function helpcom($p){return div(helpget($p),'helpxt');}
//function help_conn($p){return div(helpget($p),'helpxt');}

function voc($d,$ref,$lg0=''){$lng=ses('lng');
list($db,$col,$id)=explode('-',$ref); $vrf=md5($d);
$lg=sql('lang','voc','v','where vrf="'.$vrf.'"');
if(!$lg){//changes
	$ex=sql('id','voc','v','where ref="'.$ref.'" and lang="'.$lng.'"');
	if($ex)sqldel('voc',$ref,'ref');}
if(!$lg){if($lg0)$lg=$lg0; else $lg=yandex::detect(['txt'=>$d]);
	if($lg)$id=sqlsav('voc',[$ref,$lg,$d,$vrf]);}
if($lg && $lg!=$lng){
	$b=sql('trad','voc','v',['ref'=>$ref,'lang'=>$lng]);
	if(!$b){$c=yandex::com(['from'=>$lg,'to'=>$lng,'txt'=>$d]);
		if($c)sqlsav('voc',[$ref,$lng,$c,md5($c)]); $d=$c;}
	else $d=$b;}
return $d;}

function setlng(){$lng=ses('lng'); if($lng=='en')$lngb='US'; else $lngb=strtoupper($lng);
setlocale(LC_ALL,$lng.'_'.$lngb.'.utf8');}

#icon
function icon_ex($d){$r=sesfunc('icon_com','',0);
if(is_array($r) && array_key_exists($d,$r))return $r[$d];}
function icon_com(){return sql('ref,icon','icons','kv','');}
function icon_get($d,$o=''){$r=sesfunc('icon_com','',0);
if(!array_key_exists($d,$r) && $d && !is_numeric($d)){
	sqlsav('icons',[$d,'']); $r=sesfunc('icon_com','',1);}
$ret=isset($r[$d]) && $r[$d]?$r[$d]:'';
if($o)$ret=ico($ret); return $ret;}

#img
//force LH, cut and center
function scale($w,$h,$wo,$ho,$s){$hx=$wo/$w; $hy=$ho/$h; $yb=0; $xb=0;
if($s==2){$xb=($wo/2)-($w/2); $yb=($ho/2)-($h/2); $wo=$w; $ho=$h;}
elseif($hy<$hx && $s){$xb=0; $yb=($ho-($h*$hx))/2; $ho=$ho/($hy/$hx);}//reduce_h
elseif($hy>$hx && $s){$xb=($wo-($w*$hy))/2; $wo=$wo/($hx/$hy);}//reduce_w
elseif($hy<$hx){$xb=($wo-($w*$hy))/2; $wo=$wo/($hx/$hy);}//adapt_h
elseif($hy && $hx){$xb=0; $ho=$ho/($hy/$hx);}//adapt_w
return [$w,$h,$wo,$ho,$xb,$yb];}

function mkthumb($in,$out,$w,$h,$s){$xa=0; $ya=0;
$w=$w?$w:170; $h=$h?$h:100; list($wo,$ho,$ty)=getimagesize($in); 
list($w,$h,$wo,$ho,$xb,$yb)=scale($w,$h,$wo,$ho,$s);
if(is_file($in))if(filesize($in)/1024 >5000)return;
$img=imagecreatetruecolor($w,$h);
if($ty==2){$im=imagecreatefromjpeg($in);
	imagecopyresampled($img,$im,$xa,$ya,$xb,$yb,$w,$h,$wo,$ho);
	imagejpeg($img,$out,100);}
elseif($ty==1){$im=imagecreatefromgif($in); imgalpha($img);
	imagecopyresampled($img,$im,$xa,$ya,$xb,$yb,$w,$h,$wo,$ho);
	imagegif($img,$out);}
elseif($ty==3){$im=imagecreatefrompng($in); imgalpha($img);
	imagecopyresampled($img,$im,$xa,$ya,$xb,$yb,$w,$h,$wo,$ho);
	imagepng($img,$out);}
return $out;}

function imgalpha($img){//imagefilledrectangle($im,0,0,$w,$h,$wh);
$c=imagecolorallocate($img,255,255,255); imagecolortransparent($img,$c);
imagealphablending($img,false); 
imagesavealpha($img,true);}

function hexrgb_r($d){for($i=0;$i<3;$i++)$r[]=hexdec(substr($d,$i*2,2)); return $r;}
function hexrgb($d,$o=''){$r=hexrgb_r($d); return 'rgba('.$r[0].','.$r[1].','.$r[2].','.$o.')';}

#lang
function lng(){return ses('lng')?ses('lng'):'fr';}
function lngs(){return ['en','es','fr'];}//,'zn','ru','de','it','ja','ar','zw'
function lang_set($p){$v=val($p,'lang',lng());
sez('lng',$v); cookie('lng',$v); sesfunc('lang_com',$v,1); return $v;}
function lang_com($lang){return sql('ref,voc','lang','kv','where lang="'.$lang.'"');}
function lang_ex($d){$lang=lng();
$r=sesfunc('lang_com',$lang); if(is_array($r) && array_key_exists($d,$r))return 1;}
function lang_get($d,$o='',$no=''){$lang=lng(); $applng=ses('applng')?ses('applng'):$lang;
$r=sesfunc('lang_com',$lang,0);
if($r)if(!array_key_exists($d,$r) && $d && strpos($d,',')===false && $d && !is_numeric($d) && !$no){
	sqlsav('lang',[$d,'',$applng,$lang]);
	$r=sesfunc('lang_com',$lang,'1');}
$ret=isset($r[$d]) && $r[$d]?$r[$d]:$d;
if(!$o)$ret=ucfirst_b($ret);
return $ret;}

#phylo
function phylo($p,$r){$ret='';
foreach($r as $k=>$v){
	if(is_array($v))$ret.=div(phylo($p,$v),'',$k);
	elseif(array_key_exists($v,$p)){$va=$p[$v];
		if(is_array($va)){
			if(!is_numeric($k))$ret.=div(implode('',$va),$k);
			else $ret.=implode('',$va);}
		elseif(!is_numeric($k))$ret.=div($va,$k);
		else $ret.=$va;}}
return $ret;}

#preview
function preview($d){
return (deln(substr(strip_tags($d),0,200),' '));}//addslashes

#astro
function phi($l=10){$d=1; for($i=0;$i<10*$l;$i++)$d=bcadd(1,bcdiv(1,$d,$l),$l); return $d;}
function al2km($d,$o=1){return round(($d*9460730472580)/$o,2);}//lightspeed
function parsec2km($d,$o=1){return round(($d*30856780000000)/$o,2);}//parsec
function parsec2al($d,$o=1){return round(($d*3.261564)/$o,2);}//parsec
function al2parsec($d,$o=1){return round(($d/3.261564)/$o,2);}//parsec
function sunsz($d,$o=1){return round(($d*1392000)/$o,2);}//sun size
function ra2deg($d){//00h00m00s
	$d=str_replace(' ','',$d);
	$ad1=substr($d,0,2); $ad2=substr($d,3,2); $ad3=substr($d,6,2);
	$a=($ad1*15); $b=($ad2/6*15/10); $c=($ad3/6*15/100); //echo $a.'+'.$b.'+'.$c.'-  ';
	return $a+$b+$c;}//round,4
function dec2deg($d){//+00°00'00"
	$d=str_replace(' ','',$d);
	$ad1=mb_substr($d,0,3); $ad2=mb_substr($d,4,2); $ad3=mb_substr($d,8,2);
	$a=$ad1; $b=$ad2/60; $c=$ad3/600; //echo $a.'+'.$b.'+'.$c.'-  ';
	return round($a+$b+$c,4);}
function deg2ra($d){$ha=$d/15; $h=floor($ha);//if(!is_int($d))echo $d=floatval($d);
	$hab=$ha-$h; if($hab)$ma=round(60*$hab,4); else $ma=0; $m=floor($ma);
	$mab=$ma-$m; if($mab)$sa=round(10*$mab,4); else $sa=0; $s=floor($sa);
	$sf=round((10*$mab)-$s,2)*100;
	$h=str_pad($h,2,'0',STR_PAD_LEFT);
	$m=str_pad($m,2,'0',STR_PAD_LEFT);
	$s=str_pad($s,2,'0',STR_PAD_LEFT).'.'.$sf;
	return $h.'h '.$m."m ".$s.'s';}
function deg2dec($d){$deg=floor($d);//+00°00'00"
	$m1=$d-$deg; $m2=$m1/10*60*10; $m=floor($m2);
	$s1=$m2-$m; $s2=$s1/10*60*10; $s=floor($s2);
	$sf=round($s2-$s,2)*60; //echo $deg.'+'.$m.'+'.$s.'-'.$sf.' ';
	$deg=str_pad($deg,2,'0',STR_PAD_LEFT);
	$m=str_pad($m,2,'0',STR_PAD_LEFT);
	$s=str_pad($s,2,'0',STR_PAD_LEFT).'.'.$sf;
	if($deg>0)$deg='+'.$deg;
	return $deg.'° '.$m."' ".$s.'"';}
function xyz($ad,$dc,$ds){$a=deg2rad($ad); $b=deg2rad($dc); 
$x=0-round(sin($a)*$ds,2); $y=round(sin($b)*$ds,2); $z=round(cos($a-$b)*$ds,2);
return [$x,$y,$z,$a,$b];}
function centrifuge($d,$t){return 4*pow(pi(),2)*$d/pow($t,2);}

#pop
function alert($d){
add_head('jscode','ajaxCall("popup|core,txt","txt='.$d.'");');}

#sql//maintenance
function reflush($b,$o=''){
qr('alter table '.$b.' order by id');}
function reflush_ai($b){$id=lastid($b)+1;
qr('alter table '.$b.' auto_increment='.$id.'');}
function lastid($b){return sql('id',$b,'v','order by id desc limit 1');}
function trunc($b){qr('truncate '.$b);}
function drop($b){qr('drop table '.$b);}
function transpose($b,$bb,$r){$bb='z_'.$b.'_'.date('ymdHis');
qr('create table '.$bb.' like '.$b);
qr('insert into '.$bb.' select * from '.$b); return $bb;}
function sqlbcp($b,$d=''){$bb='z_'.$b.'_'.$d;
if(sqlex($bb))qr('drop table '.$bb);
qr('create table '.$bb.' like '.$b);
//qr('alter table '.$bb.' add primary key (id)');
qr('insert into '.$bb.' select * from '.$b);
return $bb;}
function sqlrestore($b,$d=''){$bb='z_'.$b.'_'.$d;
if(!sqlex($bb))return;
qr('truncate '.$b);
qr('alter table '.$b.' auto_increment=1');
qr('insert into '.$b.' select * from '.$bb.'');
return $b;}
function sqlex($b){$rq=qr('show tables like "'.$b.'"');
return mysqli_num_rows($rq)>0;}

#update structure
function trigger($b,$ra){$rb=sqlcols($b); $rnew=''; $rold='';
if(isset($rb['id']))unset($rb['id']); if(isset($rb['up']))unset($rb['up']);	
if($rb){$rnew=array_diff_assoc($ra,$rb); $rold=array_diff_assoc($rb,$ra);}//old
if($rnew or $rold){
	$bb=sqlbcp($b,date('ymdHis')); drop($b);
	$rtwo=array_intersect_assoc($ra,$rb);//common
	$rak=array_keys($ra); $rav=array_values($ra);
	$rnk=array_keys($rnew); $rnv=array_values($rnew); $nn=count($rnk);
	$rok=array_keys($rold); $rov=array_values($rold); $no=count($rok);
	$na=count($rnew); $nb=count($rold); $ca=array_keys($rtwo); $cb=array_keys($rtwo);
	if($na==$nb)for($i=0;$i<$nn;$i++)if($rnv[$i]==$rov[$i] or $rnv[$i]!='int'){
		$ca[]=$rnk[$i]; $cb[]=$rok[$i];}
	return 'insert into '.$b.'(id,'.implode(',',$ca).',up) select id,'.implode(',',$cb).',up from '.$bb;}}

function sqlcols($b,$o=''){//columns
$rq=qr('select COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS where table_name="'.$b.'";');
while($r=mysqli_fetch_assoc($rq)){$type=$r['DATA_TYPE'];
	if($type=='varchar')$type='var';
	if($type=='mediumtext')$type='text';
	if($type=='tinytext')$type='tiny';
	if($type=='bigint')$type='bint';
	if($type=='decimal')$type='dec';
	$rb[$r['COLUMN_NAME']]=$type;}
if(!isset($rb))return;
//0:[id,uid,txt,up];1:id,uid,txt,up;2:[uid,txt];3:uid,txt;4:[txt];5:txt,6:[0=>txt]
if($o==2 or $o==4 or $o==5 or $o==6)array_shift($rb);//or $o==3 
if($o==2 or $o==3 or $o==4 or $o==5 or $o==6)if(isset($rb['up']))array_pop($rb);//az,a,vk,k,v
if($o==4 or $o==5 or $o==6)unset($rb['uid']); if($o==7)array_shift($rb);
if($o==1 or $o==3 or $o==5)return implode(',',array_keys($rb));
if($o==6 or $o==7)return array_keys($rb);
return $rb;}

function sql_utf8($t){$r=sql('*',$t,'rr');//exec one time only on non-utf8 tables
foreach($r as $k=>$v){foreach($v as $ka=>$va)$rb[$k][$ka]=utf8_encode($va);
	sqlups($t,$rb[$k],$v['id']);}}

#create
function create_cols($r){$ret='';
$collate=ses('enc')?'collate utf8mb4_general_ci':'latin1_general_ci';
foreach($r as $k=>$v)
if($v=='int')$ret.='`'.$k.'` int(11),'."\n";
elseif($v=='bint')$ret.='`'.$k.'` bigint(36) NULL default NULL,'."\n";
elseif($v=='var')$ret.='`'.$k.'` varchar(1000) NOT NULL default "",';//'.$collate.' 
elseif($v=='tiny')$ret.='`'.$k.'` tinytext NOT NULL default "",';//'.$collate.' 
elseif($v=='text')$ret.='`'.$k.'` mediumtext,';// '.$collate.'
elseif($v=='date')$ret.='`'.$k.'` date NOT NULL,';
elseif($v=='dec')$ret.='`'.$k.'` decimal(20,20) NULL default NULL,'."\n";
elseif($v=='float')$ret.='`'.$k.'` float(20,20) NULL default NULL,'."\n";
elseif($v=='double')$ret.='`'.$k.'` double NULL default NULL,'."\n";
return $ret;}

//array('id'=>'int','ib'=>'int','val'=>'var');
function sqlcreate($b,$r,$up=''){
if(!is_array($r) or !$b)return; reset($r);
if($up)$sql=trigger($b,$r);
qr('create table if not exists `'.$b.'` (
  `id` int(11) NOT NULL auto_increment,'.create_cols($r).'
  `up` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM collate utf8_general_ci;');
if(isset($sql))qr($sql,1);}

function mkbcp($p){return sqlbcp($p['b']);}
function rsbcp($p){return restore($p['b']);}
function sqlcall($p,$db){$ret='';
$cols=val($p,'cols'); $mode=val($p,'mode');
$req=val($p,'req'); $see=val($p,'see');
$cols=str_replace('-',',',$cols);
return sql($cols,$db,$mode,$req,$see);}
function sqlclose(){mysqli_close(ses('dbq'));}

?>