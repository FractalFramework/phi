<?php

class slide{
static $private='0';
static $a='slide';
static $db='slide';
static $cb='sld';
static $cols=['tit'];
static $typs=['var'];
static $open=1;

function __construct(){
$r=['a','db','cb','cols'];
foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
appx::install(['tit'=>'var']);
sqlcreate('slide_page',['idn'=>'int','idp'=>'int','bid'=>'int','txt'=>'text','rel'=>'int'],1);}

static function admin($p){$p['o']='1';
return appx::admin($p);}

static function headers(){
add_head('csscode','');}

static function titles($p){
$d=val($p,'appMethod');
$r['addslide']=lang('creating').' '.lang('slide');
$r['mdfslide']=lang('editing').' '.lang('slide');
$r['sldel']=lang('delete').' '.lang('slide');
if(isset($r[$d]))return $r[$d];}

#sys
static function del($p){
return appx::del($p);}

//static function save($p){return appx::save($p);}
static function save($p){$tit=val($p,'tit');
$p['id']=sqlsav(self::$db,['uid'=>ses('uid'),'tit'=>$tit]);
sqlsav('slide_page',array('1','0',$p['id'],lang('first slide'),'0'));
return self::edit($p);}

static function modif($p){return appx::modif($p);}
static function create($p){return appx::create($p);}

#editor
static function form($p){return appx::form($p);}
static function edit($p){return appx::edit($p);}

//sysedit
static function sysedit($p){
$id=val($p,'id'); $idn=val($p,'idn');
$p['table']='slide_page';
$p['cols']='idp,txt,rel';
$p['colslabels']='parent id,content,related id';
$p['act']='modif';
$p['id']=sql('id','slide_page','v','where bid="'.$id.'" and idn="'.$idn.'"');
$ret=edit::com($p);
return $ret;}

//del
static function sldel($p){
$ok=val($p,'ok'); $delall=val($p,'delall'); $rid=val($p,'rid');
$id=val($p,'id'); $idn=val($p,'idn'); if($idn==1)$idn=0;//forbid del first slide
$prm='id='.$id.',idn='.$idn.',rid='.$rid; $cb=self::$cb.$id;
if(!$ok){$prm=$cb.',,x|slide,sldel|'.$prm.',ok=1'; 
	if($delall)return aj($prm.',delall=1',langp('del all slides'),'btdel');
	else return aj($prm,langp('del').' '.lang('slide').': '.$idn,'btdel');}
elseif($id && $delall){sqldel(self::$db,$id); sqldel('slide_page',$id,'bid'); 
	return self::stream($p);}
elseif($id && $idn){//reorder slides and parents after deleting
	qr('delete from slide_page where bid="'.$id.'" and idn="'.$idn.'"');
	$r=sql('id,idn','slide_page','kv','where bid="'.$id.'" and idn>"'.$idn.'" order by idn');
	if($r)foreach($r as $k=>$v){$nidn=$v-1; sqlup('slide_page','idn',$nidn,$k);}
	$r=sql('id,idp','slide_page','kv','where bid="'.$id.'" and idp>="'.$idn.'" order by idn');
	if($r)foreach($r as $k=>$v)if($v){$nidp=$v-1; sqlup('slide_page','idp',$nidp,$k);}
	$p['idn']=$idn-1>0?$idn-1:1; return self::play($p);}}

//add slide
static function lasidn($bid){
$r=sql('idn','slide_page','rv','where bid="'.$bid.'" order by idn');
if($r)return max($r)+1; else return 1;}

static function addsav($p){$aid=val($p,'aid'); $mdf=val($p,'mdf');
if($mdf)sqlups('slide_page',vals($p,['idp','txt','rel']),$aid);
else $nid=sqlsav('slide_page',vals($p,['idn','idp','bid','txt','rel']));
return self::play($p);}

static function addslide($p){
$rid=val($p,'rid'); $id=val($p,'id',1);
$idp=val($p,'idn'); $idn=self::lasidn($id);//idp+1,idn+1
$cb=self::$cb.$id; $cols='idp,txt,rel';
$r=['idp'=>$idp,'txt'=>'','rel'=>''];
$prm=$cb.',,x|slide,addsav|id='.$id.',bid='.$id.',idn='.$idn.',rid='.$rid;
$ret=aj($prm.'|'.$cols,langp('save').' '.lang('slide').' '.$idn,'btsav');
if($r)foreach($r as $k=>$v){
	if($k=='idp')$ret.=div(label($k,lang($k,1).span($v,'nfo')).hidden($k,$v));
	elseif($k=='txt')$ret.=div(textarea($k,$v,40,4).label($k,lang($k,1)));
	else $ret.=div(input($k,$v).label($k,lang($k,1)));}
return $ret;}

static function mdfslide($p){
$rid=val($p,'rid'); $id=val($p,'id',1);//id=bid
$idn=val($p,'idn',1); $idn=val($p,'idn',1); $aid=val($p,'aid');//aid=id slide
$cb=self::$cb.$id; $cols='idp,txt,rel';
$r=sql($cols,'slide_page','ra','where bid='.$id.' and idn='.$idn); //p($r);
$prm=$cb.',,x|slide,addsav|'.'id='.$id.',idn='.$idn.',aid='.$aid.',rid='.$rid.',mdf=1|'.$cols;
$ret=aj($prm,langp('modif').' '.lang('slide').' '.$idn,'btsav');
if($r)foreach($r as $k=>$v){
	if($k=='txt')$ret.=div(textarea($k,$v,40,4).label($k,lang($k,1)));
	else $ret.=div(input($k,$v).label($k,lang($k,1)));}
return $ret;}

//taxonomy
/*static function taxo_clean($r,$rb){
	foreach($rb as $k=>$v)if(isset($r[$v]))unset($r[$v]);
	return $r;}

static function taxo_find($rx,$ra,$rb){$ret='';
	foreach($rb as $k=>$v){
		if(isset($ra[$k])){
			if(is_array($ra[$k])){
				$rb=self::taxo_find($rx,$ra,$ra[$k]);
				$rx=$rb[0];
				$ret[$k]=$rb[1];}
			else $ret[$k]=$ra[$k];
			$rx[]=$k;}
		else $ret[$k]=$v;}
	return [$rx,$ret];}

static function taxonomy($r){$ra=$r; $rx=''; $ret='';
	foreach($r as $k=>$v){
		if(is_array($v)){
			$rb=self::taxo_find($rx,$ra,$v);
			$rx=$rb[0];
			$ret[$k]=$rb[1]?$rb[1]:$v;}
		else $ret[$k]=$v;}
	$ret=self::taxo_clean($ret,$rx);
	return $ret;}*/

static function mktaxo($ra,$r,$rx=''){
	$o=$ra==$r?1:0; //p($r);
	foreach($r as $k=>$v){
		if(isset($ra[$k])){
			if(is_array($v)){
				$rb=self::mktaxo($ra,$v,$rx);
				$rx=$rb[0]; //unset($ra[$k]);//p($rb[1]);
				$ret[$k]=$rb[1]?$rb[1]:$v;}
			else $ret[$k]=$ra[$k];
			//unset($ra[$k]);
			}//if(!$o)$rx[]=$k;
		else $ret[$k]=$v;}
	pr($rx);
	if($o && is_array($rx))foreach($rx as $k=>$v)if(isset($ret[$v]))unset($ret[$v]);
return [$rx,$ret];}

static function read_recursive($id,$r){$ret='';
foreach($r as $k=>$v){//sld18|slide,play|id=18,rid=,aid=113,idn=2
	$bt=aj('sld'.$id.'|slide,call|id='.$id.',idn='.$k,$k,'btn ');
	if(is_array($v))$bt.=self::read_recursive($id,$v);
	$ret.=li($bt,'');}
if($ret)return ul($ret);}

static function topo($p){$id=val($p,'id');
$r=sql('id,idn,idp,txt,rel','slide_page','rr','where bid="'.$id.'" order by idn');//pr($r);
if($r)foreach($r as $k=>$v)$rb[$v['idp']][$v['idn']]=1;
if($rb)$rc=taxonomy($rb,$rb);
//if($rb)$rb=self::mktaxo($rb,$rb,''); $rc=$rb[1];
//$ret=pr($rc,1);
$ret=self::read_recursive($id,$rc);
return $ret;}

//motor
static function build($r,$p){$ret=''; $bt=''; $next='';
$tit=val($p,'tit'); $id=val($p,'id'); $idn=val($p,'inp',val($p,'idn',1)); 
$nav=val($p,'nav'); if($nav=='prev')$idn=$idn>1?$idn-1:$idn; elseif($nav=='next')$idn=$idn+1;
$rid=val($p,'rid'); $own=val($p,'own'); $cb=self::$cb.$id;
if($r)foreach($r as $k=>$v)if($v['idn']==$idn)$ra=$v;
if(isset($ra))$aid=$ra['id']; else $aid='';
$prm='id='.$id.',rid='.$rid.',aid='.$aid.',idn='; $app='slide,play';
$rb['nav']=aj($cb.'|slide,play|'.$prm.$idn.',nav=prev',pic('previous'),'btn').' ';
$rb['nav'].=aj($cb.'|slide,play|'.$prm.$idn.',nav=next',pic('next'),'btn').' ';
$rb[]=aj($cb.'|slide,play|'.$prm.'1',ico('refresh'),'btn');
if($own){
	//$bt.=aj($cb.'|slide,menu|'.$prm.$idn,langp('back'),'btn');
	//$bt.=aj('popup|slide,syseditit|'.$prm.$id,pic('edit'),'btn');
	$bt.=aj('popup|slide,addslide|'.$prm.$idn,langpi('add'),'btsav');
	$bt.=aj('popup|slide,mdfslide|'.$prm.$idn,langpi('modif'),'btn');
	$bt.=aj('popup|slide,sldel|'.$prm.$idn,langpi('del'),'btdel');
	$bt.=aj('popup|slide,sldel|'.$prm.$idn.',delall=1',langpi('delete'),'btdel');}
$bt.=lk('/slide/'.$id,pic('url'),1);
$rb[]=span($bt,'right');
if(isset($ra)){
	$p['id']=$ra['id']; $p['idn']=$idn;
	if($ra['idp'])
		$rb[]=aj($cb.'|'.$app.'|'.$prm.$ra['idp'],pic('previous').$ra['idp'],'btn');
	$rb[]=aj($cb.'|slide,play|'.$prm.$idn,icxt(ics('slide'),$idn),'btn');
	if($ra['rel'])$rb[]=aj($cb.'|'.$app.'|'.$prm.$ra['rel'],pic('parent'),'btn');
	if($r)foreach($r as $ka=>$va){
		if($va['rel']==$idn)
			$rb[]=aj($cb.'|'.$app.'|'.$prm.$va['idn'],pic('child'),'btn');
		if($va['idp']==$idn)
			$next.=aj($cb.'|'.$app.'|'.$prm.$va['idn'],pic('next').$va['idn'],'btn');
		$rb['nxt']=$next;}}
//$here=aj($cb.'|slide,play|'.$prm.$idn,icxt(ics('slide'),$idn),'btn').' ';
$rb['topo']=popup('slide,topo|'.$prm.$idn,langp('topo'),'btn').' ';
if($rb)$bt=div(implode('',$rb));
if(isset($ra))$ret=div($ra['txt'],'','tx'.$rid,'margin:auto;');//nl2br->white-space:pre-wrap;
return $bt.div($ret,'slide');}

static function play($p){$id=val($p,'id');
$r=sql('id,idn,idp,txt,rel','slide_page','rr','where bid="'.$id.'" order by idn');
if(!$r)return help('id not exists','paneb');
$p['own']=sql('id',self::$db,'v','where uid="'.ses('uid').'" and id="'.$id.'"');
$p['tit']=sql('tit',self::$db,'v',$id);
$ret=div($p['tit'],'tit');
return $ret.self::build($r,$p);}

static function stream($p){
return appx::stream($p);}

#interfaces
static function tit($p){
return appx::tit($p);}

//call (read)
static function call($p){$id=val($p,'id');
return div(self::play($p),'',self::$cb.$id);}

//com (write)
static function com($p){
return appx::com($p);}

//interface
static function content($p){
//self::install();
return appx::content($p);}
}
?>