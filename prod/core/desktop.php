<?php

class desktop{
static $private='1';
static $db='desktop';

static function headers(){
//add_head('jscode','');
add_head('csscode','fieldset, legend{border:0; background:#ddd; width:44%; display:table-cell;}');}

static function admin(){
if(auth(4))$r[]=['','j','popup|desktop,manage','','manage'];
$r[]=['','bub','core,help|ref=desktop_app','help','-'];
return $r;}

static function install(){
sqlcreate(self::$db,['uid'=>'int','dir'=>'var','type'=>'var','com'=>'var','picto'=>'var','bt'=>'var','auth'=>'int']);}

//fill sql from existing apps
static function readapps(){
$dirs=read_dir('app');
if(is_array($dirs))foreach($dirs as $dir=>$files){
	if(is_array($files) && $dir)foreach($files as $k=>$file){
		if(is_string($file)){$app=before($file,'.');
			if($app)$private=isset($app::$private)?$app::$private:0;
			$dr='/phi/'.$dir;
			if(!$private or ses('auth')>=$private)
				$r=['uid'=>'0','dir'=>$dr,'type'=>'','com'=>$app,'picto'=>ics($app),'bt'=>$app];
				$nid=sqlsav(self::$db,$r);}}}}

static function reload(){
return aj('page|desktop',lang('reload'),'btn');}

#admin
//displace
static function savemdfdr($p){
$where=auth(6)?' or uid="0"':'';
$r=sql('id,dir',self::$db,'rr','where uid="'.ses('uid').'"'.$where);
if($p['mdfdr'])
foreach($r as $k=>$v){$vb=str_replace($p['dir'],$p['mdfdr'],$v['dir']);
	if($vb!=$v['dir'])
		qr('update desktop set dir="'.$vb.'" where id="'.$v['id'].'"');}
return desk::load('desktop','com',before($p['mdfdr'],'/'));}

static function modifdir($p){$sz=val($p,'sz',8);
$j=ajs('div,page,2|desktop,savemdfdr','dir='.$p['dir'],'mdfdr');
$prm=['type'=>'text','id'=>'mdfdr','value'=>$p['dir'],'size'=>$sz,'onblur'=>$j];
$ret=tag('input',$prm,'',1);
return $ret;}
//rename
static function savemdfbt($p){
if(auth(6) && $p['id'])sqlup(self::$db,'bt',$p['mdfbt'],$p['id']);
return desk::load('desktop','com',$p['dir']);}

static function modifbt($p){
$r=sql('bt,dir',self::$db,'ra','where id="'.$p['id'].'"');
$j=ajs('div,page,2|desktop,savemdfbt','id='.$p['id'],'mdfdr');
$prm=array('type'=>'text','id'=>'mdfbt','value'=>$r['bt'],'size'=>8,'onblur'=>$j);
$ret=tag('input',$prm,'',1);
return $ret;}
//del
static function del($p){
$nid=sqldel(self::$db,$p['id']);
return self::manage($p);}
//update
static function update($p){
$keys='dir,type,com,picto,bt'; $r=explode(',',$keys);
foreach($r as $k=>$v)sqlupdate(self::$db,$v,$p[$v],$p['id']);
//return lang('updated').' '.self::reload();
return self::manage($p);}

static function edit($p){$ret='';
$keys='dir,type,com,picto,bt';
$r=sql($keys,self::$db,'ra','where id="'.$p['id'].'"');
foreach($r as $k=>$v)$ret.=goodinput($k,$v).' '.label($k,$k).br();
$ret.=aj('dskmg|desktop,update|id='.$p['id'].'|'.$keys,lang('save'),'btsav');
$ret.=aj('dskmg|desktop,del|id='.$p['id'],langp('del'),'btdel');
return div($ret,'','dskdt');}

static function save($p){
$r=sqlcols(self::$db,2);
foreach($r as $k=>$v)$rb[$k]=val($p,$k);
$nid=sqlsav(self::$db,$rb);
if($nid)self::manage($p);}

static function add($p){
$r=sqlcols(self::$db,2);
$keys=implode(',',array_keys($r)); unset($r['uid']);
$ret=hidden('uid',ses('uid'));
foreach($r as $k=>$v)$ret.=input($k,$k,16,1).br();
$ret.=aj('dskpop|desktop,save||'.$keys,lang('add'),'btn');
return div($ret,'','dskpop');}

static function tlex_app($p){$app=val($p,'app'); $ret='';
$ex=sql('dir',self::$db,'v','where dir like "/apps/tlex/%" and com="'.$app.'"');
$rb=['uid'=>ses('uid'),'dir'=>'/apps/tlex','type'=>'','com'=>$app,'picto'=>ics($app),'bt'=>$app,'auth'=>2];
if(!$ex)$nid=sqlsav(self::$db,$rb);
else $ret=aj('popup|desktop,del|id='.$ex,langp('delete'),'btdel');
return $ret.aj('popup|desktop|dir='.$ex,lang('desktop').$ex,'btn');}

//edit on place
static function mdfbtn($p){
if($p['col']=='picto')$btn=ico($p['val']).' '; else $btn=$p['val'];
return aj($p['cbk'].'|desktop,modif|id='.$p['id'].',col='.$p['col'].',val='.jurl($p['val']).',cbk='.$p['cbk'],$btn,'btn');}

static function savemdf($p){$p['val']=$p[$p['idv']];
sqlup(self::$db,$p['col'],$p['val'],$p['id']);
return self::mdfbtn($p);}

static function modif($p){
$idv='mdf'.$p['id'].$p['col'];
$js=ajs('div,'.$p['cbk'].',2|desktop,savemdf','cbk='.$p['cbk'].',id='.$p['id'].',col='.$p['col'].',idv='.$idv,$idv);
$r=array('type'=>'text','id'=>$idv,'value'=>$p['val'],'size'=>16,'onblur'=>$js);
$ret=tag('input',$r,'',1);
return $ret;}

//manage
static function manage($p){$ret=''; $ra=''; $dir=val($p,'dir');
if(isset($p['addrow'])){$r=sqlcols(self::$db,2);
	foreach($r as $k=>$v)$rb[$k]='';
	$rb['uid']=ses('uid'); $rb['dir']=$dir;
	$nid=sqlsav(self::$db,$rb);}
if(auth(4))$ret=aj('dskmg|desktop,manage|dir='.$dir.',addrow=1',langp('add'),'btn');
//$ret.=aj('dskmg|desktop,manage|dir='.$dir,langp('refresh'),'btn');
//$ret.=aj('popup|desktop,readapps|'.lang('reflush apps'),'btn');
//table
if(auth(2))$keys='id,dir,type,com,picto,bt,auth'; else $keys='id,dir,picto,bt,auth';
$kr=explode(',',$keys); $n=count($kr);
if($dir)$wh=' and dir like "'.$dir.'%"'; else $wh='';
$r=sql($keys,self::$db,'','where (uid="'.ses('uid').'") and auth<="'.ses('auth').'" '.$wh.' order by id');
foreach($r as $k=>$v){
	//$ra[$k][0]=aj('popup|desktop,edit|id='.$v[0],$v[0],'btn');
	for($i=1;$i<$n;$i++){$cbk='inp'.$k.$i;//public can edit $v[6]
		if($kr[$i]=='picto')$ti=ico($v[$i]);
		else $ti=strlen($v[$i])>20?substr($v[$i],0,16).'...':$v[$i];
		if($kr[$i]=='com')$v[$i]=jurl($v[$i]);
		$bt=aj($cbk.'|desktop,modif|dir='.$dir.',id='.$v[0].',col='.$kr[$i].',val='.$v[$i].',cbk='.$cbk,$ti,'btn');
		$ra[$k][]=span($bt,'',$cbk);}
	$ra[$k][]=aj('dskmg|desktop,del|dir='.$dir.',id='.$v[0],pic('delete'),'btdel');}
$modes=hlpbt('desktop_modes','mode','btn');
$icons=aj('popup|fontawesome','icon');
$auth=hlpbt('desktop_auth','auth','btn');
if(auth(4))$rk=array('root',$modes,'app',$icons,'button',$auth);
else $rk=array('root',$icons,'button',$auth);
if($ra)array_unshift($ra,$rk); else $ra[]=$rk;
$ret.=mktable($ra);
return div($ret,'','dskmg');}

//$r[]=array('dir','//j/in/lk','app','method','icon');
static function com(){
//$r=sqlcols(self::$db,3); unset($r['uid']); $keys=implode(',',array_keys($r));
$keys='id,dir,type,com,picto,bt,auth'; $w='where uid="'.ses('uid').'"';
if(auth(4))$w.=' or (dir="/apps/tlex" and auth<'.ses('auth').') ';
$w.=' or dir like "/system%" or dir like "/public%"';
return sql($keys,self::$db,'id',$w.'order by dir');}

static function picker($p){$css='bicon'; $usid=ses('uid'); $id=val($p,'id'); $ret='';
$r=sql('id,dir,type,com,picto,bt,auth','desktop','id','where uid="'.$usid.'" and auth<="'.ses('auth').'" and dir like "/documents/img%" order by id desc');// limit 100
if($r)foreach($r as $k=>$v)if($v[1]=='img'){$f='img/full/'.$v[2];
	$im=tlex::playthumb($v[2],'micro',1); $bt=btj($im,atj('insert',[$v[2],$id]));
	if(is_file($f))$ret.=div($bt.span($v[4]),$css);}
return div($ret,'board');}

static function pickim($id){return popup('desktop,picker|id='.$id,pic('desktop'));}

//content
static function content($p){$ret='';
//self::install();
$ret=desk::load('desktop','com',val($p,'dir'));
if(val($p,'dir') && !$ret)$ret=desk::load('desktop','com','');
return $ret;}
}