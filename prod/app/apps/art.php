<?php

class art{
static $private='0';
static $a='art';
static $db='articles';
static $cb='artwrp';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];
static $title='Tlex';
static $description='Articles';
static $image='';

function __construct(){
$r=['a','db','cb','cols'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']='1';
return appx::admin($p);}

static function injectJs(){return '
function format(p,o){document.execCommand(p,false,o?o:null);}
function fontsz(n){var txt=document.getSelection(); alert(txt);}';}

static function headers(){
//add_prop('og:title',self::$title);
//add_prop('og:description',self::$description);
//add_prop('og:image',self::$image);
//add_head('csscode','');
//add_head('jscode',self::injectJs());
}

//edit
static function wysiwyg($id){$ret=build::wysiwyg($id);
$ret.=self::editbt(['id'=>$id,'o'=>1]);
return div($ret,'sticky-edt','edt'.$id,'display:none;');}

static function wswg($id){
$ret=btj('[]',atj('embed_slct',['[',']',$id]),'btn');
$r=['h','b','i','u','q','k','url','web'];
foreach($r as $k=>$v)$ret.=btj(lang($v,1),atj('embed_slct',['[',':'.$v.']',$id]),'btn');
return div($ret);}

static function isabto($usr){return sql('id','tlex_ab','v',['ab'=>$usr,'usr'=>ses('user')]);}
static function vrfab($pub,$usr){if($pub==4)return 1; $ab=self::isabto($usr); if($pub==2 && $ab)return 1;}
static function gooduser($id){
	$r=sqljoin('uid,pub,name',self::$db,'login','uid','ra','where articles.id='.$id);
	//$r=sqlin('uid,pub,name','login',self::$db,'uid','ra',$id); pr($r);
	if(!$r)return;
list($uid,$pub,$usr)=valk($r,['uid','pub','name']);
if($uid==ses('uid'))return 1; return self::vrfab($pub,$usr);}

//sav
static function del($p){$id=val($p,'id'); $ok=val($p,'ok');
if(!self::gooduser($id))return lang('operation not permitted');
if(!$ok)return aj('art'.$id.'|art,del|id='.$id.',ok=1',langp('confirm deleting'),'btdel');
else{sqldel(self::$db,$id);
	sqldel('desktop','art,call|id='.$id,'com');}
return self::stream($p);}

static function mkpub($p){
sqlup(self::$db,'pub',val($p,'pub'),val($p,'id'));
return self::play($p);}

//save
static function untitled($p){$id=val($p,'id'); 
$tit=val($p,'tit'.$id,lang('title'));
$txt=val($p,'txt'.$id,lang('text'));
$pub=ses('uid')?0:1;
$savr=array('uid'=>ses('uid'),'tit'=>$tit,'txt'=>$txt,'pub'=>$pub);
$id=sqlsav(self::$db,$savr); $p['id']=$id;
$com='art,call|id='.$id;//desk
//$nid=sqlsav('desktop',[ses('uid'),'/documents/art','pop',$com,'file-o',$tit,2]);
return $id;}

static function create($p){
$r=['uid'=>ses('uid'),'tit'=>lang('title'),'txt'=>lang('text')];
$id=sql('id',self::$db,'v',$r);
if($id)$p['id']=$id; else $p['id']=self::untitled($p);
return div(self::call($p),'',self::$cb);}

static function savetxt($p){
$id=val($p,'id'); $tit=val($p,'tit'.$id);
$p['txt']=val($p,'txt'.$id,val($p,'txt-conn'.$id));
$own=self::gooduser($id);
if(!$own)return;
if(val($p,'conn'))$txt=$p['txt']; else $txt=trans::call($p);
if($tit){$tit=trim(strip_tags(delbr($tit,' ')));
	if(strlen($tit)>144)$tit=substr($tit,0,144);
	sqlup(self::$db,'tit',$tit,$id); return $tit;}
if($txt)sqlup(self::$db,'txt',trim($txt),$id,'id');
if(val($p,'conn'))return self::play($p);
return conn::read(['msg'=>$txt,'ptag'=>1]);}

//edit
static function editconn($p){$id=val($p,'id'); $rid=val($p,'rid');
if(!$id or !self::gooduser($id))return; $vid='txt-conn'.$id;
list($tit,$txt)=sql('tit,txt',self::$db,'rw',$id);
//$ret=aj('art'.$id.'|art,play|conn=1,id='.$id,pic('back'),'btn').' ';
$ret=aj('art'.$id.',,x|art,savetxt|conn=1,id='.$id.',rid='.$rid.'|'.$vid,langp('save'),'btsav').br();
$ret.=self::wswg($vid);
$ret.=textarea($vid,$txt,'64','28','','console').hidden('tit',$tit);
return $ret;}

static function playconn($p){//from utils.js
$ret=sql('txt',self::$db,'v',$p['id']);
return conn::read(['msg'=>$ret,'mth'=>'minconn','ptag'=>1]);
return nl2br(trim($ret));}

static function editbt($p){
if($p['o'])return btj(langpi('save'),atj('editbt',$p['id']),'btsav');
else return btj(langpi('edition'),atj('editbt',$p['id']),'btsav');}

static function privacy($p){$ret='';
$id=val($p,'id'); $rid=val($p,'rid'); $pub=val($p,'pub'); $r=appx::privacy_prm();
foreach($r as $k=>$v){$bt=$k==$pub?ico('check'):'';
	$ret.=aj('art'.$id.'|art,mkpub|id='.$id.',rid='.$rid.',pub='.$k,lang($v).$bt);}
return div($ret,'list');}

static function edition($p){$ret=''; $usr=ses('user');
$id=val($p,'id'); $rid=val($p,'rid'); $name=val($p,'name'); $pub=val($p,'pub');
$ret=aj(self::$cb.'|art,stream|rid='.$rid,langpi('back'),'btn');
if($rid)$ret.=insertbt(langp('use'),$id.':art',$rid);
$ret.=hlpbt('edit_art');
$ret.=btj(langpi('restore'),atj('restore_art',$id),'btn');
$ret.=span(self::editbt(['id'=>$id,'o'=>0]),'','bt'.$id);
$ret.=aj('popup|art,editconn|id='.$id.',rid='.$rid.',edit=1',ico('edit'),'btn');
$ret.=aj('art'.$id.'|art,del|id='.$id,langpi('delete'),'btdel');
$ret.=bubble('art,privacy|id='.$id.',rid='.$rid.',pub='.$pub,langpi('privacy'),'btn',1);
return div($ret,'');}

static function editable($p){
$txt=val($p,'txt'); $txb=trans::call($p);
if(strlen($txt)==strlen($txb))return 1;}

//appx
static function edit($p){return self::call($p);}

//play
static function build($p){
$id=val($p,'id'); $name=val($p,'name');
$date=val($p,'date'); $pub=val($p,'pub');
$title=val($p,'tit'); $txt=val($p,'txt');
if(ses('user')==$name)$own=1;
elseif(self::vrfab($pub,$name))$own=2;
else $own='';
//if($own)$own=self::editable($p);
$date=lk('/a/'.$id,$date,'');
$lnk=lkb('/art/'.$id,langpi('url'),'btn');
$prmb=['id'=>'tit'.$id,'class'=>'editoff','contenteditable'=>$own?'true':'false'];
if($own){$prmb['onclick']=atj('editxt',['tit',$id]);
	$prmb['onblur']=atj('savtxt',['tit',$id]);}
if($own)$ret['mnu']=div(self::edition($p),'');
$ret['t']=tag('h1',$prmb,$title);
if($own==2)$pub='('.$pub.') '; else $pub='';
$ret['by']=div(lkb('/u/'.$name,$name,'btxt').' '.$pub.$date.' '.$lnk,'small right');
$ret['edit']=self::wysiwyg($p['id']);
//$ret['edit']=span(self::wswg($p['id']),'connbt','edt'.$id,'display:none;');
$prm=['id'=>'txt'.$id,'class'=>'editoff','contenteditable'=>'off'];
if($own){$prm['ondblclick']=atj('editbt',[$id,1]);//
	//$prm['onblur']=atj('savtxt',['txt',$id]);//not work with wsyg
	//$prm['onblur']=atj('editbt',$id);
	$prm['onkeypress']='backsav(event,\''.$id.'\')';}
$txt=conn::read(['msg'=>$txt,'ptag'=>1]);
$rtx=tag('div',$prm,$txt);
$ret['m']=div($rtx,'article editarea');
self::$title=$title;
self::$description=preview($txt);
return implode('',$ret);}

static function play($p){$id=val($p,'id');
$cols='name,tit,txt,DATE_FORMAT('.self::$db.'.up,"%d/%m/%Y") as date,pub';
if($id)$r=sqljoin($cols,self::$db,'login','uid','ra','where '.self::$db.'.id='.$id);
//if($id)$r=sqlin($cols,'login',self::$db,'uid','ra',$id);
if(isset($r))$p=merge($p,$r);
$ret=self::build($p);
$apf=val($p,'appFrom');//$apf && 
if($p['id']){tlex::$title=self::$title;//meta
	tlex::$description=self::$description;
	tlex::$image='';}
return div($ret,'content','art'.$id,'');}

static function brut($p){$id=val($p,'id');
$cols='name,tit,txt,DATE_FORMAT('.self::$db.'.up,"%d/%m/%Y") as date,pub';
if($id)$r=sqljoin($cols,self::$db,'login','uid','ra','where '.self::$db.'.id='.$id);
$ret=conn::read(['msg'=>$r['txt'],'ptag'=>1]);
return $ret;}

//stream
static function stream($p){
return appx::stream($p);}

//call
static function read($p){return self::play($p);}//old

static function preview($p){$id=val($p,'id');
$r=sql('tit,txt',self::$db,'rw',$id); 
if(!$r)return;
$t=popup('art,call|id='.$id,span(pic('art',32).' '.$r[0]),'btxt');
//$t.=lk('/art/'.$id,pic('url'),'btxt');
$txt=conn::read(['msg'=>$r[1],'app'=>'conn','mth'=>'noconn','ptag'=>'no']);
$max=strlen($txt); if($max>140)$max=strpos($txt,'.',140);
if($max>240)$max=strpos($txt,' ',140);
$txt=substr($txt,0,$max+1).'...';
$ret=div($t,'app').div($txt,'stxt').div('','clear');
return div($ret,'panec');}

static function txt($p){$id=val($p,'id');
if($id)$txt=sql('txt',self::$db,'v',$id);
if($txt)return conn::read(['msg'=>$txt,'ptag'=>1]);}

static function tit($p){$id=val($p,'id');
if($id)return sql('tit',self::$db,'v',$id);}

static function call($p){$id=val($p,'id'); $ret='';
if($id){$p['id']=sql('id',self::$db,'v',$id);
	if($p['id'])$ret=self::play($p); else $ret=help('article not exists','board');}
return div($ret,'',self::$cb);}

static function com($p){
return appx::com($p);}

#content
static function content($p){
//self::install();
	return appx::content($p);}
}
?>
