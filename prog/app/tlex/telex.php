<?php
class telex{
static $private=0;
static $width=590;
static $objects=0;
//static $db='telex';
static $db='telex_xt';
static $title='Telex';
static $description='Telex is an objects social network';
static $image='http://tlex.fr/usr/telex/telex.png';
static $usr='';

//install
static function install(){
Sql::create(self::$db,['uid'=>'int','txt'=>'var','lbl'=>'int','ib'=>'int','ko'=>'int']);
Sql::create('telex_ab',['usr'=>'var','ab'=>'var','list'=>'var','wait'=>'int','block'=>'int'],1);
Sql::create('telex_web',['url'=>'var','tit'=>'var','txt'=>'var','img'=>'var']);
Sql::create('telex_lik',['luid'=>'int','lik'=>'int']);
Sql::create('telex_rpt',['rpuid'=>'int','tlxid'=>'int']);
//typntf:1=quote,2=reply,3=like,4=subsc,5=chat,6=subsc-approve
Sql::create('telex_ntf',['4usr'=>'var','byusr'=>'var','typntf'=>'int','txid'=>'var','state'=>'int']);}

static function titles($p){
$d=val($p,'appMethod'); if(!$d or $d==1)return;
$r['content']='welcome';
$r['read']='telex';
if(val($p,'ntf'))$r['read']='notifications';
$r['editor']='publish';
if($type=val($p,'type')=='bers')$r['subscrptn']='subscribers';
else $r['subscrptn']='subscriptions';
$r['objplayer']='object player';
if(isset($r[$d]))return $r[$d];}

#headers		
static function injectJs(){
if(ses('uid'))return '
var activelive=1; var nbnew=0; var reloadtime=10000;
setTimeout("telexlive(0)",500);
setTimeout("telexlive(1)",3600000);';}

static function headers(){
Head::add('meta',array('attr'=>'property','prop'=>'og:title','content'=>self::$title));
Head::add('meta',array('attr'=>'property','prop'=>'og:description','content'=>self::$description));
Head::add('meta',array('attr'=>'property','prop'=>'og:image','content'=>self::$image));
Head::add('csslink','/css/telex.css');
Head::add('csscode','body{background:#'.ses('clr'.self::$usr).';}');
Head::add('jslink','/js/telex.js');
Head::add('jscode',self::injectJs());}

#ajax
static function refresh($p){$own=ses('user');
$p['count']=1; $recents=self::api($p); $w='where 4usr="'.$own.'" and state=1';
$notifs=Sql::read('count(id)','telex_ntf','v',$w.' and typntf in (1,2,3)');//quote,reply,like
$subscr=Sql::read('count(id)','telex_ntf','v',$w.' and typntf=4');//subscr
$approv=Sql::read('count(id)','telex_ntf','v',$w.' and typntf=6');//approve
$chat=Sql::read('count(id)','telex_ntf','v',$w.' and typntf=5');//chat
return $recents.'-'.$notifs.'-'.$subscr.'-'.$approv.'-'.$chat;}

#saves
static function savemetas($d){
$r=self::playmetas($d); $f=''; $txt='';
if(!$r){$r=Html::metas($d);
	if($r[1])$txt=str_replace(array('“','”'),'',$r[1]);
	if($r[2])$f=File::saveimg($r[2],'web','590','400'); else $f='';
	if($r[0])Sql::insert('telex_web',[$d,$r[0],$txt,$f]);}
return $r;}

//build connectors
static function build_conn($d,$o=''){$ret='';
$d=clean_n($d);
$d=str_replace("\n",' (nl) ',$d);
$r=explode(' ',$d);
foreach($r as $v){
	if(substr($v,0,1)=='@'){$v=substr($v,1); $ret[]='['.$v.':@]'; $_POST['ntf'][$v]=1;}
	elseif(substr($v,0,1)=='#')$ret[]='['.substr($v,1).':#]';
	elseif(is_img($v)){
		$f=File::saveimg($v,'tlx','590','400');
		$ret[]='['.($f?$f:$v).':img]';}
	elseif(substr($v,0,4)=='http'){
		$v=before($v,'?utm',1);
		$xt=extension($v);
		if($xt=='.mp3')return '['.$v.':audio]';
		elseif($xt=='.mp4')return '['.$v.':mp4]';
		//elseif($p=='http')return Conn::href($d,'btlk','',1);
		$metas=self::savemetas($v);
		if(Video::provider($v))$ret[]='['.$v.':video]';
		elseif($metas)$ret[]='['.$v.':web]';
		else $ret[]='['.$v.':url]';}
	else $ret[]=$v;
	$conn=substr($v,0,1)=='['?1:0;
	if($conn && substr($v,-4)==':id]' && $id=substr($v,1,-4)){
		$usr=Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$id);
		if($usr)$_POST['ntf-r'][$usr]=1;}}//notify
if($ret)$d=implode(' ',$ret);
$d=str_replace(' (nl) ',"\n",$d);
$d=str_replace(':img]'."\n",':img]',$d);
return trim($d);}

static function save($p){$txt=val($p,$p['ids']); $_POST['ntf']='';
$lbl=val($p,'lbl'); if($lbl && !is_numeric($lbl))
	$lbl=Sql::read('id','labels','v','where ref="'.$lbl.'"');
if($oAuth=val($p,'oAuth')){$ok=Sql::read('puid','profile','v','where oAuth="'.$oAuth.'"');
	if($ok)sez('uid',$ok); else return 'error';}
if($txt){$txt=self::build_conn($txt,1);
	$id=Sql::insert(self::$db,array(ses('uid'),$txt,$lbl,val($p,'ibs'),''));
	if(isset($_POST['ntf']))self::saventf($id,1,'ntf');
	if(isset($_POST['ntf-r']))self::saventf($id,2,'ntf-r');}
if(val($p,'apicom'))return isset($id)?$id:'error';
return self::read($p);}

static function modif($p){$txt=val($p,$p['ids']); $id=val($p,'id');
if($id && $txt)Sql::update(self::$db,'txt',$txt,$id);
return self::read($p);}

#editor	
static function realib($id){
$d=Sql::read('txt',self::$db,'v','where id='.$id);
if(strpos($d,':id]'))$id=segment($d,'[',':id]');
return $id;}

static function upload($rid){
return '<form id="upl" action="" method="POST" onchange="upload(\''.$rid.'\')">
<label class="uplabel btn"><input type="file" id="upfile" name="upfile" multiple />
'.pic('image').'</label></form>';}

//publish
static function editor($p){
$ib=val($p,'ib'); $idv=val($p,'idv'); $rid=randid('ids');
$ret=''; $appsbt='';
if($qo=val($p,'quote'))$msg='['.self::realib($qo).':id]';
elseif($to=val($p,'to'))$msg='@'.$to.' ';
elseif($id=val($p,'id'))$msg=Sql::read('txt',self::$db,'v','where id='.$id);
else $msg=val($p,'msg');
if($ib)$ret.=div(lang('in-reply to').' '.$to,'grey');
if($qo)$ret.=div(lang('repost'),'grey');
$js='strcount1(\''.$rid.'\',144); resizearea(\''.$rid.'\');';
$r=array('class'=>'area','id'=>$rid,'onkeyup'=>$js,'onmousedown'=>$js,'placeholder'=>'message');
//if(!$ib && !$qo && !$to)$r['onfocus']='togglediv(\'edtsv\',1);';
$ret.=tag('textarea',$r,$msg);//form
$count=span('144','btxt small','strcnt'.$rid).' ';
$ja='div,tlxbck,resetform|telex,save|ibs='.$ib.',ids='.$rid.'|'.$rid.',lbl';
$prm['onclick']='closediv(\'tlxapps\'); closediv(\'lbcbk\');'; $prm['id']='edtbt';
$prm['data-prmtm']='tm='.ses('user');//'current';//return to current prmtm
$sav=span($count.aj($ja,langp('publish'),'btsav',$prm),'right').' ';//save
if(!$ib && !$qo && !$to && !$id){
	$tg=dropdown('tlxcall,menuapps|rid='.$rid,langph('applications'),'btn');
	$tg.=self::upload($rid);
	$tg.=dropdown('tlxcall,ascii|rid='.$rid,pic('smile-o'),'btn');//'&#128522;'
	//if(auth(4))$tg.=toggle('tlxapps|fontawesome,com|id='.$rid,pic('fort-awesome'),'btn');
	$tg.=dropdown('tlxcall,labels',langph('labels'),'btn');
	$tg.=span(span('','','lblxt'),'','lbcbk').hidden('lbl',0);
	$sav.=div($tg,'edtbt');//embed used to close others
	$sav.=div('','','tlxapps','');//put editor here if use object mode
	$ret.=div($sav,'','edtsv');}//,'display:none;'
else{$ret.=div($sav,'','edtsv');}
	//$ret.=btj(pic('close'),'closediv(\'pn'.$idv.'\')');
$ret.=div('','clear');
return $ret;}

static function redit($p){$id=val($p,'id'); $rid=randid('ids');
$msg=Sql::read('txt',self::$db,'v','where id='.$id);
$js='strcount1(\''.$rid.'\',144); resizearea(\''.$rid.'\');';
$r=['class'=>'area','id'=>$rid,'onkeyup'=>$js,'onmousedown'=>$js,'cols'=>44];
$ret=tag('textarea',$r,$msg);//form
$count=span(144-mb_strlen(html_entity_decode($msg)),'btxt small','strcnt'.$rid).' ';
$ja='tlx'.$id.',,x|telex,modif|id='.$id.',ids='.$rid.'|'.$rid;
$ret.=span($count.aj($ja,langp('modif'),'btsav'),'right').' ';
return $ret;}

#players
static function playmetas($d){
return Sql::read('tit,txt,img','telex_web','rw','where url="'.$d.'"');}

static function playlink($d){
$d=http($d); $r=self::playmetas($d); $t=domain($d);
$r=array('href'=>$d,'title'=>$r[1],'class'=>'btlk','target'=>'_blank');
return tag('a',$r,$t);}

static function playweb($d,$o=''){
$d=http($d); $r=self::playmetas($d); //$r=self::savemetas($d);
if($o){$p=Video::provider($d); if($p)$id=Video::extractid($d,$p);}
$dom=domain($d);
$t=$r[0]?$r[0]:$dom;
$lk=href($d,$t,'btlk',1);
if(!$r)return $lk;
if(substr($r[2],0,4)=='http'){$f=$r[2];
	if($r[2])$imx=@getimagesize($r[2]); else $imx[0]='x';
	if($imx[0]>590)$img=img($r[2],'590'); else $img='';}
elseif($r[2])$f=self::thumb($r[2],'full'); else $f='';
if($imx=@getimagesize($f)){
	if($imx[0]>590)$img=img('/'.$f,'590');
	elseif($imx[0])$img=img('/'.self::thumb($r[2],'mini'),100,100,'artim');} else $img='';
if($dom=='1nfo.net')$apc='playphilum'; else $apc='playweb';
$j='telex,objplayer|popwidth=550px,obj='.$apc.',p1='.nohttp($d);
if($img && isset($id))$ban=pagup('Video,call|p='.$p.',id='.$id,$img,'');
elseif($img)$ban=pagup($j,$img,''); else $ban=$img;
if($dom=='1nfo.net')$bt=' '.pagup($j,langp('read'),'grey').' '; else $bt='';
$url=href($d,pico('url').$dom,'grey');
$ret=div($t,'bold').div($r[1],'stxt').div($url.$bt).div('','clear');
$ret=div($ret,'pncxt');
return div($ban.$ret,'panec');}

static function playphilum($p){
$id=after($p,'/');
$f='http://1nfo.net/api/id:'.$id.',preview:3';
$d=File::get($f);
$r=json_decode($d,true);
$ret=div($r[$id]['title'],'tit').div(html_entity_decode($r[$id]['content']),'txt');
$ret.=href(http($p));
return $ret;}
	
static function playquote($id){
$r=self::api(['id'=>$id]);
if(!$r)return div(lang('telex_deleted'),'paneb');
$v=$r[0]; $v['idv']='qlx'.$id;
$ret=self::panehead($v,'popup');
$ret=tag('header',['class'=>''],$ret);
$ret.=Conn::load(['msg'=>$v['txt'],'app'=>'telex','mth'=>'reader','opt'=>'it2']);
return div($ret,'paneb',$v['idv']);}

static function thumb($f,$dim){$dr='img/';
$fb='medium/'.$f; $big=is_file($dr.$fb);
if($dim=='mini' or $dim=='micro')$im='mini/'.$f;
elseif($dim=='medium')$im=$big?$fb:'full/'.$f; else $im='full/'.$f;
if(is_file($dr.$im))if(filesize($dr.$im))return $dr.$im;}

static function playthumb($f,$dim,$o='',$c=''){
$sz=590; if($dim=='micro')$sz=64;
if(substr($f,0,4)=='http')$f=File::saveimg($f,'tlx',$sz);
$fb=self::thumb($f,$dim);
if($o)return img('/'.$fb,$sz);
if($f)return imgup('img/full/'.$f,img('/'.$fb,$sz),$c);}

static function url($p,$o,$e=''){$t=$o?$o:domain($p);
//$pop=popup('telex,objplayer|obj=playweb,p='.$p.',o='.$o,$t,'btlk');
return href($p,$t.' '.pic('external-link'),'',$e);}

static function objplayer($p){$func=$p['obj'];
return self::$func(val($p,'p1'),val($p,'p2'));}

//connectors
static function reader($d,$b=''){
list($p,$o,$c)=readconn($d);
if(is_img($d))return img($d,'','',$o);
switch($c){
	case('@'):return dropdown('telex,profile|usr='.$p,'@'.$p,'btlk'); break;
	case('#'):return aj('pagup|telex,search_txt|srch='.$p,'#'.$p,'btlk'); break;
	case('b'):return tag('strong',['class'=>$o],$p); break;
	case('i'):return tag('em',['class'=>$o],$p); break;
	case('q'):return tag('blockquote',['class'=>$o],$p); break;
	case('red'):return tag('red',['class'=>$o],$p); break;
	case('list'):return Conn::mklist($p); break;
	case('id'):if(is_numeric($p))return self::playquote($p); break;
	case('url'):return self::url($p,$o,''); break;
	case('link'):return self::playlink($p); break;
	case('img'):if(Conn::$one!=1)$ret=self::playthumb($p,'full'); else $ret='';
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('web'):if(Conn::$one!=1)$ret=self::playweb($p); else $ret=self::playlink($p);
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('video'):if(Conn::$one!=1)$ret=self::playweb($p,1); else $ret='';
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('audio'):return audio($p); break;
	case('mp4'):return video($p); break;
	case('philum'):if(Conn::$one!=1)$ret=self::playphilum($p); else $ret='';
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('art'):return href('/art/'.$p,article::tit(['id'=>$p]),'btlk'); break;
	case('article'):self::$objects[$c][]=[$p,$o]; return; break;
	case('chat'):self::$objects[$c][]=[$p,$o]; return; break;
	case('gps'):self::$objects[$c][]=[$p,$o]; return; break;
	case('app'):return aj('popup|'.$p.'|param='.$o,pico($p,32),'btn'); break;
	case('open'):if(method_exists($p,$o))return $p::$o; break;
	case('picto'):return picto($p,$o?$o:24); break;
	case('pic'):return pic($p,$o?$o:24); break;
	case('ascii'):return '&#'.$p.';'; break;
	default:if(method_exists($c,'call')){self::$objects[$c][]=[$p,$o];
		return App::open($c,['appMethod'=>'call','brut'=>1,'id'=>$p]);} break;
}
return '['.$d.']';}

//objects
static function objects(){$ret=''; $r=self::$objects; $sz='24'; $css='licon';//36 from css
if($r)foreach($r as $kr=>$vr){$imok=0;
foreach($vr as $k=>$v){$fc=''; $ic=''; list($p,$o)=$v;
switch($kr){
case('img'):if($imok)$ret.=self::playthumb($p,'micro'); $imok=1; break;
case('article'): $t=Sql::read('tit','articles','v','where id='.$p);
	$ret.=pagup('article,read|popwidth:550px,tlx=1,id='.$p,pic('file-text-o',$sz).span($t),$css);
	break;
case('chat'):$rb=Sql::read_inner('name','chatlist','login','ruid','rv','where roid='.$p);
	if($rb)$with=lang('with').' '.implode(', ',$rb); else $with='';
	$ret.=aj('pagup,,,1|chat|param='.$p.',headers=1',pic('comments',$sz).span($with),$css); break;
case('gps'):$t=Gps::com(['coords'=>$p]);
	$ret.=pagup('map,com|coords='.$p,pic('map-marker',$sz).span($t),$css); break;}
if($ic)$t=pic($ic,$sz).span($t);//when called method need an interface
if($fc)$ret.=pagup('telex,objplayer|obj='.$fc.',p1='.$p.',p2='.$o,$t,$css);}}
if($ret)return div($ret,'');}//panec

#search	
static function search_txt($p){
$srch=val($p,'srch'); $ret='';
$r=self::api(['srh'=>$srch]);
if($r)foreach($r as $k=>$v)$ret.=self::pane($v,1);
else $ret=help('no results','board');
return $ret;}

static function searchbt(){
$r=['type'=>'text','id'=>'srch','placeholder'=>lang('search'),'onkeypress'=>'SearchT(\'srch\')'];
return tag('form',['id'=>'srchfrm','name'=>'srchfrm','action'=>'javascript:Search(\'srch\');'],tag('input',$r,'',1)).div('','','cbksrch');}

#like
static function savelike($p){$id=val($p,'id'); $lid=val($p,'lid'); $nlik=val($p,'nlik');
if($lid){Sql::delete('telex_lik',$lid); $p['lid']='';}
else{$p['lid']=Sql::insert('telex_lik',[ses('uid'),$id]); tlxcall::saventf1($p['name'],$id,3);}
return self::likebt($p);}
	
static function likebt($p){$rid=randid('lik'); $mylik=''; $sty='';
$id=val($p,'id'); $lid=val($p,'lid'); $n=''; $nlik='';
if($lid){
	$nlik=Sql::read('count(id)','telex_lik','v','where lik='.$id);
	$mylik=Sql::read('id','telex_lik','v','where lik='.$id.' and luid='.ses('uid'));
	if($mylik)$sty='color:#e81c4f;';}
$bt=pic('heart',$sty,'like','','',span($nlik,'liknb'));
$ret=aj($rid.'|telex,savelike|id='.$id.',lid='.$mylik.',name='.$p['name'],$bt);
return span($ret,'',$rid);}

#follow
static function followbt($p){$rid=val($p,'rid',randid('flw'));
$usr=val($p,'usr'); $sm=val($p,'small'); //$wait=val($p,'wait');//vcu
$w='where usr="'.ses('user').'" and ab="'.$usr.'"';
$id=Sql::read('id','telex_ab','v',$w);
$rb=Sql::read('wait,block','telex_ab','ra',$w);//contexts:user see visitor (ucv),
if($id){
	if($rb['wait'])$flag='pending'; elseif($rb['block'])$flag='blocked'; else $flag='unfollow';
	$bt=$sm?pico($flag):langph($flag);
	$ret=dropdown('tlxcall,follow|chan=1,usr='.$usr.',rid='.$rid,pico('menu'),'');
	$ret.=aj($rid.'|tlxcall,follow|usr='.$usr.',unfollow='.$id.',rid='.$rid,$bt,'btdel');}
else{$bt=$sm?pico('follow'):langph('follow');
	$ret=dropdown('tlxcall,follow|chan=1,usr='.$usr.',rid='.$rid,$bt,'btsav');}
$c=val($p,'rid')?'':'followbt';
return span($ret,$c,$rid);}

#subscriptions
static function subscribt($usr,$uid){
if(!$uid)$uid=Sql::read('id','login','v','where name="'.$usr.'"');
$n0=Sql::read('count(id)',self::$db,'v','where uid="'.$uid.'"');
$n1=Sql::read('count(id)','telex_ab','v','where usr="'.$usr.'"');
$n2=Sql::read('count(id)','telex_ab','v','where ab="'.$usr.'"');
$bt=div(div(lang('published telex'),'subscrxt').div($n0,'subscrnb'),'subscrbt');
$ret=self::loadtm('tm='.$usr.',noab=1',$bt,'');//timbck
$bt=div(div(lang('subscriptions'),'subscrxt').div(span($n1,'','tlxabs'),'subscrnb'),'subscrbt');
$ret.=aj('tlxbck|tlxcall,subscrptn|type=ption,usr='.$usr.'|tlxabs',$bt);
$bt=div(div(lang('subscribers'),'subscrxt').div(span($n2,'','tlxsub'),'subscrnb'),'subscrbt');
$ret.=aj('tlxbck|tlxcall,subscrptn|type=ber,usr='.$usr.'|tlxsub',$bt);
$ret.=hidden('tlxsubnb',$n2).hidden('tlxabsnb',$n1).div('','clear');
return div($ret,'subscrstats').div('','clear');}

#profile	
static function profile($p){$ret='';
$usr=val($p,'usr'); $uid=val($p,'uid'); $sm=val($p,'small');
if($usr){$ret=div(profile::read($p),'','prfl');
	//if(ses('user')!=$usr)$ret.=div(self::followbt(['usr'=>$usr]),'subscrbt');
	if(ses('user')==$usr && !$sm)$ret.=div(self::subscribt($usr,$uid),'subscrban');}
if($ret)return div($ret,'profile');}

static function avatar($p){$im=val($p,'avatar');
$clr=sesif('clr'.val($p,'name'),Clr::random());
$f=profile::avatar_im($im,'mini',$clr);
return profile::divim($f,'avatarsmall',$clr);}

#notifications
static function saventf($id,$type,$o){$r=$_POST[$o];
if($r)foreach($r as $k=>$v)if($k!=ses('user'))$sql[]=[$k,ses('user'),$type,$id,'1'];
if(isset($sql))Sql::insert2('telex_ntf',$sql); $_POST[$o]='';}

static function readntf($v){$n=$v['typntf']; $by='@'.$v['byusr']; $ret='';
//$uname=Sql::read('name','login','v','where usr="'.$v['byusr'].'"');
if($v['state']==1)Sql::update('telex_ntf','state','0',$v['ntid']);
if($n==1 && $v['ib'])$ret=$by.' '.lang('has_reply',1); 
elseif($n==1)$ret=$by.' '.lang('has_sent',1);
elseif($n==2)$ret=$by.' '.lang('has_repost',1);
elseif($n==3)$ret=$by.' '.lang('has_liked',1);
return div($ret,'ntftit');}

#channels
static function chanread($usr){
return Sql::read('distinct(list)','telex_ab','rv','where usr="'.$usr.'" and wait=0 and block=0');}
static function chanbt(){$ret=self::loadtm('tm='.ses('user'),lang('all'));
//$r=sesclass('telex','chanread',ses('user'));//todo reactive after subscr
$r=self::chanread(ses('user'));
if($r)foreach($r as $v)$ret.=self::loadtm('tm='.ses('user').',list='.$v,$v);
return div($ret,'list');}

#labels
static function lablread(){
	return Sql::read('labels.id,ref','labels','kv','inner join '.self::$db.' on lbl=labels.id
	inner join profile on puid=uid where privacy=0 or (puid="'.ses('uid').'" and privacy=1)');}
static function lablbt(){$ret='';//self::loadtm('labl=',lang('all'));
	$r=self::lablread();//sesclass('telex','lablread');
	if($r)foreach($r as $k=>$v)$ret.=self::loadtm('labl='.$k,$v);
	return div($ret,'list');}

#desktop
static function desktop($p){$css=val($p,'mode','licon');
$bt=href('/desktop/'.ses('user'),pic('link'),'btxt');
$ret=div($bt,'right');
$r=Sql::read('id,dir,type,com,picto,bt,auth','desktop','id','where uid="'.ses('uid').'" and auth<="'.ses('auth').'" and dir="/documents" order by id desc limit 10');
if($r)foreach($r as $k=>$v){
	if($v[1]=='img'){$f='img/full/'.$v[2];
		if(is_file($f))$ret.=imgup($f,self::playthumb($v[2],'micro',1).span($v[4]),$css);}
	elseif($v[1]=='pop')$ret.=aj('popup,,,1|'.$v[2].',headers=1',pic($v[3],24).span($v[4]),$css);
	elseif($v[1]=='pag')$ret.=aj('pagup,,,1|'.$v[2].',headers=1',pic($v[3],24).span($v[4]),$css);
	elseif($v[1]=='lk')$ret.=href('/app'.$v[2],'',$css,1);
	else $ret.=aj($v[2],pic($v[3],24).span($v[4]),$css);}
else $ret=div(lang('desktop'),'btit');
return $ret.div('','clear');}

#pub
static function pub(){
$r=['newsnet','socialsys','socialgov'];
foreach($r as $v)$ret[]=self::profile(['usr'=>$v,'small'=>'1']);
return implode('',$ret);}

#read
static function relativetime($sec){$time=ses('time')-$sec;
$ret=lang('there_was').' ';
if($time>864000)$ret=strftime('%a %d %b',$sec);
if($time>86400)$ret=strftime('%d %b',$sec);
elseif($time>3600)$ret.=floor($time/3600).'h ';
elseif($time>60)$ret.=floor($time/60).'min ';
else $ret.=$time.'s';
return span($ret,'small');}

//thread
static function thread_parents($id,$ret=''){
$ib=Sql::read('ib',self::$db,'v','where '.self::$db.'.id="'.$id.'"',0);
if($ib){$ret[$ib]=1; $ret=self::thread_parents($ib,$ret);}
return $ret;}
static function thread_childs($id){
return Sql::read('id',self::$db,'k','where ib='.$id,0);}
static function sql_thread($id){
$ids=self::thread_childs($id);
$ids=self::thread_parents($id,$ids);
$ids[$id]=1; ksort($ids);
if($ids)$r=array_keys($ids);
if(isset($r))return 'where ('.self::$db.'.id='.implode(' or '.self::$db.'.id=',$r).')';}

//pane
static function panehead($v,$tg){$id=$v['id']; $idv=$v['idv']; $usr=$v['name'];
$name=href('/'.$usr,tag('strong','',$v['pname']),'btxt').' ';
$usrnm=span('@'.$usr,'grey'); if(ses('user'))
$usrnm=aj('pn'.$idv.'|telex,editor|idv='.$idv.',to='.$usr,span('@'.$usr,'grey'));
$time=self::relativetime($v['now']).' ';
$url=href('/id/'.$id,pic('link',12),'grey');
$date=pagup('telex,read|popwidth=500px,th='.$id,$time,'grey');
$ico=$v['icon']?pic($v['icon']):'';
if($v['ref'])$label=span(span(ucfirst($v['ref']),'tx').$ico,'label right'); else $label='';
//if(auth(6))
if($usr==ses('user'))
	$url.=aj('popup|telex,redit|id='.$id,pico('edit'));
if($v['ib']){
	$to=Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$v['ib']);
	$url.=' '.pagup('telex,read|th='.$id,lang('in-reply to').' '.$to,'grey').' ';}
if($nb=Sql::read('count(id)',self::$db,'v','where ib='.$id))
	$url.=pagup('telex,read|th='.$id,$nb.' '.lang($nb>1?'replies':'reply'),'grey');
return div($name.' '.$usrnm.' - '.$date.' '.$url.' '.$label,'username');}

static function panefoot($v,$tg){
$id=$v['id']; $idv=$v['idv']; $pr='pn'.$idv; $usr=$v['name']; $ret='';
$ret.=toggle($pr.'|telex,editor|idv='.$idv.',to='.$v['name'].',ib='.$id,picit('reply','to reply'));
$ret.=toggle($pr.'|telex,editor|idv='.$idv.',quote='.$id,picit('retweet','quote'));
$ret.=self::likebt($v);
$ret.=toggle($pr.'|tlxcall,share|id='.$id,picit('share-alt','share'));
if($usr==ses('user'))
	$ret.=toggle($pr.'|tlxcall,del|idv='.$idv.',did='.$id,picit('bolt','delete','del'));
if(self::$objects)
	$ret.=toggle($pr.'|tlxcall,keep|idv='.$idv.',id='.$id,picit('download','keep'));
$ret.=toggle($pr.'|tlxcall,report|idv='.$idv.',id='.$id.',cusr='.$usr,picit('warning','report'));
$ret.=toggle($pr.'|tlxcall,translate|id='.$id,picit('exchange','translate'));
return $ret;}

static function pane($v,$current=''){$id=$v['id']; $usr=$v['name'];
if($current){$v['idv']='plx'.$id; $tg='popup,,x';}
else{$v['idv']='tlx'.$id; $tg='popup';}
$avatar=bubble('telex,profile|usr='.$usr.',small=1',self::avatar($v),'btxt',1);
$head=self::panehead($v,$tg);
self::$objects='';
if($v['ko']){$msg=div(help('telex_banned'),'alert'); self::$objects='';}
else{$msg=Conn::load(['msg'=>$v['txt'],'app'=>'telex','mth'=>'reader','ptag'=>1]);
$msg.=div(self::objects(),'objects');}
$msg=div($msg,'message');
if(ses('uid'))$foot=div(self::panefoot($v,$tg),'actions'); else $foot='';
$ret=div($avatar,'bloc_left').div($head.$msg.$foot,'bloc_content');
$ret.=div('','','pn'.$v['idv']);
if($current==$id)$css='pane hlight';
elseif(isset($v['typntf']))$css=$v['state']==1?'pane hlight':'pane';
else $css='pane';
$sty='border:4px solid #'.$v['clr'].';'; $sty='';
if(isset($v['typntf']))$ret=self::readntf($v).$ret;
$ret=div($ret,$css,$v['idv'],$sty);
if($current==$id){self::$title='telex.fr/'.$id.' by @'.$usr; self::$description=strip_tags($msg);
	self::$image='http://tlex.fr/img/mini/'.$v['avatar'];}
return $ret;}

static function readusr($p,$usr){//authorized to watch
$prv=Sql::read('privacy','profile','v','where pusr="'.$usr.'"');
if($prv){
	$id=Sql::read('id','telex_ab','v','where usr="'.ses('user').'" and ab="'.$usr.'" and wait=0 and block=0');
	if(!$id)return div(pic('lock').hlpxt('private account'),'pane');
	else return self::read($p);}
else return self::read($p);}

static function read($p){$ret=''; $id='';//$id will be in popup
$last=val($p,'from'); $th=val($p,'th'); $id=val($p,'id');
$rs=val($p,'rs'); $ib=val($p,'ib'); 
if($th && !$last)$id=$p['th']=$th;//thread
elseif($id && !$last)$p['id']=$id;//one
elseif($rs && !$last)$id=$p['rs']=$rs;//current+childs
elseif($ib && !$last)$p['ib']=$ib;//childs
else{$usr=val($p,'usr',ses('user')); $tm=val($p,'tm');//timeline
	$p['from']=$last; $p['tm']=$tm?$tm:$usr;}
if(isset($p))$r=self::api($p); //pr($r);
if(isset($r))foreach($r as $k=>$v)$ret.=self::pane($v,$id);
//if(!$ret)if(isset($usr) && $usr==ses('user'))$ret=tlxcall::zero_telex();
return $ret;}

#api
static function sql_timeline($usr,$from,$list,$noab,$since,$labl,$count){$sq='';
if(!$noab && !$labl){$sqa=$list?' and list="'.$list.'"':'';
	$r=Sql::read('ab','telex_ab','rv','where usr="'.$usr.'"'.$sqa.' and wait=0 and block=0',0);
	if($r)$sq=' or name="'.implode('" or name="',$r).'"';}
if($labl)$ret='where labels.id="'.$labl.'"';
elseif($list && !$noab)$ret='where ('.substr($sq,4).')';
elseif($usr==ses('user'))$ret='where (txt like "%@'.$usr.' %" or name="'.$usr.'"'.$sq.')';
else $ret='where (((txt like "%@'.$usr.' %" or name="'.$usr.'") and (privacy="0" or uid="'.ses('uid').'")))';//'.$sq.'
if($from)$from='and '.self::$db.'.id<'.$from.'';
elseif($since)$from='and '.self::$db.'.id>'.$since.'';
$limit=$count?'':'group by '.self::$db.'.id order by '.self::$db.'.id desc limit 20';//
return $ret.' '.$from.' '.$limit;}//and no!=1 

static function api($p){
$p=vals($p,['tm','th','id','ib','srh','ntf','from','list','noab','since','labl','count']);
if($p['count']){$cols='count('.self::$db.'.id)'; $vmode='v';}
else{$cols=self::$db.'.id,uid,name,txt,unix_timestamp('.self::$db.'.up) as now,ib,telex_lik.id as lid,pname,avatar,clr,ref,icon,privacy,ko'; $vmode='rr';}
$inn='left join login on login.id=uid 
left join profile on puid=uid 
left join telex_lik on '.self::$db.'.id=lik 
left join labels on lbl=labels.id ';
if($p['since'])$since=' and '.self::$db.'.id>'.$p['since']; else $since='';
if($p['id'])$where='where '.self::$db.'.id='.$p['id'].$since;
elseif($p['ib'])$where='where ib='.$p['ib'].$since;
elseif($p['th'])$where=self::sql_thread($p['th']).$since;
elseif($p['srh'])$where='where ((name="'.$p['srh'].'" or txt like "%'.$p['srh'].'%") and (privacy=0 or uid="'.ses('uid').'"))'.$since.' order by id desc limit 20';
elseif($p['ntf']){$cols.=',telex_ntf.id as ntid,byusr,typntf,state';
	$inn.='inner join telex_ntf on txid='.self::$db.'.id ';
	$minid=$p['since']?' and '.self::$db.'.id>'.$p['since']:'';
	$limit=$p['count']?'':' order by '.self::$db.'.up desc limit 20';
	$where='where 4usr="'.ses('user').'"'.$minid.''.$limit;}
else $where=self::sql_timeline($p['tm'],$p['from'],$p['list'],$p['noab'],$p['since'],$p['labl'],$p['count']);
return Sql::read($cols,self::$db,$vmode,$inn.$where,0);}//1=verbose

//http://tlex.fr/api.php?app=telex&mth=call&prm=tm:dav
static function call($p){$r=self::api($p);
foreach($r as $k=>$v){self::$objects='';
	$r[$k]['avatar']='http://tlex.fr/img/full/'.$v['avatar'];
	$r[$k]['txt']=Conn::load(['msg'=>$v['txt'],'app'=>'telex','mth'=>'reader']);
	$r[$k]['objects']=self::objects();}
return json_r($r);}

//load button
static function loadtm($p,$t,$c='',$tg=''){
if($c)$r['class']=$c; if(!$tg)$tg='tlxbck';
$r['onclick']='refresh();';
$r['data-prmtm']=$p; $r['onmousedown']='ajbt(this)';
$r['data-j']='div,'.$tg.',,resetscroll|telex,read|'.($p=='current'?'':$p);
return tag('a',$r,$t);}
	
static function vrfusr($d){return Sql::read('id','login','v','where name="'.$d.'"');}
static function vrfid($d){return Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id="'.$d.'"');}

#content
static function content($p){$badusr=''; $badid=''; $dsk=''; $pub='';
$own=ses('user'); $desk=val($p,'desk'); $chat=val($p,'chat'); 
$ntf=val($p,'ntf'); $art=val($p,'art');
//self::install();
//profile::install();
//if(ses('dev')=='prog')self::$db='telex_xt';//alternative table
if(!$own)$login=login::com(['auth'=>2]);
else $login=Menu::call(array('app'=>'tlxcall','method'=>'profilemenu'));
//usr
$usr=val($p,'usr'); $id=val($p,'id',val($p,'th')); if(is_numeric($usr)){$id=$usr; $usr='';}
if($usr){$okusr=self::vrfusr($usr); if(!$okusr){$badusr=1; $usr='';} else $p['usr']=$usr;}
if(!$usr && $own)$p['usr']=$own;
//id
if($id){$okid=self::vrfid($id); if(!$okid){$badid=1; $id='';} else{$p['id']=$id; $usr=$okid;}}
//nav
$nav=span($login,'right');
$nav.=span(href('/',langph('tlex'),'btn abbt'),'');//span('TELEX','microsys')
//$nav.=self::loadtm('tm='.$usr,langph('telex'),'btn abbt');
if(ses('uid')){
	$bt=langph('notifications').span('','nbntf','tlxntf');
	//$nav.=aj('tlxbck|telex,read|ntf=1|tlxntf',$bt,'btn abbt');
	$nav.=self::loadtm('ntf=1',$bt,'btn abbt');
	$bt=langph('messages').span('','nbntf','tlxmsg');
	$nav.=pagup('chat,com|headers=1',$bt,'btn abbt');
	$nav.=ajtime('telex,lablbt','',langph('labels'),'btn abbt');}
if($own)$nav.=ajtime('telex,chanbt','usr='.$usr.',list='.ses('list'),langph('lists'),'btn abbt');
if(ses('uid'))$nav.=aj('pblshcnt|telex,searchbt',langph('search'),'btn abbt');
//profile
$bigprofile='';
if($badid or $badusr)$profile=help('telex','board');
elseif($usr && !$badusr)list($bigprofile,$profile)=profile::read(['usr'=>$usr,'big'=>'1']);
elseif(!$own or $usr or $id or $desk)$profile='';//div(help('welcome'),'board');
else $profile=self::profile(['usr'=>$own,'face'=>'1']);
//dashboard
if(auth(1) && !$desk)$dsk=div(self::desktop($p),'board','dsk');
elseif(!$own && !$usr && !$id)$pub=div(self::pub(),'');
$credits=help('credits','board',1);
//publish
if($own && !$id && !$usr && !$badid && !$badusr && !$art && !$desk)
	$publish=div(self::editor(''),'pblshcnt','pblshcnt');
else $publish='';
//refreshbt
$bt=span('','nbntf','tlxrec').lang('new telex');
if($own && $own==$p['usr'])$refresh=self::loadtm('current',$bt,'refreshbt');
elseif($own && $own!=$usr)$refresh=href('/'.$own,lang('back'),'refreshbt');
else $refresh='';
//stream
if($badid)$stream=help('404iderror');
elseif($badusr)$stream=help('404usrerror');
elseif($art)$stream=App::open('article',['id'=>$art,'appFrom'=>'telex']);
elseif(!$usr && !$own && !$id)$stream=App::open('article',['id'=>6]);
elseif($desk)$stream=Desk::load('desktop','com',val($p,'dir','/documents'));
elseif($chat)$stream=App::open('chat','');
elseif($usr && $usr!=$own)$stream=self::readusr($p,$usr);
else $stream=self::read($p);
$stream=div($stream,'','tlxbck');
//render
if(get('popup'))$rs=['position:relative','margin-top:10px']; else $rs=['',''];
$ret=div(div($nav,'navigation'),'topbar','',$rs[0]);
$hdash=''; $htime='';
if($bigprofile){$ret.=div($bigprofile,'bigprofile'); $hdash=' hdash'; $htime=' htime';}
else $ret.=div('','hfixer');
$cnt=div($profile.$dsk.$credits.$pub,'dashboard'.$hdash,'',$rs[1]);
$cnt.=div($publish.$refresh.$stream,'timeline'.$htime,'timbck',$rs[1]);
//prmtm
if(val($p,'th'))$pmtm='th='.$usr; elseif($ntf)$pmtm='ntf=1,usr='.$usr; 
elseif(!$id && !$desk && !$chat && !$art){
	if($usr)$pmtm='noab=1,tm='.$usr; else $pmtm='tm='.$own;}
else $pmtm='';
$cnt.=hidden('prmtm',$pmtm);
self::$usr=$usr;
return $ret.div($cnt,'container');}
}
?>