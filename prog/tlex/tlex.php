<?php

class tlex{
static $private=0;
static $width=590;
static $objects=0;
//static $db='tlex';
static $db='tlex';
static $title='Tlex';
static $description='';
static $image='';
static $usr='';
static $opn=0;
static $id=0;

//install
static function install(){
sqlcreate(self::$db,['uid'=>'int','txt'=>'var','lbl'=>'int','lg'=>'tiny','ib'=>'int','ko'=>'int']);
sqlcreate('tlex_ab',['usr'=>'var','ab'=>'var','list'=>'var','wait'=>'int','block'=>'int']);
sqlcreate('tlex_web',['url'=>'var','tit'=>'var','txt'=>'var','img'=>'var']);
sqlcreate('tlex_lik',['luid'=>'int','lik'=>'int']);
sqlcreate('tlex_rpt',['rpuid'=>'int','tlxid'=>'int']);
sqlcreate('tlex_ntf',['4usr'=>'var','byusr'=>'var','typntf'=>'int','txid'=>'var','state'=>'int']);
sqlcreate('tlex_to',['toid'=>'int','to'=>'var']);
sqlcreate('tlex_tag',['tgid'=>'int','tag'=>'var']);}

static function admin($p){
$r[]=['Tlex','pop','core,help|ref=tlex,conn=1','tlex','welcome'];
$r[]=['Tlex','pop','core,help|ref=features,conn=1','art','features'];
return $r;}

/*static function titles($p){
$d=val($p,'appMethod'); if(!$d or $d==1)return;
$r['read']='tlex';
$r['editor']='publish';
$r['objplayer']='object player';
if(isset($r[$d]))return $r[$d];}*/

#headers		
static function injectJs(){
if(ses('uid'))return '
var activelive=1; var nbnew=0; var reloadtime=10000;
setTimeout("tlexlive(0)",1000);';}
//setTimeout("tlexlive(1)",3600000);
//setTimeout("refresh()",500);

static function headers(){
add_prop('og:title',addslashes(self::$title));
add_prop('og:description',self::$description);
add_prop('og:image',self::$image);
add_head('csslink','/css/tlex.css');
add_head('jslink','/js/tlex.js');
add_head('jscode',self::injectJs());}

#ajax
//typntf:1=quote,2=reply,3=like,4=subsc,5=chat,6=subsc-approve
static function refresh($p){$own=ses('user'); $p['count']=1;
$recents=self::apisql($p); $w='where 4usr="'.$own.'" and state=1';
$notifs=sql('count(id)','tlex_ntf','v',$w.' and typntf in (1,2,3)');//quote,reply,like
$subscr=sql('count(id)','tlex_ntf','v',$w.' and typntf in (4,6)');//subscr
$sbcnb=sql('count(id)','tlex_ab','v','where ab="'.$own.'"');//subscr_nb
$chat=sql('count(id)','tlex_ntf','v',$w.' and typntf=5');//chat
return $recents.'-'.$notifs.'-'.$subscr.'-'.$sbcnb.'-'.$chat;}

#saves
static function savemetas($d){
$r=self::playmetas($d); $f=''; $txt='';
if(!$r){$r=og::metas($d);
	if($r[1])$txt=(str_replace(array('“','”'),'',$r[1]));//html_entity_decode
	if($r[2])$f=files::saveimg($r[2],'web','590','400','tlex'); else $f='';
	if($r[0])sqlsav('tlex_web',[$d,$r[0],$txt,$f]);}
return $r;}

//build connectors
static function build_conn($d,$o=''){$ret='';
$d=clean_n($d);
$d=str_replace("\n",' (nl) ',$d);
$r=explode(' ',$d);
foreach($r as $v){
	if(substr($v,0,1)=='@'){$v=substr($v,1); $ret[]='['.$v.':@]'; $_POST['ntf'][$v]=1;}
	elseif(substr($v,0,1)=='#'){$ret[]='['.substr($v,1).':#]'; $_POST['tag'][substr($v,1)]=1;}
	elseif(is_img($v)){
		$f=files::saveimg($v,'tlx','590','400','tlex');
		$ret[]='['.($f?$f:$v).']';}//:img
	elseif(substr($v,0,4)=='http'){
		$v=before($v,'?utm',1);
		$xt=ext($v);
		if($xt=='.mp3')return '['.$v.']';//:audio
		elseif($xt=='.mp4')return '['.$v.']';//:mp4
		elseif($xt=='.pdf')return '['.$v.']';//:pdf
		else $metas=self::savemetas($v);
		if(video::provider($v))$ret[]='['.$v.':video]';
		elseif($metas)$ret[]='['.$v.':web]';
		else $ret[]='['.$v.'';}//:url]
	else $ret[]=$v;
	$conn=substr($v,0,1)=='['?1:0;
	if($conn && substr($v,-4)==':id]' && $id=substr($v,1,-4)){
		$usr=sqljoin('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$id);
		if($usr)$_POST['ntf-r'][$usr]=1;}//notify
	elseif($n=strrpos($v,':')){$cnn=substr($v,$n+1,-1); 
		if($cnn)$_POST['lbl']=$cnn=='art'?'article':$cnn;}}
if($ret)$d=implode(' ',$ret);
$d=str_replace(' (nl) ',"\n",$d);
//$d=str_replace(':img]'."\n",':img]',$d);
return trim($d);}

static function save($p){$txt=val($p,$p['ids']); $_POST['ntf']='';
if($oAuth=val($p,'oAuth')){$ok=sql('puid','profile','v','where oAuth="'.$oAuth.'"');
	if($ok)sez('uid',$ok); else return 'error';}
if($txt){$txt=self::build_conn($txt,1); $ib=val($p,'ibs',0);
	$lbl=val($p,'lbl',0); if(!$lbl && $lbl=post('lbl'))$_POST['lbl']='';
	if($lbl && !is_numeric($lbl))$lbl=sql('id','labels','v','where ref="'.$lbl.'"');
	$lg=yandex::detect(['txt'=>$txt]); if(!$lg)$lg=ses('lng');
	$id=sqlsav(self::$db,[ses('uid'),$txt,(int)$lbl,$lg,$ib,0]);
	if(isset($_POST['ntf']))self::saventf($id,1,'ntf');
	if(isset($_POST['tag']))self::saventf($id,0,'tag');
	if(isset($_POST['ntf-r']))self::saventf($id,2,'ntf-r');}
if(val($p,'apicom'))return isset($id)?$id:'error';
return self::read($p);}

static function modif($p){$txt=val($p,$p['ids']); $id=val($p,'id');
$txt=self::build_conn($txt);
if($id && $txt)sqlup(self::$db,'txt',$txt,$id);
return self::one($p);}

#editor	
static function realib($id){
$d=sql('txt',self::$db,'v',$id);
if(strpos($d,':id]'))$id=segment($d,'[',':id]');
return $id;}

static function publishbt($t,$v,$rid){
$ja='div,tlxbck,resetform|tlex,save|ids=tx'.$rid.'|tx'.$rid.'';
$prm['onclick']='closediv(\''.$rid.'up\'); closediv(\'lbcbk\'); closediv(\'dboard\'); cltg(); resizearea(\''.$rid.'\');';
$prm['id']='edtbt'.$rid; $prm['data-prmtm']='tm='.ses('user');
return aj($ja,langph('publish'),'btsav',$prm).hidden('tx'.$rid,'['.$v.']');}

//publish
static function editor($p){
$ib=val($p,'ib'); $idv=val($p,'idv'); $rid=val($p,'rid',randid('ids')); $to=val($p,'to');
$ret=''; $appsbt=''; $bts='';
if($qo=val($p,'quote'))$msg='['.self::realib($qo).':id]';
//elseif($to)$msg='@'.$to.' ';
elseif($id=val($p,'id'))$msg=sql('txt',self::$db,'v',$id);
else $msg=val($p,'msg');
if($ib)$ret.=div(lang('in-reply to').' '.$to,'grey');
if($qo)$ret.=div(lang('repost'),'grey');
$count=span('','btno','strcnt'.$rid,'display:none;').' ';//save
$ja='div,tlxbck,resetform|tlex,save|ibs='.$ib.',ids='.$rid.'|'.$rid.',lbl';
$prm['onclick']='closediv(\''.$rid.'up\'); closediv(\'lbcbk\'); closediv(\'dboard\'); cltg(); resizearea(\''.$rid.'\');';
$prm['id']='edtbt'.$rid; $prm['data-prmtm']='tm='.ses('user');//'current';
if(!$qo){$bts=upload::call($rid,1);
	$bts.=bubble('ascii,call|rid='.$rid,ico('smile-o'),'btn',1);}//'&#128522;'
$ret.=div($count.$bts.aj($ja,langph('publish'),'btsav',$prm),'right').' ';
$js='strcount1(\''.$rid.'\',768); resizearea(\''.$rid.'\');';//form
$r=['class'=>'area','id'=>$rid,'onkeyup'=>$js,'onmousedown'=>$js,'placeholder'=>lang('message')];
$ret.=div(tag('textarea',$r,$msg));
if(!$qo){$divid='edcnt'; $clpm=['data-prmtm'=>'no'];//stop continue scrolling
	$tg=span(span('','','lblxt'),'','lbcbk').hidden('lbl',0);
	$ret.=div($tg,'edtbt'.$rid);//embed used to close others
	$ret.=div('','',$rid.'up','');}//uploads
else{$divid='';}
$ret.=div('','clear');
return div($ret,'tlxapps',$divid);}

static function redit($p){$id=val($p,'id'); $rid=randid('ids');
$msg=sql('txt',self::$db,'v',$id);
$js='strcount1(\''.$rid.'\',768); resizearea(\''.$rid.'\');';
$r=['class'=>'area','id'=>$rid,'onkeyup'=>$js,'onmousedown'=>$js,'cols'=>44];
$ret=tag('textarea',$r,$msg);//form
$count=span(768-mb_strlen(html_entity_decode($msg)),'btxt small','strcnt'.$rid).' ';
$ja='tlx'.$id.',,x|tlex,modif|id='.$id.',ids='.$rid.'|'.$rid;
$ret.=span($count.aj($ja,langp('modif'),'btsav'),'right').' ';
$ret.=div('','clear');
return $ret;}

#players
static function playmetas($d){
return sql('tit,txt,img','tlex_web','rw','where url="'.$d.'"');}

static function playlink($d){
$d=http($d); $r=self::playmetas($d); $t=domain($d);
$r=array('href'=>$d,'title'=>$r[1],'class'=>'btlk','target'=>'_blank');
return tag('a',$r,$t);}

static function playweb($d,$o=''){
$d=http($d); $r=self::playmetas($d); //$r=self::savemetas($d);
if($o){$p=video::provider($d); if($p)$id=video::extractid($d,$p);}
$dom=domain($d);
$t=$r[0]?$r[0]:$dom;
$lk=lk($d,$t,'btlk',1);
if(!$r)return $lk;
if(substr($r[2],0,4)=='http'){$f=$r[2];
	if($r[2])$imx=@getimagesize($r[2]); else $imx[0]='x';
	if($imx[0]>590)$img=img($r[2],'590'); else $img='';}
elseif($r[2])$f=self::thumb($r[2],'mini'); else $f='';
if($imx=@getimagesize($f)){
	if($imx[0]>590)$img=img('/'.self::thumb($r[2],'medium'));//img('/'.$f,'590');
	elseif($imx[0])$img=img('/'.self::thumb($r[2],'mini'),100,100,'artim');} else $img='';
if($dom=='1nfo.net' or $dom=='newsnet.fr')$apc='playphilum'; else $apc='playnet';
$j='tlex,objplayer|popwidth=580,obj='.$apc.',p1='.nohttp($d);//
if($img && isset($id))$ban=pagup('video,call|p='.$p.',id='.$id,$img,'');
elseif($img)$ban=imgup('img/full/'.$r[2],$img,''); else $ban=$img;
if($dom=='1nfo.net' or $dom=='newsnet.fr')$bt=' '.pagup($j,langp('read'),'grey').' ';
else $bt=' '.pagup($j,langp('read'),'grey').' ';
$url=lk($d,pic('url').$dom,'grey');
$ret=div($t,'bold').div($r[1],'stxt').div($url.$bt).div('','clear');
$ret=div($ret,'pncxt');
return div($ban.$ret,'panec');}

static function playphilum($u){
$id=after($u,'/');
$f='http://newsnet.fr/api/id:'.$id.',preview:3';
$d=files::get($f);
$r=json_decode($d,true);
$ret=tag('h1','',$r[$id]['title']);
$ret.=div(html_entity_decode($r[$id]['content']),'txt');
$ret.=lk(http($u));
return $ret;}

static function playnet($u){
$ret=mercury::read(['url'=>http($u)]);
return div($ret,'pane');}
	
static function playquote($id){
$r=self::apisql(['id'=>$id]);
if(!$r)return div(lang('telex_deleted'),'paneb');
$v=$r[0]; $v['idv']='qlx'.$id;
$ret=self::panehead($v,'popup');
$ret=tag('header',['class'=>''],$ret);
$ret.=conn::read(['msg'=>$v['txt'],'app'=>'tlex','mth'=>'reader','opt'=>'it2','ptag'=>'no']);
return div($ret,'paneb',$v['idv']);}

static function thumb($f,$dim){$dr='img/';
$fb='medium/'.$f; $med=is_file($dr.$fb);
if($dim=='mini' or $dim=='micro')$im='mini/'.$f;
elseif($dim=='medium')$im=$med?$fb:'full/'.$f; else $im='full/'.$f;
if(is_file($dr.$im) && filesize($dr.$im))return $dr.$im;}

static function playthumb($f,$dim,$o='',$c=''){
$sz=590; if($dim=='micro')$sz=64;
if(substr($f,0,4)=='http')$f=files::saveimg($f,'tlx',$sz,'','tlex');
$fb=self::thumb($f,$dim); $u=img('/'.$fb,$sz);
if(!$fb)return; if($o)return $u;
if($f)return imgup('img/full/'.$f,$u,$c);}

static function url($p,$o,$e=''){$t=$o?$o:domain($p);
//$pop=popup('tlex,objplayer|obj=playweb,p='.$p.',o='.$o,$t,'btlk');
return lk($p,$t.' '.ico('external-link'),'',$e);}

static function objplayer($p){$func=$p['obj'];
return self::$func(val($p,'p1'),val($p,'p2'));}

//connectors
static function reader($d,$b=''){
list($p,$o,$c)=readconn($d); $xt='';
$s=strrpos($p,'.');
if($s){$xt=substr($p,$s+1);// && !$c
	if($xt=='jpg' or $xt=='png' or $xt=='gif')$c='img';
	elseif($xt=='mp3')$c='audio'; elseif($xt=='mp4')$c='video';
	elseif($xt=='pdf')$c='pdf';}
elseif(substr($p,0,4)=='http')$c='web';
if(conn::$one!=1 or self::$opn)$opn=1; else $opn=0;
if($c)switch($c){
	case('@'):return bubble('tlex,profile|usr='.$p,'@'.$p,'btlk',1); break;
	case('#'):return aj('pagup|tlex,search_txt|srch='.$p,'#'.$p,'btlk'); break;
	case('b'):return tag('strong',['class'=>$o],$p); break;
	case('i'):return tag('em',['class'=>$o],$p); break;
	case('q'):return tag('blockquote',['class'=>$o],$p); break;
	case('red'):return tag('red',['class'=>$o],$p); break;
	case('clr'):return span($p,'','','background-color:#'.$o.'; color:#'.clrneg($o,1).';'); break;
	case('list'):return conn::mklist($p); break;
	case('id'):if(is_numeric($p)){$_POST['repost']=$p; return self::playquote($p);} break;
	case('url'):return self::url($p,$o,''); break;
	//case('link'):return self::playlink($p); break;
	case('img'):if($opn==1)$ret=self::playthumb($p,'full'); else $ret='';
		self::$objects[$c][]=[$p,$o]; conn::$one=1; return $ret; break;
	case('web'):if($opn==1)$ret=self::playweb($p); else $ret=self::playlink($p);
		self::$objects[$c][]=[$p,$o]; conn::$one=1; return $ret; break;
	case('pdf'):return pagup('iframe,get|url='.nohttp($p),ico('file-pdf-o',24).domain($p),'btxt'); break;
	case('video'):if($opn==1)$ret=$xt=='mp4'?video($p):self::playweb($p,1); else $ret='';
		self::$objects[$c][]=[$p,$o]; conn::$one=1; return $ret; break;
	case('audio'):return audio($p); break;
	case('mp4'):return video($p); break;
	case('philum'):if($opn==1)$ret=self::playphilum($p); else $ret='';
		self::$objects[$c][]=[$p,$o]; conn::$one=1; return $ret; break;
	case('artit'):art::boot(); $tit=art::tit(['id'=>$p]); conn::$one=1;
		return lk('/art/'.$p,$tit,'btlk').' '; break;
	case('gps'):self::$objects[$c][]=[$p,$o]; $t=gps::com(['coords'=>$p]);
		return pagup('map,call|coords='.$p,ico('map-marker',24).span($t)); break;
	case('app'):self::$objects[$c][]=[$p,$o]; conn::$one=1;
		return aj('popup|'.$p.'|id='.$o,pic($p,24),'btn'); break;
	case('open'):if(method_exists($p,$o))return $p::$o; break;
	case('picto'):return picto($p,$o?$o:24); break;
	case('ico'):return ico($p,$o?$o:24); break;
	case('ascii'):return '&#'.$p.';'; break;
	case('db'):self::$objects[$c][]=[$p,$o]; return db::call(['f'=>'usr/'.$p]); break;
	case('no'):return; break;
	default: if(method_exists($c,'call'))return self::display_app($c,$p,$o); break;}
//if(is_img($d))return img($d,'','',$o);
//if($p=='http' or $p=='https')return self::url($p,$o,'');
return '['.$d.']';}

static function display_app($c,$p,$o){tlex::$objects[$c][]=[$p,$o];
	if(conn::$one!=1 && $c=='art' && !self::$opn)$ret=art::preview(['id'=>$p]);
	//else $ret=app($c,['appMethod'=>'call','conn'=>'no','id'=>$p]);
	else{$q=new $c; $t=$o?$o:$q::tit(['id'=>$p]); $ret='';
		$bt=span(hlpic($c,28),'apptyp').' '.span($t,'apptit');
		$op=isset($q::$open)?$q::$open:0;
		if((conn::$one!=1 && $op) or self::$opn){
			if($op==2)$ret=ifrapp($c,$p);
			elseif($op==3)$ret=lkb(host().'/frame/'.$c.'/'.$p,$bt,'app');
			else $ret=app($c,['appMethod'=>'call','conn'=>'no','id'=>$p]);}
		//else $ret=div(popup($c.',call|headers=1,id='.$p,$bt),'app');}
		//else $ret=div(aj('opn'.self::$id.'|'.$c.',call|headers=1,id='.$p,$bt),'app');
		else $ret=div(toggle('opn'.self::$id.'|'.$c.',call|headers=1,id='.$p,$bt),'app');}
	conn::$one=1; return $ret;}

//objects
static function objects(){$ret=''; $r=self::$objects; $sz='24'; $css='licon';//36 from css
if($r)foreach($r as $kr=>$vr){$imok=0;
foreach($vr as $k=>$v){$fc=''; $ic=''; list($p,$o)=$v;
	if($kr=='img'){if($imok)$ret.=self::playthumb($p,'micro'); $imok=1;}
	if($ic)$t=ico($ic,$sz).span($t);//when called method need an interface
	if($fc)$ret.=pagup('tlex,objplayer|obj='.$fc.',p1='.$p.',p2='.$o,$t,$css);}}
if($ret)return div($ret,'');}//panec

#search	
static function search_txt($p){
$srch=val($p,'srch'); $ret='';
$r=self::apisql(['srh'=>$srch]);
if($r)foreach($r as $k=>$v)$ret.=div(self::pane($v,1),'pane','tlx'.$v['id']);
else $ret=help('no results','board');
return $ret;}

static function searchbt(){
$r=['type'=>'text','id'=>'srch','placeholder'=>lang('search'),'onkeypress'=>'SearchT(\'srch\')'];
return tag('form',['id'=>'srchfrm','name'=>'srchfrm','action'=>'javascript:Search(\'srch\');'],tag('input',$r,'',1)).div('','','cbksrch');}

#like
static function savelike($p){$id=val($p,'id'); $lid=val($p,'lid'); $nlik=val($p,'nlik');
if($lid){sqldel('tlex_lik',$lid); $p['lid']='';
$r=['4usr'=>$p['name'],'byusr'=>ses('user'),'typntf'=>3,'txid'=>$id];
$ex=sql('id','tlex_ntf','v',$r); if($ex)sqldel('tlex_ntf',$ex);}
else{$p['lid']=sqlsav('tlex_lik',[ses('uid'),$id]); tlxcall::saventf1($p['name'],$id,3);}
return self::likebt($p);}
	
static function likebt($p){$rid=randid('lik'); $mylik=''; $sty='';
$id=val($p,'id'); $lid=val($p,'lid'); $n=''; $nlik='';
if($lid){
	$nlik=sql('count(id)','tlex_lik','v','where lik='.$id);
	$mylik=sql('id','tlex_lik','v','where lik='.$id.' and luid='.ses('uid'));
	if($mylik)$sty.='color:#e81c4f;';}
$bt=ico('heart',$sty,'like','','',span($nlik,'liknb'));
$ret=aj($rid.'|tlex,savelike|id='.$id.',lid='.$mylik.',name='.$p['name'],$bt);
return span($ret,'',$rid);}

#follow
static function followbt($p){$rid=val($p,'rid',randid('flw'));
$usr=val($p,'usr'); $sm=val($p,'small'); //$wait=val($p,'wait');//vcu
$w='where usr="'.ses('user').'" and ab="'.$usr.'"';
$id=sql('id','tlex_ab','v',$w);
$rb=sql('wait,block','tlex_ab','ra',$w);//contexts:user see visitor (ucv),
if($id){
	if($rb['wait'])$flag='pending'; elseif($rb['block'])$flag='blocked'; else $flag='unfollow';
	$bt=$sm?pic($flag):langph($flag);
	$ret=bubble('tlxcall,follow|chan=1,usr='.$usr.',rid='.$rid,pic('menu'),'btn',1);
	$ret.=aj($rid.'|tlxcall,follow|usr='.$usr.',unfollow='.$id.',rid='.$rid,$bt,'btdel');}
else{$bt=$sm?pic('follow'):langph('follow');
	$ret=bubble('tlxcall,follow|chan=1,usr='.$usr.',rid='.$rid,$bt,'btsav',1);}
$c=val($p,'rid')?'':'followbt';
return span($ret,$c,$rid);}

#subscriptions
static function subscribt($usr,$uid,$role){
if(!$uid)$uid=sql('id','login','v','where name="'.$usr.'"');
$n0=sql('count(id)',self::$db,'v','where uid="'.$uid.'"');
$n1=sql('count(id)','tlex_ab','v','where usr="'.$usr.'"');
$n2=sql('count(id)','tlex_ab','v','where ab="'.$usr.'"');
$bt=div(lang('published telex'),'subscrxt').div($n0,'subscrnb clr');
$ret=div(self::loadtm('tm='.$usr.',noab=1',$bt,''),'subscrbt');
$bt=div(lang('subscriptions'),'subscrxt').div(span($n1,'','tlxabs'),'subscrnb clr');//ab
$ret.=div(aj('tlxbck|tlxcall,subscrptn|type=ption,usr='.$usr.'|tlxabs',$bt),'subscrbt');
$t=$role?'members':'subscribers';
$bt=div(lang($t),'subscrxt').div(span($n2,'','tlxsub'),'subscrnb clr');//sub
$ret.=div(aj('tlxbck|tlxcall,subscrptn|type=ber,usr='.$usr.'|tlxsub',$bt),'subscrbt');
return div($ret,'subscrstats').div('','clear');}

#profile
static function profile($p){$ret='';
$usr=val($p,'usr'); $uid=val($p,'uid'); $sm=val($p,'small');
if($usr){$rp=profile::build($p);
	$usn=div($rp['username'].$rp['status']);
	if(ses('user')!=$usr)$subsc=div($rp['follow'],'right'); else $subsc='';
	$ret=div($rp['banner'].$subsc.$rp['avatar'].$usn,'','prfl');}
if($ret)return div($ret,'profile');}

static function profilesmall($p){
if(val($p,'usr')){$rp=profile::build($p);
	return div($rp['username'].$rp['status']);}}

#visited usermenu
static function usermenu($p){
$usr=val($p,'usr',ses('user')); $rid=val($p,'rid'); $uid=val($p,'usid'); $c=''; $ret='';
if(!$uid)$uid=sql('id','login','v','where name="'.$usr.'"');
$n0=sql('count(id)',self::$db,'v','where uid="'.$uid.'"');
$n1=sql('count(id)','tlex_ab','v','where usr="'.$usr.'"');
$n2=sql('count(id)','tlex_ab','v','where ab="'.$usr.'"');
$clpm=['data-prmtm'=>'no'];//stop continuous scrolling!
//$ret=lk('/',ico('star'),'btxt');
//$ret.=lk('/u/'.$usr,ico('user'),'btxt');
$bt=langph('stream').' '.span('','nbntf','tlxrec');//stream
$ret.=self::loadtm('tm='.$usr,$bt,$c);//'current'
$bt=langph('published telex').' '.span($n0,'nbntf');//published
$ret.=self::loadtm('tm='.$usr.',noab=1',$bt,$c);
//if(auth(6))$ret.=aj('tlxbck|desktop|dir=/documents',langp('desktop'),$c,$clpm);//desk
$bt=langph('notifications').' '.span('','nbntf','tlxntf');//notifs
$ret.=self::loadtm('ntf=1',$bt,$c);
$bt=langph('subscriptions').' '.span($n1,'nbntf','tlxsub');//subsc
$ret.=aj('tlxbck|tlxcall,subscrptn|type=ption,usr='.$usr,$bt,$c,$clpm);
$role=sql('role','profile','v','where pusr="'.$usr.'"');
$bt=langph($role?'members':'subscribers').' '.span($n2,'nbntf','tlxabs');//ab
$ret.=aj('tlxbck|tlxcall,subscrptn|type=ber,usr='.$usr.'|tlxsub',$bt,$c,$clpm);
$ret.=aj('tlxbck|profile,edit',langpi('user'),$c,$clpm);
$ret.=aj('tlxbck|tlex,pub|usr='.$usr,langpi('other accounts'),$c);//pub
//$ret.=aj('tlxbck,,1|art,com|add=1,rid='.$rid,langpi('art'),'',$clpm);
//$ret.=aj('tlxbck,,1|tabler,com|add=1,rid='.$rid,langpi('tabler'),'',$clpm);
//$ret.=aj('tlxbck|explorer|b=usr',langpi('datas'),$c,$clpm);//disk
$ret.=hidden('tlxabsnb',$n2);//.hidden('tlxsubnb',$n1)
return div($ret,'lisb');}

#dashboard
static function dashboard($p){
$usr=val($p,'usr',ses('user')); $rid=val($p,'rid'); $uid=val($p,'usid'); $c=''; $ret='';
if(!$uid)$uid=sql('id','login','v','where name="'.$usr.'"');
$clpm=['data-prmtm'=>'no'];//stop continuous scrolling!
$ret.=toggle('dboard|tlex,editor|rid='.$p['rid'],langph('publish'),$c);//publish
$ret.=toggle('dboard|tlxcall,menuapps|rid='.$p['rid'],langph('apps'),$c);//apps
$ret.=toggle('dboard|tlxcall,dskdir|usid='.$uid,langph('documents'),$c);//docs

$ret.=toggle('dboard|tlex,lablbt',langph('labels'),$c);//label
$ret.=toggle('dboard|tlex,chanbt|usr='.$usr.',list='.ses('list'),langph('lists'),$c);//lists
$bt=langp('messages').span('','nbntf','tlxmsg');//chat
$ret.=toggle('dboard|chat,calltlx|headers=1',langph('messages'),$c);
$ret.=toggle('dboard|tlex,searchbt',langph('search'),$c);//search
return div($ret,'lisb');}

#notifications
static function saventf($id,$type,$o){
$r=isset($_POST[$o])?$_POST[$o]:'';
if($r)foreach($r as $k=>$v)if($k!=ses('user')){
	if($type)tlxcall::saventf1($k,$id,$type);
	if($o=='ntf')sqlsavup('tlex_to',[$id,$k]);
	if($o=='tag')sqlsavup('tlex_tag',[$id,$k]);}
$_POST[$o]='';}

static function readntf($v){$n=$v['typntf']; $by='@'.$v['byusr']; $ret='';
//$uname=sql('name','login','v','where usr="'.$v['byusr'].'"');
if($v['state']==1)sqlup('tlex_ntf','state','0',$v['ntid']);
if($n==1 && $v['ib'])$ret=$by.' '.lang('has_reply',1); 
elseif($n==1)$ret=$by.' '.lang('has_sent',1);
elseif($n==2)$ret=$by.' '.lang('has_repost',1);
elseif($n==3)$ret=$by.' '.lang('has_liked',1);
return div($ret,'ntftit');}

#channels
static function chanread($usr){
return sql('distinct(list)','tlex_ab','rv','where usr="'.$usr.'" and wait=0 and block=0');}
static function chanbt(){$ret=self::loadtm('tm='.ses('user'),lang('all'));
//$r=sesclass('tlex','chanread',ses('user'));//todo reactive after subscr
$r=self::chanread(ses('user'));
if($r)foreach($r as $v)$ret.=self::loadtm('tm='.ses('user').',list='.$v,$v);
return div($ret,'lisb');}

#labels
static function lablbt(){$ret='';//self::loadtm('labl=',lang('all'));
$r=sql('ref,labels.id,icon','labels','kvv','inner join '.self::$db.' on lbl=labels.id
inner join profile on puid=uid where privacy=0 or (puid="'.ses('uid').'" and privacy=1)');
if($r)foreach($r as $k=>$v)$ret.=self::loadtm('labl='.$v[0],ico($v[1]).lang($k));
return div($ret,'lisb');}

#pub
static function pub($p){$usr=val($p,'usr');
$mail=sql('mail','login','v','where name="'.$usr.'"');
$r=sqljoin('name','profile','login','puid','rv','where mail="'.$mail.'" and name!="'.$usr.'" and auth>1 and privacy=0');
if($r){foreach($r as $v)$ret[]=profile::small(['usr'=>$v]); return implode('',$ret);}}

#read
static function relativetime($sec){$time=ses('time')-$sec;
$ret=lang('there_was').' ';
if($time>864000)$ret=strftime('%a %d %b',$sec);
if($time>86400)$ret=strftime('%d %b',$sec);
elseif($time>3600)$ret.=floor($time/3600).'h ';
elseif($time>60)$ret.=floor($time/60).'min ';
else $ret.=$time.'s';
return span(utf8_encode($ret),'small');}

//thread
static function thread_parents($id,$ret=''){
$ib=sql('ib',self::$db,'v','where '.self::$db.'.id="'.$id.'"',0);
if($ib){$ret[$ib]=1; $ret=self::thread_parents($ib,$ret);}
return $ret;}
static function thread_childs($id){
return sql('id',self::$db,'k','where ib='.$id,0);}
static function sql_thread($id){
$ids=self::thread_childs($id);
$ids=self::thread_parents($id,$ids);
$ids[$id]=1; ksort($ids);
if($ids)$r=array_keys($ids);
if(isset($r))return 'where ('.self::$db.'.id='.implode(' or '.self::$db.'.id=',$r).')';}

//pane
static function panehead($v){$id=$v['id']; $idv=$v['idv']; $usr=$v['name']; $label='';
$ret=bubble('tlex,profile|usr='.$usr.',small=1',profile::avatarsmall($v),'btxt',1).' ';
$usrnm=lk('/u/'.$usr,$v['pname'],'btxt" title="@'.$usr);
//$time=self::relativetime($v['now']);
$time=span(date('d-m-Y',$v['now']),'small');
$url=lk('/'.$id,$time.ico('link'),'grey');//.ico('external-link',12)
$ret.=$usrnm.' '.$url.' ';
$ico=$v['icon']?ico($v['icon']):'';
if($v['ref'])$ret.=span(span($ico.lang($v['ref']),'tx'),'label').' ';
$ret.=self::likebt($v).' ';
//$date=pagup('tlex,read|popwidth=600,th='.$id,$time,'grey');
if($v['ib']){
	$to=sqljoin('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$v['ib']);
	$ret.=pagup('tlex,read|popwidth=600,th='.$id,lang('in-reply to',1).' '.$to,'grey small').' ';}
if($nb=sql('count(id)',self::$db,'v','where ib='.$id))
	$ret.=pagup('tlex,read|popwidth=600,th='.$id,$nb.' '.lang($nb>1?'replies':'reply',1),'grey small');
//$label=; else
if(ses('uid'))$ret.=span(self::panefoot($v),'actions');//
return $ret;}

static function panefoot($v){
$id=$v['id']; $idv=$v['idv']; $pr='pn'.$idv; $usr=$v['name']; $lg=$v['lg']; $ret=''; $sz='';
$bt=icit('reply','to reply','',$sz);
$ret.=toggle($pr.'|tlex,editor|idv='.$idv.',to='.$v['name'].',ib='.$id,$bt);
$ret.=toggle($pr.'|tlex,editor|idv='.$idv.',quote='.$id,icit('retweet','quote','',$sz));
$ret.=toggle($pr.'|tlxcall,share|id='.$id,icit('share-alt','share','',$sz));
$bt=icit('download','keep','',$sz);
if(self::$objects)$ret.=toggle($pr.'|tlxcall,keep|idv='.$idv.',id='.$id,$bt);
$bt=icit('ellipsis-h','actions','',$sz);
$ret.=toggle($pr.'|tlxcall,actions|id='.$id.',idv='.$idv.',uid='.$v['uid'].',usr='.$usr,$bt);
$bt=langp('originally in').' '.lang($lg,1);
if($lg && $lg!=ses('lng'))$ret.=toggle($pr.'|tlxcall,translate|id='.$id.',lg='.$lg,$bt,'grey small');
//if(post($a))$ret.=popup($a.'|edit=1,id='.$p['id'],langpi('edit'));
return $ret;}

static function pane($v,$current=''){$head='';
$id=$v['id']; $usr=$v['name']; $v['idv']='tlx'.$id; self::$id=$id;
$head.=div(self::panehead($v),'bloc_header');
$head.=div('','bloc_redit','pn'.$v['idv']);
self::$objects=''; $_POST['repost']=0;
if($v['ko']){$msg=div(help('telex_banned'),'alert'); self::$objects='';}
else{$msg=conn::read(['msg'=>$v['txt'],'app'=>'tlex','mth'=>'reader','ptag'=>1,'opt'=>'']);
$msg.=div(self::objects(),'objects');}
$msg=div($msg,'message');
$msg.=div('','','opn'.$id);
/*if($id=$_POST['repost']){$by=bubble('tlex,profile|usr='.$usr,'@'.$usr,'',1);
	$head.=div($by.' '.lang('has_repost',1),'grey');}*/
//if(ses('uid'))$foot=div(self::panefoot($v),'actions'); else $foot='';
$ret=$head.div($msg,'bloc_content');//div($avatar,'bloc_left').//.$foot
if(isset($v['typntf']))$ret=self::readntf($v).$ret;
if($current==$id && !self::$description){self::$title=host().'/'.$id.' by @'.$usr;
	self::$description=preview($msg);
	self::$image=host().'/img/mini/'.$v['avatar'];}
return $ret;}

static function readusr($p,$usr){//authorized to watch
$open=sql('auth','login','v','where name="'.$usr.'"');
if(!$open)return div(ico('lock').hlpxt('closed account'),'pane');
$prv=sql('privacy','profile','v','where pusr="'.$usr.'"');//
if($prv){$id=sql('id','tlex_ab','v','where usr="'.ses('user').'" and ab="'.$usr.'" and wait=0 and block=0');
	if(!$id)return div(ico('lock').hlpxt('private account'),'pane');
	else return self::read(['tm'=>$usr,'noab'=>1]);}
else return self::read(['tm'=>$usr,'noab'=>1]);}

//read
static function read($p){$ret=''; $id='';//$id will be in popup
$last=val($p,'from'); $th=val($p,'th'); $id=val($p,'id');
$usr=val($p,'usr'); $own=val($p,'own');
$rs=val($p,'rs'); $ib=val($p,'ib'); 
if($th && !$last)$id=$p['th']=$th;//thread
elseif($id && !$last)$p['id']=$id;//one
elseif($rs && !$last)$id=$p['rs']=$rs;//current+childs
elseif($ib && !$last)$p['ib']=$ib;//childs
elseif($usr && $usr==$own){$p['tm']=$usr; $p['noab']=1;}//noab
else{$tm=val($p,'tm');//timeline
	$usr=$usr?$usr:($tm?$tm:($own?$own:ses('user')));
	$p['from']=$last; $p['tm']=$tm?$tm:$usr;}
if(isset($p))$r=self::apisql($p);
if(isset($r))foreach($r as $k=>$v){
	$ret.=div(self::pane($v,$id),'pane','tlx'.$v['id'],'');}
if(!$ret && $usr && !$last)$ret=tlxcall::zero_activity($usr);
return $ret;}

#sql
static function sql_timeline($p){$sq='';
$p=valk($p,['tm','from','list','noab','since','labl','count']);
list($usr,$from,$list,$noab,$since,$labl,$count)=$p;
if(!$noab && !$labl){$sqa=$list?' and list="'.$list.'"':'';
	$r=sql('ab','tlex_ab','rv','where (usr="'.$usr.'" and wait=0 and block=0'.$sqa.')',0);
	if($r)$sq=' or name in ("'.implode('","',$r).'")';}
if($labl)$ret='where labels.id="'.$labl.'"';
elseif($list && !$noab)$ret='where '.substr($sq,4);
elseif($usr==ses('user'))$ret='where (txt like "%@'.$usr.' %" or name="'.$usr.'"'.$sq.')';
else $ret='left join tlex_ab on tlex_ab.usr=login.name
where (txt like "%@'.$usr.' %" or name="'.$usr.'") and (privacy="0" or uid="'.ses('uid').'" or ab="'.ses('user').'")';//'.$sq.'
if($from)$from='and '.self::$db.'.id<'.$from.'';
elseif($since)$from='and '.self::$db.'.id>'.$since.'';
$limit=$count?'':'order by '.self::$db.'.id desc limit 20';
$group='group by '.self::$db.'.id';
//$group='group by '.self::$db.'.id,uid,name,txt,lid,pname,avatar,clr,ref,icon,privacy,ko';//Msql5.7.5
return $ret.' '.$from.' '.$group.' '.$limit;}//and no!=1 

static function apisql($p,$z=''){
$p=vals($p,['tm','th','id','ib','srh','ntf','from','list','noab','since','labl','count']);
if($p['from']=='wrp')return;
if($p['count']){$cols='count('.self::$db.'.id)'; $vmode='v';}
else{$cols=self::$db.'.id,uid,name,txt,lg,unix_timestamp('.self::$db.'.up) as now,ib,tlex_lik.id as lid,pname,avatar,clr,ref,icon,privacy,ko'; $vmode='rr';}
$inn='left join login on login.id=uid 
left join profile on puid=uid 
left join tlex_lik on '.self::$db.'.id=lik 
left join labels on lbl=labels.id ';
if($p['since'])$since=' and '.self::$db.'.id>'.$p['since']; else $since='';
if(!$p['count'])$group=' group by '.self::$db.'.id';
if($p['id'])$w='where '.self::$db.'.id='.$p['id'].$since.$group;//
elseif($p['ib'])$w='where ib='.$p['ib'].$since.$group;
elseif($p['th'])$w=self::sql_thread($p['th']).$since.$group;
elseif($p['srh'])$w='where ((name="'.$p['srh'].'" or txt like "%'.$p['srh'].'%") and (privacy=0 or uid="'.ses('uid').'"))'.$since.$group.' order by id desc limit 20';
elseif($p['ntf']){$cols.=',tlex_ntf.id as ntid,byusr,typntf,state';
	$inn.='inner join tlex_ntf on txid='.self::$db.'.id ';
	$minid=$p['since']?' and '.self::$db.'.id>'.$p['since']:'';
	$limit=$p['count']?'':' order by '.self::$db.'.up desc limit 20';
	$w='where 4usr="'.ses('user').'"'.$minid.$limit;}
else $w=self::sql_timeline($p);
return sql($cols,self::$db,$vmode,$inn.$w,$z);}//z=verbose

static function wrapper($p){
$pub=div(self::editor($p),'pblshcnt','pblshcnt');
return div(self::read($p),'','tlxbck');}

static function one($p){$r=self::apisql($p);
if($r)return self::pane(current($r),$p['id']);}

static function api($p){;return self::call($p);}

//http://tlex.fr/api.php?app=tlex&mth=call&prm=tm:dav
static function call($p){$r=self::apisql($p);
if($r)foreach($r as $k=>$v){self::$objects='';
	$r[$k]['avatar']='http://tlex.fr/img/full/'.$v['avatar'];
	$r[$k]['txt']=conn::read(['msg'=>$v['txt'],'app'=>'tlex','mth'=>'reader']);}
	//$r[$k]['objects']=self::objects();
if($r)return json_r($r);}

//load button
static function loadtm($p,$t,$c='',$tg=''){
if($c)$r['class']=$c; if(!$tg)$tg='wrapper';
$r['onclick']='setTimeout(\'refresh()\',500);'; $r['data-prmtm']=$p; $r['onmousedown']='ajbt(this)';
$r['data-j']='div,'.$tg.',,resetscroll|tlex,wrapper|'.($p=='current'?'':$p);
return tag('a',$r,$t);}
	
static function vrfusr($d){return sql('id','login','v','where name="'.$d.'" and auth>1');}
static function vrfid($d){return sqljoin('name',self::$db,'login','uid','v','where '.self::$db.'.id="'.$d.'"');}

#content
static function content($p){
//self::install(); //profile::install();
//if(ses('dev')=='prog')self::$db='tlex';//alternative table
$badusr=''; $badid=''; $dsk=''; $pub=''; $bigban=''; $publish=''; $hub='';
$own=ses('user'); $desk=val($p,'desk'); $chat=val($p,'chat'); $p['rid']=randid('ids');
$ntf=val($p,'ntf'); $art=val($p,'art'); $usr=val($p,'usr'); $id=val($p,'id',val($p,'th'));
if($usr){$okusr=self::vrfusr($usr); if(!$okusr){$badusr=1; $usr='';} else $p['usid']=$okusr;}//okusr
if(is_numeric($usr)){$id=$usr; $usr='';}//okid
if($id){$okid=self::vrfid($id);
	if(!$okid){$badid=1; $id=''; self::$opn=0;}
	else{$p['id']=$id; $usr=$okid; self::$opn=1;}}
if($art)$usr=sqljoin('name','articles','login','uid','v',['articles.id'=>$art]);//art
if($usr)$p['usr']=$usr; if($own)$p['own']=$own;//defs
if(!$usr && $own)$hub=$usr;//hub
//profile
if($badid or $badusr)$board='';
elseif($usr && !$badusr){list($bigban,$board)=profile::big(['usr'=>$usr]);
	if(auth(1))$board.=self::usermenu($p);}
elseif(!$own or $usr or $id or $desk)$board='';
else $board=div('','profile','prfl').self::usermenu($p).self::dashboard($p);
//div(lk('/u/'.$own,$own,'btit')).
//self::profilesmall(['usr'=>$own,'face'=>'1']).
$board.=div('','pan','dboard');//receptacle of menus
//elseif(!$own && !$usr && !$id)//credits
//stream
if($badid)$stream=help('404iderror','board');
elseif($badusr)$stream=help('404usrerror','board');
elseif($art)$stream=app('art',['id'=>$art,'appFrom'=>'tlex']);
elseif(!$usr && !$own && !$id)$stream=help('tlex','pane',1);
elseif($desk)$stream=desk::load('desktop','defs',val($p,'dir','/documents'));
elseif($chat)$stream=app('chat','');
elseif($id)$stream=self::read($p);
elseif($usr && $usr!==$own)$stream=self::readusr($p,$usr);
else $stream=self::read($p);
//render
/*
$clr=sesr('clr',$usr?$usr:$own);
if(!$clr && $usr)$clr=profile::init_clr(['usr'=>$usr?$usr:$own]);
if(substr($clr,0,1)=='-'){$c=substr($clr,1); $sky=ses('sky'.$c);
	if(!$sky)$sky=ses('sky'.$c,sql('css','sky','v','where tit="'.$c.'"'));
	$bclr='background-image:'.$sky.';';}
else{
	$hex=$clr?hexrgb($clr,0.7):'rgba(119,0,119,0.7)';
	$clr0=clrneg($clr,1); $hex0='rgba(0,0,0,0.15)';//$clr?hexrgb($clr0,0.1):
	$clr1=clrb($clr,-100); $hex1='rgba(0,0,0,0)';//$clr?hexrgb($clr1,0.2):
	$clr2=clrb($clr,100); $hex2='rgba(0,0,0,0)';//$clr?hexrgb($clr2,0.2):
	$bclr='background-color:#'.$clr.'; color:'.$clr0.';';//#'.$clr.'
	$bclr.='background-image:
	linear-gradient(to bottom,rgba(0,0,0,0),'.$hex0.'),
	linear-gradient(to left,rgba(0,0,0,0),'.$hex1.'),
	linear-gradient(to right,rgba(0,0,0,0),'.$hex2.');';}
if(get('popup'))add_head('csscode','.container{'.$bclr.'}');
else add_head('csscode','body{'.$bclr.'}');*/
/*
.list a:hover{background-color:#'.$clr.'; color:#'.$clr0.';}
.list a:hover .pic,.btit,.apptyp{color:#'.$clr0.';}
.pane,.paneb,.panec,.paned{color:#'.$clr0.' background-color:'.$hex0.';}*/
//.lisb a, .lisb a:hover,.lisb .pic{color:#'.$clr0.';}
//prmtm
if(val($p,'th'))$pmtm='th='.$usr; elseif($ntf)$pmtm='ntf=1,usr='.$usr; 
elseif(!$id && !$desk && !$chat && !$art){
	if($usr && $usr!==0)$pmtm='noab=1,tm='.$usr; elseif($own)$pmtm='tm='.$own; else $pmtm='';}
else $pmtm='';
$hid=hidden('prmtm',$pmtm);
$ban=''; $hdash=''; $htime='';
if($bigban){$ban.=div($bigban,'bigprofile'); $hdash=' hdash'; $htime=' htime';}
$ret=div($board,'dashboard'.$hdash);
$ret.=div($publish.div($stream,'','tlxbck'),'timeline'.$htime,'wrapper').$hid;
return $ban.div($ret,'container');}
}
?>