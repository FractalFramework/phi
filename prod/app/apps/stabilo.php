<?php
class stabilo{
static $private=0;
static $a='stabilo';
static $db='stabilo';
static $cb='stk';
static $cols=['tit','txt','pub'];
static $typs=['var','text','int'];
static $conn=0;
static $db2='stabilo_vals';
static $open=0;
static $qb='';//db
static $obso='';

function __construct(){
$r=['a','db','cb','cols','conn'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
sqlcreate(self::$db2,['uid'=>'int','bid'=>'int','start'=>'int','end'=>'int','pad'=>'var','txt'=>'var'],1);
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']=1; return appx::admin($p);}
//static function titles($p){return appx::titles($p);}
static function injectJs(){return;}
static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

static function titles($p){
$d=val($p,'appMethod');
$r['add_note']='add note';
$r['play']=self::$a;
if(isset($r[$d]))return lang($r[$d]);
return val($p,'appMethod');
return $ret;}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){$p['db2']=self::$db2; return appx::del($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function create($p){return appx::create($p);}

//subform
static function subops($p){return appx::subops($p);}
static function subedit($p){return appx::subedit($p);}
static function subform($p){return appx::subform($p);}
static function subedit_form($r){return appx::subedit_form($r);}

//form
static function form($p){return appx::form($p);}
static function edit($p){$p['collect']=self::$db2; return appx::edit($p);}

#edit
static function save_note($p){
$a=self::$a; $db=self::$db2; $cb=self::$cb; $cols=sqlcols($db,4);
$rid=$p['rid']; $p['txt']=$p[$rid];
$r=appx::batch_vals($p,$cols,1);
sqlsav($db,$r);
return self::play(['id'=>$p['bid']]);}

static function add_note($p){$rid=randid('stk');
$s=$p['start']; $e=$p['end']; $txt=$p['txt']; $id=$p['id'];
//$ret=help('save note');
$ret=div($txt,'helpxt','pad');
$ret.=textarea($rid,'');
$ret.=aj(self::$cb.$id.',,x|stabilo,save_note|bid='.$id.',rid='.$rid.',start='.$s.',end='.$e.'|pad,'.$rid,langp('save'),'btsav');
return $ret;}

static function del_note($p){$del=val($p,'del');
if($del)sqldel(self::$db2,$del);
return self::stream_notes($p);}

static function modif_note($p){
$a=self::$a; $db=self::$db2; $cb=self::$cb; $cols=sqlcols($db,4);
$id=$p['id']; $txt=$p['pad'.$id];
sqlup(self::$db2,'txt',$txt,$id);
return;}

static function edit_note($p){$id=val($p,'id');
$r=sql('pad,txt',self::$db2,'ra',$id);
$ret=div($r['pad'],'tit');
$ret.=textarea('pad'.$id,$r['txt']);
$ret.=aj('socket,,x|stabilo,modif_note|id='.$id.'|pad'.$id,langp('modif'),'btsav');
$ret.=aj('socket,,x|stabilo,del_notes|del='.$id,langp('del'),'btdel');
return $ret;}

static function stream_notes($p){
$id=val($p,'id'); $txt=sql('txt',self::$db,'v',$id); $uid=ses('uid'); $own=appx::own($id);
$rb['_k']=[lang('pad'),lang('note'),lang('edit'),lang('delete'),lang('error')];
if($own)$rb['_k'][]=lang('user');
if($id)$r=self::build2($p,$own);
if($r)foreach($r as $k=>$v){$ok=1;
	$s=$v['start']; $e=$v['end'];
	if($own)$usr=sql('name','login','v',$v['uid']);
	$ok=self::intersections($r,$v,$txt);
	$ex=strpos($txt,$v['pad']); if($ex===false)$ok=0;
	if(!$ok)$er=picto('alert'); else $er='';
	$sav=aj('popup|stabilo,edit_note|id='.$v['id'],picto('edit'),'');
	$del=aj('obso|stabilo,del_note|id='.$id.',del='.$v['id'],picto('del'),'');
	$rb[$k]=[$v['pad'],$v['txt'],$sav,$del,$er];
	if($own)$rb[$k][]=$usr;}
if(!$rb)return lang('empty');
else return div(mktable($rb),'','obso');}

#build
static function build($p){
return appx::build($p);}

static function build2($p,$o=''){
$w=$o?'':' and uid='.ses('uid');
$r=sql('id,uid,start,end,pad,txt',self::$db2,'rr','where bid='.$p['id'].$w.' order by start');
return $r;}

static function own_pad($id){
$uid=sql('uid',self::$db2,'v',$id);
if($uid==ses('uid'))return 1;}

static function usr_clr($uid){
return sql('clr','profile','v',['puid'=>$uid]);}

static function pad_read($p){$id=$p['id']; $bt='';
$r=sqljoin('name,pad,txt',self::$db2,'login','uid','ra','where '.self::$db2.'.id='.$id);
if(!$r)return lang('empty');
$bt=bubble('tlex,profile|usr='.$r['name'].',small=1',lang('by',1).' '.$r['name'],'grey small',1).' ';
if(self::own_pad($id))$bt.=popup('stabilo,edit_note|id='.$id,picto('edit'),'btn');//div($r['pad'],'helpxt').
return div(div($bt,'').div($r['txt'],'txt'),'helpxt');}

static function pad($p,$id){
$usr=sqlin('name','login',self::$db2,'uid','v','where '.self::$db2.'.id='.$id);
$clr=profile::init_clr(['usr'=>$usr]); $clr2=clrneg($clr,1);
$bt=bubble('stabilo,pad_read|id='.$id,$p,'');
$s='background-color:'.hexrgb($clr,0.3).';';// color:#'.$clr2.';
return span($bt,'stab','',$s);}

static function intersections($r,$v,$txt){
$s=$v['start']; $e=$v['end']; $pad=$v['pad']; $id=$v['id']; $ok=1;
foreach($r as $k=>$v)if($v['id']!=$id){
	if($s>=$v['end'] or $e<=$v['start'])$ok=1; else return 0;}
if(strpos($txt,$pad)===false)return 0;
return $ok;}

static function detection($ret,$v){static $dc=0;//decal because of previous results
$s=$v['start']; $e=$v['end']; $pad=$v['pad']; $id=$v['id'];
$n=mb_substr_count($ret,$pad); $pos=0; $s2=0; $dist=$e-$s; if($n)$dc+=10;
if($n==1)return str_replace($pad,'['.$pad.'*'.$id.':sticky]',$ret);//or search nearest
else for($i=0;$i<$n;$i++){$pos=mb_strpos($ret,$pad,$pos+1); if($pos-$s-$dc>=0 && !$s2)$s2=$pos;}
if($s2){$d1=mb_substr($ret,0,$s2); $d2=mb_substr($ret,$s2,$dist); $d3=mb_substr($ret,$s2+$dist);
	return $d1.'['.$d2.'*'.$id.':sticky]'.$d3;}
else return $ret;}

static function build_stick($p){
$r=self::build2($p,1); $txt=$p['txt'];
$ret=$txt; $rb=[]; $rtb=utf8_decode($ret);
if($r)foreach($r as $k=>$v){$ok=1;
	$ok=self::intersections($r,$v,$txt);
	if($ok)$ret=self::detection($ret,$v);
	else self::$obso[]=$v['id'];}
return $ret;}

static function privilege($r){
if($r['uid']==ses('uid') or $r['pub']==4)return 1; elseif($r['pub']<2)return 0;
$asr=sql('name','login','v',$r['uid']); return appx::privilege($asr);}

static function play($p){
$rid=randid('stk'); $id=$p['id'];
$r=self::build($p);
$txt=$r['txt']; $p['txt']=$txt;
$txt=self::build_stick($p);
$txt=conn::read(['msg'=>$txt,'ptag'=>1]);
$prm=['id'=>$rid,'class'=>'editarea padarea'];
$jb='useslct(this,\''.$rid.'\',\''.$id.'\');';
$editable=self::privilege($r);
if($editable)$prm['ondblclick']=$jb;
$ret=div($r['tit'],'tit');//if(self::$obso)
if($editable)$ret.=popup('stabilo,stream_notes|id='.$id,langp('my notes'),'btn');
$ret.=span(appx::privacy($r['pub']),'grey small');
$ret.=tag('div',$prm,$txt);
$ret.=jscode('var xid='.$id.';');
return $ret;}

static function stream($p){
//$p['t']=self::$cols[0];
return appx::stream($p);}

#call (read)
static function tit($p){
//$p['t']=self::$cols[0];
return appx::tit($p);}

static function call($p){
return appx::call($p);}

#com (edit)
static function com($p){return appx::com($p);}
static function uid($id){return appx::uid($id);}
static function own($id){return appx::own($id);}

#interface
static function content($p){
//self::install();
return appx::content($p);}

static function api($p){
return appx::api($p);}
}
?>