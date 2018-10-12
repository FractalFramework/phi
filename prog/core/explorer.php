<?php
class explorer{
static $private=2;
//f:short url without extension
//u:full url

static function ico($f,$o=''){$k=ext($f);
$r=['.jpg'=>'image','.png'=>'image','.gif'=>'image','.php'=>'file-code-o','.txt'=>'file-word-o','.pdf'=>'file-pdf-o','.odt'=>'file-word-o','.mp3'=>'file-audio-o','.mp4'=>'file-video-o','.gz'=>'file-archive-o','.json'=>'file-excel-o',''=>'file'];
if(!$f)$ret='folder'; elseif(isset($r[$k]))$ret=$r[$k]; else $ret='file';
return ico($ret,$o);}

static function a($f){$xt=after($f,'.'); $d='';//app of file
if($xt=='php')$d='db'; elseif($xt=='json')$d='json'; else $d='folders'; return $d;}
static function read($f){$a=self::a($f); return $a::read(self::fu($f,1));}
static function write($f,$r){$a=self::a($f); return $a::write(self::fu($f,1),$r);}
static function secu($u){if(strpos($u,ses('user'))!==false or auth(6))return 1;}

//fullurl=disk/usr/dav (job)
//smarturl=usr/dav (system)
//shorturl=/dav (user)
static function fu($f,$o=''){
$dsk=ses('dsk'); if(!$dsk)$dsk=['disk','usr',ses('user')];
if($o)array_shift($dsk);//smart url
return implode('/',$dsk).$f;}

static function nod($app,$id){
$usr=sqljoin('name',$app::$db,'login','uid','v',$id);
return 'usr/'.$usr.'/'.$app.'/'.$id;}

#editable
//cell
static function sav_cell($p){
$f=val($p,'f'); $id=val($p,'id',0); list($k,$kb)=explode('-',$id); $v=val($p,'d'.$id);
$r=self::read($f); if(isset($r[$k][$kb]))$r[$k][$kb]=trim(delbr($v)); self::write($f,$r);
return self::nav($p);}

//row
static function sav_row($p){
$f=$p['f']; $ka=val($p,'k',0); $n=$p['n']; $rid=$p['rid'];
$r=self::read($f); if(isset($r[$ka]))$rk=$r[$ka]; else{$ka=max(array_keys($r))+1; $rk=current($r);}
if(isset($rk))foreach($rk as $k=>$v)$r[$ka][$k]=val($p,$rid.$k,'');
self::write($f,$r);
return self::nav($p);}

static function edit_row($p){$f=$p['f']; $ka=$p['k'];
$r=self::read($f); $ra=current($r); $n=count($ra); if($r[$ka])$r=$r[$ka]; $rid=randid();
for($i=0;$i<$n;$i++)$rb[]=$rid.$i; if($n)$prm=implode(',',$rb); else $prm='';
$bt=aj('xpl,,x|explorer,sav_row|f='.$f.',k='.$ka.',n='.$n.',rid='.$rid.'|'.$prm,langp('save'),'btsav');
$bt.=aj('xpl,,x|explorer,opsav|op=del_row,f='.$f.',nm='.$ka,langp('del'),'btdel');
if($ra)foreach($ra as $k=>$v)$ret[]=[$v,goodinput($rb[$k],val($r,$k))];
return $bt.mktable($ret);}

#datas
static function del_col($p){$f=val($p,'f'); $n=val($p,'n'); $f=before($f,'.');
if(!$n)return input('n','').aj('xpl|explorer,del_col|f='.$f.'|n',langp('ok'),'btdel');
$r=self::read($f); foreach($r as $k=>$v)unset($r[$n]); self::write($f,$r);
return self::nav($p);}

static function mkrow($r,$o=''){$rb=$r?current($r):[]; $n=count($rb); if($n==0)$ret[]='';
for($i=1;$i<=$n;$i++)if($o)$ret['_'][]=''; else $ret[]='';
if($o){$rb=$ret+$r; $r=$rb;} else $r[]=$ret;
return $r;}

#files
static function del($p){$f=val($p,'f'); $ok=val($p,'ok'); $u=self::fu($f);
if(!$ok)return aj('xpl|explorer,del|ok=1,f='.$f,langp('confirm deleting'),'btdel');
if(is_dir($u))rmdir($u);//rmdir_r($u);//if(self::secu($u))
elseif(is_file($u)){unlink($u); if(is_file('_bak/'.$u))unlink('_bak/'.$u);}
if(strpos($f,'/')===false)$f=''; else $f=before($f,'/');
return self::nav(['f'=>$f]);}

static function code($p){$f=val($p,'f'); $u=self::fu($f); $d=files::read($u);
$ret=textarea('nm',$d); $x=ext($u); //if($x=='.txt')
$ret.=aj('xpl|explorer,opsav|op=editxt,f='.$f.'|nm',pic('ok').langpi('save'),'btsav');
return $ret;}

//dirs
static function add_dir($p){$f=val($p,'f'); $u=self::fu($f);
if(!is_dir($u))mkdir_r($u);
return self::nav($p);}

//rename
static function rename_sav($p){$f=val($p,'f'); $nm=val($p,'nm');
if(substr($f,0,1)=='/')$f=substr($f,1);
$base=self::fu(''); $fa=$base.'/'.$f; $fb=$base.'/'.$nm;
if(!is_dir($fb))mkdir_r($fb); if($fb!=$fa)rename($fa,$fb);
return self::nav(['f'=>$nm]);}

//repair
static function repair($r){$er=self::errors($r);
if($er=='no')return [1=>['col1']];
if($er=='reord')return db::reorder($r);
if($er=='reset')return self::reset_header($r);
if($er)foreach($r as $k=>$v)$ret[$k]=array_pad($v,$er,''); return $ret;}

static function errors($r){if(!is_array($r))return 'no';
if(isset($r[0]))return 'reord'; if(isset($r['_k']))return 'reset';
$na=count(current($r)); $nb=0; $er='';
foreach($r as $k=>$v){$n=count($v); if($n!=$na)$er=1; $nb=$n>$nb?$n:$nb;}
if($er)return $nb;}

static function reset_header($r){$rk['_']=array_shift($r); return merge($rk,$r);}

//select (for tabler)
static function select($p){$a=val($p,'a'); $tg=val($p,'tg'); $nm=val($p,'nm');
$r=dir_scan(self::fu($nm)); $bck=strpos($nm,'/')?before($nm,'/'):'';
$rb[]=aj('slctdb|explorer,select|a='.$a.',tg='.$tg.',nm='.$bck,langpi('back').$bck,'licon grey');
if($r)foreach($r as $k=>$v){$vb=($nm?$nm.'/':'').before($v,'.'); $bt=ico('folder').$v;
	if(!is_numeric($k))$rb[]=aj('slctdb|explorer,select|a='.$a.',tg='.$tg.',nm='.$vb,$bt,'licon');
	else $rb[]=aj($tg.'|'.$a.',readb|f='.$vb,ico('file-o').$v,'licon');}
if(isset($rb))$ret=implode('',$rb);
return div($ret,'bkg');}

//displace
static function displace($p){
$f=val($p,'f'); $nm=val($p,'nm'); $r=dir_scan(self::fu($nm)); $bck=before($nm,'/');
if($f)$rb[]=aj('edit|explorer,displace|f='.$f.',nm='.$bck,langpi('back').$bck,'licon grey');
$rb[]=aj('xpl|explorer,rename_sav|f='.$f.',nm='.$nm.'/'.after($f,'/'),langp('save in').' '.$nm,'btsav').br();
if($r)foreach($r as $k=>$v)if(!is_numeric($k))$rb[]=aj('edit|explorer,displace|f='.$f.',nm='.($nm?$nm.'/':'').$v,ico('folder').$v,'licon');
if(isset($rb))$ret=implode('',$rb);
return div($ret,'bkg');}

//import
static function khead($r){
$rb=array_shift($r); $k=key($rb); pr($rb);echo $k;
if(!is_numeric($k) or $k==0){$rb['_']=array_keys($rb); $rb[1]=array_values($rb);}
foreach($r as $k=>$v)$rb[]=$v;
return $rb;}

//swap
static function swap($r,$d){
list($a,$b)=explode('-',$d); //$a-=1; $b-=1;
foreach($r as $k=>$rb)foreach($rb as $kb=>$vb){
if($kb==$a)$rc[$k][$kb]=$rb[$b]; elseif($kb==$b)$rc[$k][$kb]=$rb[$a]; else $rc[$k][$kb]=$vb;}
return $rc;}

static function fext($f,$o=''){
if($o)return substr($f,-4)!='.php'?$f.'.php':$f;
return substr($f,-4)=='.php'?before($f,'.'):$f;}

//operations
static function opsav($p){
$f=val($p,'f'); $op=val($p,'op'); $nm=trim(val($p,'nm')); $no=0;
$r=self::read($f);
switch($op){
case('add_head'):$r=self::mkrow($r,1); break;
case('del_head'):foreach($r as $k=>$v)$rb[$k+1]=$v; $r=$rb; break;
case('add_col'):foreach($r as $k=>$v)$r[$k][]=''; break;
case('del_col'):foreach($r as $k=>$v)unset($r[$k][$nm]); break;
case('add_row'):$r=self::mkrow($r); break;
case('del_row'):if(isset($r[$nm]))unset($r[$nm]); break;
case('reorder'):$a=self::a($f); $r=$a::reorder($r); break;
case('repair'):$r=self::repair($r); break;
case('rename_file'):return self::rename_sav($p); break;
case('rename_dir'):return self::rename_sav($p); break;
case('add_file'):$r=[1=>['col1']]; $f=self::fext($nm,1); $p['f']=$f; break;
case('add_dir'):return self::add_dir(['f'=>$nm]); break;
case('import'):$r=self::read(self::fext($nm,1)); $f=self::fext($f,1); break;
case('import_sql'):if($nm!='login')$r=sql('all',$nm,'rr','');
	array_unshift($r,sqlcols($nm,6)); break;//['lang'=>'fr']
case('import_html'):$d=tabler::trans($nm); $r=explode_array($d,'¬','|'); break;
case('backup'):$u=self::fu($f); $ub=self::fu('_bak/'.$f); mkdir_r($ub); copy($u,$ub); break;
case('restore'):$r=self::read('_bak/'.$f); break;
case('reset_header'):$r=self::reset_header($r); break;
case('request'):$ra=explode(';',$nm); $r=db::rq($ra[0],$f,$ra[1]); $no=1; break;
case('trunc'):$rk['_']=array_shift($r); $r=$rk;  break;
case('swap'):$r=self::swap($r,$nm); break;
case('editxt'): files::write(self::fu($f),$nm); $no=1; break;
default:$r='';}
if($r && !$no)self::write($f,$r);
return div(self::nav($p),'','xpl');}

static function opedt($p){$f=val($p,'f'); $op=val($p,'op'); $d=''; $inp='';
switch($op){
case('del_col'):$d=0; break;
case('code'):return self::code($p); break;
case('displace'):return self::displace($p); break;
case('del_file'):return self::del($p); break;
case('del_dir'):return self::del($p); break;
case('add_file'):$d=$f.'/'.lang('file',1); break;
case('add_dir'):$d=$f.'/'.lang('folder',1); break;
case('import_sql'):$d=''; break;
case('import_html'):$inp=divarea('','editarea','nm'); break;
case('request'):$d='0,1;0=login,3=fr'; break;
case('swap'):$d='0-2'; break;
default:$d=self::fext($f);}
$ret=$inp?$inp:input('nm',$d,'');
$ret.=aj('xpl|explorer,opsav|op='.$op.',f='.$f.'|nm',pic('ok').langpi($op),'btsav');
$ret.=aj('xpl|explorer,nav|f='.$f,langpi('cancel'),'btn');
return $ret;}

//actions
static function dtool($f){$ret=''; $is_f=strpos($f,'.'); $a=self::a($f);
if($is_f){$r=['displace','rename_file','del_file','code']; 
	if($a=='db'){$r[]='import'; $r[]='import_html'; if(auth(6))$r[]='import_sql'; $r[]='request';}}
else{$r[]='add_dir'; if($f)$r[]='rename_dir'; if($f)$r[]='del_dir'; $r[]='add_file';}
if(isset($r))foreach($r as $k=>$v)$ret.=aj('edit|explorer,opedt|op='.$v.',f='.$f,langph($v),'');
if($is_f){$ret.=popup('db,api|f='.ses('b').'/'.$f,ico('terminal'));}
return div($ret,'nbp').div('','','edit');}

static function ftool($f,$ra){$ret=''; $c=''; $xt=ext($f); $er='';
$r[]='backup'; if(is_file(self::fu('_bak/'.$f)))$r[]='restore';
if(isset($ra['_']))$r[]='del_head'; else{$r[]='reset_header'; $r[]='add_head';}
array_push($r,'reorder','add_row','add_col');//,'trunc'
if($xt=='.php' && $er=self::errors($ra)){$r=['repair']; $c='btdel';}
foreach($r as $k=>$v)$ret.=aj('xpl|explorer,opsav|op='.$v.',f='.$f,langph($v),$c);
$r=['del_col','swap'];
if(!$er)foreach($r as $k=>$v)$ret.=aj('edit|explorer,opedt|op='.$v.',f='.$f,langph($v),$c);
return div($ret.hlpbt('tabler_edit','',''),'nbp');}

//play table
static function reader($f,$r){$a=self::a($f); $xt=ext($f); $u=self::fu($f);
if(is_array($r))$ret=build::editable($r,'explorer,edit_row|f='.$f,'explorer,sav_cell|f='.$f);
//elseif($a=='json')$ret=build::recursive($r);//tag('pre','',print_r($r,true));
elseif($xt=='.jpg' or $xt=='.png')$ret=img($u);
elseif($xt=='.mp3' or $xt=='.mid')$ret=audio($u);
elseif($xt=='.mp4')$ret=video($u);
elseif($xt!='.php')$ret=nl2br($r);
return div($ret,'board');}

//play file
static function play($p){
$f=val($p,'f'); $xt=ext($f); $ret='';
$u=self::fu($f);
if(!is_file($u))return;
$r=self::read($f);
$bt=self::ico($f);
$bt.=span(before($f,'.'),'btxt').' - ';//lk('/'.$u,before(,'.')
$bt.=span(files::fsize(['f'=>$u]),'small').' - ';
$bt.=span(count($r).' '.lang('entries',1),'small').' - ';
$bt.=span(files::fdate(['f'=>$u,'o'=>'d/m/Y']),'small').' - ';
$ret.=div($bt,'stit');
if($xt=='.php' or $xt=='.json')$ret.=self::ftool($f,$r);
$ret.=self::reader($f,$r);
return $ret;}

/*static function tree($f){$r=explode('/',$f); $root=self::fu('',1); $ret=''; $c=''; $bt='';
$rb[]=aj('xpl|explorer,nav|f=',ico('database').$root,$c);
foreach($r as $k=>$v)if($v){$dr[]=$v; $dir=implode('/',$dr);
	if(strpos($v,'.'))$rb[]=aj('xpl|explorer,nav|f='.$dir,before($v,'.'),$c);//self::ico($v).
	else $rb[]=aj('xpl|explorer,nav|f='.$dir,$v,$c);}//ico('folder').
$ret.=implode('/',$rb);
return $bt.div($ret,'tree');}*/

static function navdr($p){
$fa=$p['fa']; $f=$p['f']; $ret=''; $ka=$p['k']; $va=$p['v']; $kb=$ka+1; $idb='dr'.$kb; 
//if(strpos($fa,'/'))$f0=after($fa,'/',1); else $f0='';//devine alone
$dr=self::fu($fa);
$r=dir_scan($dr); //self::fu($fa);
$rb=explode('/',$f); $ac=isset($rb[$kb])?$rb[$kb]:''; //echo $ac.' w '.$kb.' in '.$f; p($rb);
$fb=$fa.'/';//$fa?:'';//force to seek rootdir
if($r)foreach($r as $k=>$v)if($k!='_bak'){$c=$ac==$v?'active':'';
	if(is_numeric($k))$rf[]=aj('xpl|explorer,nav|f='.$fb.$v,self::ico($v,16).before($v,'.'),$c);
	else $rd[]=aj('xpl|explorer,nav|f='.$fb.$k.',k='.$kb,ico('folder-o',16).$k,$c);}
if(isset($rd))$ret.=implode('',$rd);
if(isset($rf))$ret.=implode('',$rf);
//$bt=self::dtool($f);
return div($ret,'lisb');}

static function nav($p){
$f=val($p,'f'); $u=self::fu($f,0); $ret=''; $bt='';
if(!self::secu($u))return;
//root from first authorized dir
$rb=explode('/',$f); //p($rb);
foreach($rb as $k=>$v){$fb[]=$v;
	$ret.=div(self::navdr(['fa'=>implode('/',$fb),'f'=>$f,'k'=>$k,'v'=>$v]),'','dr'.$k);}
$ret.=self::dtool($f);
$ret.=div('','','edit');
if(strpos($f,'.')!==false)$ret.=div(self::play($p),'','explay');
//$ret.=self::tree($f);
return div($ret,'','main');}

static function content($p){$ret='';
$b=val($p,'b',ses('b')); ses('b',$b?$b:'usr'); if($b=='x')$b=sez('b');
$_SESSION['dsk']=['disk',$b]; if(!auth(6))$_SESSION['dsk'][]=val($p,'c',ses('user'));
$dr=['db','json','usr','_','/','../img','../usr'];
if(auth(6))$ret=div(batch($dr,'ppl|explorer|b=$v',$b),'');//,'mem','mysql'
return div($ret.div(self::nav($p),'pan','xpl'),'','ppl');}

}