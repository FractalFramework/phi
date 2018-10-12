<?php
//abstract app
abstract class appx{
static $private='1';
static $a='';//app name
static $db='';//data base
static $cb='';//callback name
static $cols=[];//cols of db
static $typs=[];//types of cols
static $conn='';//connectors
static $gen='';//motor
static $db2='';//db2
static $boot;
static $qb;

static function boot(){//$a=self::$a;
if(self::boot==null)self::$boot=new $a;}

static function install($p){$r['uid']='int';
sqlcreate(self::$db,merge($r,$p),1);}

#admin menus
static function admin($p){
$a=self::$a; $cb=self::$cb; $rid=val($p,'rid'); $o=val($p,'o');//obsolete
//if($rid)$r[]=['','j',$cb.'|tlxcall,menuapps|rid='.$rid,'',$a];
if($rid)$r[]=['','j',$cb.'|'.$a.',stream|rid='.$rid,'',$a];
if(val($p,'ob'))$r[]=['','j',$cb.'|'.$a.',menu|rid='.$rid,'folder-open','-'];
/*$r[]=['','j',$cb.'|'.$a.',stream|display=2,rid='.$rid,'list','-'];
if($o)$r[]=['','j',$cb.'|'.$a.',stream|display=1,rid='.$rid,'th-large','-'];*/
//else $r[]=['','j',$cb.'|'.$a.',stream|rid='.$rid,'','open'];
if(in_array('pub',self::$cols)){
	$r[]=['','j',$cb.'|'.$a.',stream|spread=2,rid='.$rid,'user','-'];
	$r[]=['','j',$cb.'|'.$a.',stream|spread=1,rid='.$rid,'users','-'];}
if(ses('uid'))$r[]=['','j',$cb.'|'.$a.',create|rid='.$rid,'plus','new'];
$r[]=['','bub','core,help|ref='.$a.'_app','question-circle-o','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f='.$a,'code','Code'];
if(auth(6)){
	$r[]=['admin/identity','pop','admin_lang,open|ref='.$a.',app='.$a,'lang','name'];
	$r[]=['admin/identity','pop','admin_help,open|ref='.$a,'help','name'];
	$r[]=['admin/identity','pop','admin_help,open|ref='.$a.'_app','help','help'];
	$r[]=['admin/identity','pop','admin_icons,open|ref='.$a,'picto','pictos'];
	$r[]=['admin/identity','pop','admin_labels,open|ref='.$a,'tag','label'];
	$r[]=['admin','pop','desktop,tlex_app|app='.$a,'desktop','tlex apps'];}
return $r;}

#titles to display in popup for each method
static function titles($p){
$d=val($p,'appMethod');
$r['content']='welcome';
$r['collect']='collected datas';
$r['play']=self::$a;
if(isset($r[$d]))return lang($r[$d]);
return val($p,'appMethod');}

#edit
static function privilege($name){//if usr ab to name
$ex=sql('id','tlex_ab','v',['usr'=>ses('user'),'ab'=>$name]);
if($ex)return 1;}

static function permission($db,$id,$pub){
$uid=sql('uid',$db,'v',$id);
if($uid==ses('uid') or $pub==4)return 1;
if($pub){$pub=sql('pub',$db,'v',$id);
	if($pub==2){$name=sql('name','login','v',$uid);
		return self::privilege($name);}}}

static function del($p){$id=$p['id'];
$a=self::$a; $db=self::$db; $cb=self::$cb;
$own=sql('id',$db,'v',['uid'=>ses('uid'),'id'=>$id]);
if($own!=$id)return help('operation not permited','alert');
if(val($p,'ok')){sqldel($db,$id); 
	if($db2=val($p,'db2'))sqldel($db2,$id,'bid');
	return $a::stream($p);}
$ret=aj($cb.'|'.$a.',del|ok=1,id='.$id,lang('confirm deleting'),'btdel');
$ret.=aj($cb.'|'.$a.',edit|id='.$id,lang('cancel'),'btn');
return $ret;}

static function batch_vals($p,$cols,$o=''){
if($o)$r=[ses('uid')];
foreach($cols as $k=>$v){
	if($v=='int')$default=0;
	elseif($v=='date')$default=date('Y-m-d',time());
	else $default='';
	$r[$k]=val($p,$k,$default);}
return $r;}

static function save($p){
$a=self::$a; $db=self::$db; $cb=self::$cb; $cols=sqlcols($db,4);
$r=self::batch_vals($p,$cols,1);
$p['id']=sqlsav($db,$r);
return $a::edit($p);}

static function modif($p){$id=val($p,'id');
$a=self::$a; $db=self::$db; $cols=sqlcols($db,4);
$pub=isset($cols['pub'])?1:0;
$r=self::batch_vals($p,$cols,0);//$pub=val($p,'pub');
$ok=self::permission($db,$id,$pub); if(!$ok)return lang('permission denied');
sqlups($db,$r,$id);
return $a::edit($p);}

//privacy
static function privacy_prm(){
return [0=>'private',1=>'clan-visible',2=>'clan-editable',3=>'all-visible',4=>'all-editable'];}
static function privacy($n){$r=self::privacy_prm(); return lang($r[$n]);}

static function pub($nm,$val,$uid){$ret=''; $r=self::privacy_prm();
if(!$uid or $uid==ses('uid'))foreach($r as $k=>$v){$chk=$k==$val?'checked':'';
	$atb=['type'=>'radio','name'=>$nm,'id'=>$nm.$k,'value'=>$k,'checked'=>$chk];
	$ret.=div(tag('input',$atb,'',1).label($nm.$k,lang($v)));}
else $ret=hidden($nm,$val).lang($r[$val],'nfo');
return $ret;}

//subform
static function subops($p){
$id=val($p,'id'); $idb=val($p,'idb'); $op=val($p,'op');
$cb=self::$cb; $a=self::$a; $db2=self::$db2;
if($op=='add'){$cols=sqlcols($db2,2);
	foreach($cols as $k=>$v)
		if($k=='bid')$rc[$k]=$id; elseif($k=='uid')$rc[$k]=ses('uid'); else $rc[$k]='';
	sqlsav($db2,$rc);}
elseif($op=='del')sqldel($db2,$idb);
elseif($op=='sav'){$cols=sqlcols($db2,6);
	$r=vals($p,$cols); sqlups($db2,$r,$idb);}
return self::subform($p);}
	
static function subedit_form($r){$ret='';
$ret=hidden('bid',$r['bid']); array_shift($r);
foreach($r as $k=>$v){
	$ret.=div(input($k,$v,63,$k,'',512));}
return $ret;}

static function subedit($p){
$id=val($p,'id'); $idb=val($p,'idb');
$a=self::$a; $cb=self::$cb; $j='id='.$id.',idb='.$idb;//.',bid='.$id
$cls=sqlcols(self::$db2,6); $cols=implode(',',$cls);
$r=sql($cols,self::$db2,'ra',$idb);
$ret=aj($cb.'sub|'.$a.',subops|'.$j,langp('back'),'btn');
$ret.=aj($cb.'sub|'.$a.',subops|'.$j.',op=sav|'.$cols,langp('save'),'btsav');
$ret.=aj($cb.'sub|'.$a.',subops|'.$j.',op=del',langp('delete'),'btdel');
//$ret.=div(input($cls[0],$r[$cls[0]],63,lang($cls[0]),'',512));
$ret.=div($a::subedit_form($r),'',$cb.'subedt');
return $ret;}

static function subform($p){$id=val($p,'id'); $ret='';
$a=self::$a; $cb=self::$cb; $db=self::$db; $db2=self::$db2;
$rc=sqlcols($db2,2); $cls=array_keys($rc); $cols=implode(',',$cls);
$r=sql('id,'.$cols,$db2,'rr',['bid'=>$id]);
$ret.=tag('h3','',lang('attached datas'));
if($r)foreach($r as $k=>$v){$bt=ico('edit').' '.$v[$cls[1]];
	$ret.=aj($cb.'sub|'.$a.',subedit|id='.$id.',idb='.$v['id'],$bt,'licon');}
$ret.=aj($cb.'sub|'.$a.',subops|op=add,id='.$id,langp('add'),'btn');
return div($ret,'',$cb.'sub');}

//form
static function formcom($p){$ret=''; $ty=val($p,'ty'); $answ=val($p,$ty);
if($answ){$rv=explode('|',$answ); $nb=count($rv); unset($p[$ty]);}
else $nb=val($p,'nb',2);
for($i=1;$i<=$nb;$i++){$inp[]=$ty.$i; $j=atj('multhidden',[$nb,$ty]);
	if(isset($rv[$i-1]))$v=$rv[$i-1]; else $v=val($p,$ty.$i);
	$prm=['id'=>$ty.$i,'value'=>$v,'size'=>40,'onkeyup'=>$j,'onclick'=>$j,'onchange'=>$j];
	$prm['placeholder']=lang('option').' '.$i;
	$ret.=div(tag('input',$prm,'',1));}
$inps=implode(',',$inp); $_POST[$ty]=$inps;
if($nb<20)$ret.=aj('choices|appx,formcom|ty='.$ty.',nb='.($nb+1).'|'.$inps,langp('add option'),'btn');
if(!val($p,'nb'))$ret=div($ret,'','choices').hidden($ty,$answ);
return $ret;}

static function form($p){$cb=self::$cb; $ret=''; $sz='34';//63
$cols=sqlcols(self::$db,4); $id=val($p,'id'); $uid=val($p,'uid'); $html=val($p,'html');
foreach($cols as $k=>$v){$val=val($p,$k); $a=self::$a; $bt=''; $no='';
	if($k==$html)$bt=divarea($val,'editarea',$k);
	elseif(val($p,'fc'.$k)){$f='fc_'.$k; $ret.=$a::$f($k,$val); $no=1;}
	elseif($k=='txt'){
		if(self::$conn)$bt=build::connwsg($k);
		if(self::$gen)$bt=build::genwsg($k);
		$bt.=textarea($k,$val,40,strlen($val)>500?26:8,'','',$v=='var'?512:0);}
	elseif($k=='pub')$bt=self::pub($k,$val,$uid);
	elseif($k=='com')$bt=self::formcom($p+['ty'=>$k]);
	elseif($k=='cl')$bt=build::toggle(['id'=>$k,'v'=>$val]);
	elseif($k=='nb')$bt=div(bar($k,$val,1,1,10),'inp');//,val($p,'barfunc')
	elseif($k=='nb1')$bt=div(bar($k,$val,1,1,100),'inp');
	elseif($k=='clr')$bt=input($k,$val,$sz,'','',512).pickbt($k);
	elseif($k=='img')$bt=input($k,$val,$sz,'','',512).desktop::pickim($k);
	elseif($k=='code')$bt=textarea($k,$val,40,26,'','console');
	elseif($v=='var')$bt=input($k,$val,$sz,'','',512);
	elseif($v=='text')$bt=textarea($k,$val,40,strlen($val)>500?26:8,'');
	elseif($v=='date')$bt=input($k,$val && $val!='0000-00-00'?$val:date('Y-m-d',time()),8,'');
	elseif($v=='int')$bt=input($k,$val,'8','',1);
	//extras
	if(val($p,'exec'.$k))$bt.=popup('exec,edit|rid='.$k.',ind='.$a.$id,langp('exec'),'btn');
	if(val($p,'bt'.$k))$bt.=val($p,'bt'.$k);
	if(!$no)$ret.=div($bt.label($k,lang($k)));}
if($sub=val($p,'sub')){$a=self::$a; $d=$a::subform($p);
	$ret.=hr().div($d,'',$cb.'sub');}//$a
return $ret;}

//admin	
static function create($p){
$id=val($p,'id'); $rid=val($p,'rid');
$a=self::$a; $cb=self::$cb; $cls=implode(',',self::$cols);
$ret=aj($cb.'|'.$a.',stream|rid='.$rid,pic('back'),'btn');
if($hlp=val($p,'help'))$ret.=hlpbt($hlp);
$ret.=aj($cb.'|'.$a.',save|rid='.$rid.'|'.$cls,lang('save'),'btsav').br();
$ret.=$a::form($p).br();
return $ret;}

static function edit($p){
$id=val($p,'id',val($p,'edit')); $rid=val($p,'rid'); 
$db2=val($p,'collect'); $uid=ses('uid'); $own=0; $sav=''; $qb=self::$qb;
$a=self::$a; $cb=self::$cb; $db=self::$db; $cls=implode(',',self::$cols); 
$t=val($p,'t',self::$cols[0]);
$r=sql('id,uid,'.$cls,$db,'ra',$id); if(!$r)return;
$pub=val($r,'pub'); $r['sub']=val($p,'sub');
$ok=self::permission($db,$id,$pub); if($r['uid']==$uid or auth(6))$own=1;
$ret=aj($cb.'|'.$a.',stream|rid='.$rid,pic('back').'#'.$r['id'],'btn');
if($rid)$ret.=insertbt(lang('use'),$id.':'.$a.'',$rid);
$ret.=aj($cb.'edit,,1|'.$a.',call|headers=1,id='.$id.',rid='.$rid,langp('view'),'btn');
if($own or $ok){$r['own']=1;
	$ret.=aj($cb.'|'.$a.',edit|id='.$id.',rid='.$rid,langp('edit'),'btn');
	if($hlp=val($p,'help'))$ret.=hlpbt($a.'_edit');//$help
	$ret.=aj('popup|tlxcall,keepsave|p1='.$id.',com='.$a.',tit='.$r[$t],langpi('desktop'),'btn');
	$sav=aj($cb.',,z|'.$a.',modif|id='.$id.',rid='.$rid.'|'.$cls,langp('save'),'btsav').br();}
if($own){
	if($db2)$ret.=aj($cb.'edit|'.$a.',collect|id='.$id.',db='.$db2,langpi('datas'),'btn');
	if($qb)$ret.=$qb::bt('/'.(auth(6)?ses('user').'/':'').$a.'/'.$id);//ses('user').
	$ret.=aj($cb.'edit|'.$a.',del|id='.$id.',rid='.$rid,langpi('delete'),'btdel');}
$ret.=lkb('/'.$a.'/'.$id,pic('url'),'btn');
$ret.=lkb('/api/'.$a.'/'.$id,pic('code'),'btn');
if($own or $ok)$ret.=div($sav.$a::form($r),'',$cb.'edit');
else $ret.=self::call($p);//arrived there by krack
return $ret;}

#collected datas
static function delb($p){
if(auth(4))sqldel($p['db'],$p['idb']);
return self::collect($p);}

static function collect($p){
$ra=sqlcols($p['db'],2); $cb=val($p,'cb',self::$cb);
unset($ra['bid']); $ra=array_keys($ra);
array_unshift($ra,'name'); array_unshift($ra,$p['db'].'.id');
$r=sqljoin($ra,$p['db'],'login','uid','rr',['bid'=>$p['id']],0);
if($r && self::$qb)db::save(self::$a,$p['id'],$r);//inform db
if(auth(6))foreach($r as $k=>$v)
	$r[$k]['del']=aj($cb.'edit|appx,delb|db='.$p['db'].',cb='.$cb.',id='.$p['id'].',idb='.$v['id'],pic('del'));
$r=array_merge([$ra],$r);
return mktable($r,1);}

#build
static function build($p){$id=val($p,'id');
$cols=implode(',',self::$cols); //$cols=sqlcols($db,2);
$r=sql('uid,'.$cols,self::$db,'ra',$id);
//tlex will use $conn; this var is sent by tlex::reader
if(isset($r['txt']) && self::$conn)$r['txt']=conn::call($r['txt'],1);
return $r;}

#play
static function template(){
return '[(tit)*class=tit:div][(txt)*class=txt:div]';
return '[[tit:var]*class=tit:div][[txt:var]*class=txt:div]';}

static function play($p){$ret='';
$r=self::build($p); $a=self::$a;
if(val($p,'pub'))$ret.=aj(self::$cb.'|'.$a.',stream|rid='.val($p,'pub'),pic('back'),'btn');
//if(val($p,'vue'))$ret.=vue::read($r,$a::template()); else
$ret.=gen::read($a::template(),$r);//gen by default
//if($qb=self::$qb && auth(6))$ret.=$qb::bt('datas/'.$a.$id);
return $ret;}

static function appmenu($a,$cb,$rid,$dsp,$spd){
$ret=aj($cb.'|'.$a.',stream|display=2,rid='.$rid,ico('list'),$dsp==1?'active':'');
$ret.=aj($cb.'|'.$a.',stream|display=1,rid='.$rid,ico('th-large'),$dsp==2?'active':'');
if(in_array('pub',self::$cols)){
$ret.=aj($cb.'|'.$a.',stream|spread=2,rid='.$rid,ico('user'),$spd==1?'active':'');
$ret.=aj($cb.'|'.$a.',stream|spread=1,rid='.$rid,ico('users'),$spd==2?'active':'');}
return $ret;}

static function stream_r($p){$ret='';
$a=self::$a; $db=self::$db;; $cols=self::$cols;
$t=val($p,'t',$cols[0]); $uid=ses('uid');
$spd=ses($a.'spd',val($p,'spread'));
$pub=in_array('pub',$cols)?1:0; if($spd==2)$pub='';
if($pub)$r=sqljoin($db.'.id,uid,'.$t.',pub,name,dateup',$db,'login','uid','rr','order by uid asc, id desc');
else $r=sql('id,uid,'.$t.',dateup',$db,'rr','where uid="'.$uid.'" order by id desc');
return $r;}

#stream //0=private,1=clan-visible,2=clan-editable,3=all-visible,4=all-editable
static function stream_datas($p){$rid=val($p,'rid'); $ret=''; $w=''; $pb='';
$a=self::$a; $cb=self::$cb; $cols=self::$cols;
$t=val($p,'t',$cols[0]); $uid=ses('uid');
$dsp=ses($a.'dsp',val($p,'display'));
$spd=ses($a.'spd',val($p,'spread'));
$rb['c']=$dsp==1?'bicon':'licon';
$pub=in_array('pub',$cols)?1:0; if($spd==2)$pub='';
$r=self::stream_r($p);
if($r)$rb['head']=span(self::appmenu($a,$cb,$rid,$dsp,$spd),'supright');
if($r)foreach($r as $k=>$v){$ok=1; $id=$v['id'];
	$tit=$v[$t]?$v[$t]:'#'.$v['id'];
	if($pub){$pb=$v['pub'];
		if($pb==2 or $pb==4)$lock=ico('unlock'); else $lock=ico('lock');
		if($v['uid']==$uid){$com='edit'; $ic='file-o';}
		else{$ic='file';
			if($pb==1 or $pb==2){
				$ex=self::privilege($v['name']);
				if($ex)$com=$pb==1?'call':'edit'; else $ok=0;}
			elseif($pb==3)$com='call'; elseif($pb==4)$com='edit'; else $ok=0;}}
	else{$com='edit'; $ic='file-o';}
	$ret[$id]['btn']=ico($ic).'#'.$v['id'].' '.$tit.' '.span($v['date'],'date');
	$ret[$id]['ico']=ico($ic);
	$ret[$id]['id']='#'.$v['id'];
	$ret[$id]['tit']=$tit;
	$ret[$id]['dat']=span($v['date'],'date');
	if($dsp==1)$lock='';
	if($pub)$ret[$id]['btn'].=' '.span(lang('by').' '.$v['name'].' '.$lock,'small');
	if($pub){
		$ret[$id]['by']=span(lang('by').' '.$v['name'].' '.$lock,'small');
	}
	if($ok)$ret[$id]['j']=$cb.'|'.$a.','.$com.'|id='.$v['id'].',rid='.$rid;}
if(!$ret)$ret['null']=help('no element','txt');
return [$ret,$rb];}

static function stream($p){
list($r,$rb)=self::stream_datas($p); $ret='';
if(isset($r['null']))return $r['null'];
if($r)$ret=$rb['head'];
foreach($r as $k=>$v)if(isset($v['j']))$ret.=aj($v['j'],$v['btn'],$rb['c']);
	//$ret.=$v['ico'].' '.$v['id'].' '.$v['tit'].' '.aj($v['j'],pic('see'));
return div($ret,'');}

#interfaces
//title (used by desktop and shares)
static function tit($p){$id=val($p,'id');
$t=val($p,'t',self::$cols[0]);
if($id)return sql($t,self::$db,'v',$id);}

//call (read)
static function call($p){$a=self::$a; $cb=self::$cb; $bt='';
$ret=$a::play($p);
if(!$ret)return help('id not exists','board');
if(self::own($p['id']))$bt=popup($a.'|edit=1,id='.$p['id'],ico('edit'),'right');
$bt.=lk('/'.$a.'/'.$p['id'],ico('link'),'right');
return $bt.div($ret,'',$cb.$p['id']);}

static function uid($id){return sql('uid',self::$db,'v',$id);}
static function own($id){if(self::uid($id)==ses('uid'))return true;}

//com (write)
static function com($p){$rid=val($p,'rid'); $a=self::$a; $ret='';//rid (will focus on tlex editor)
if(method_exists($a,'admin'))$ret=menu::call(['app'=>$a,'method'=>'admin','rid'=>$rid]);
$ret.=$a::content($p);
return $ret;}

static function iframe($p){$a=self::$a;
if(method_exists($a,'headers'))$a::headers();
$ret=div($a::play($p),'',self::$cb.$p['id']);
return generate().tag('body','',$ret);}

//interface
static function content($p){$ret='';
$a=self::$a; $cb=self::$cb;
$p['id']=val($p,'id',val($p,'p'));
$own=self::own($p['id']);
//if($p['id'] && $own)$ret=$a::edit($p);else
if(val($p,'edit'))$ret=$a::edit($p);
elseif(val($p,'add'))$ret=$a::create($p);
elseif($p['id'])$ret=$a::call($p);
elseif($a)$ret=$a::stream($p);
return div($ret,'board',$cb);}

//api
static function api($p){
$r=self::build($p);
return json_r($r,true);}//json_encode
}
?>