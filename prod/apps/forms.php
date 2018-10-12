<?php

class forms{
static $private='1';
static $a='forms';
static $db='forms';
static $cb='frm';
static $cols=['tit','txt','com','cl'];
static $typs=['var','var','var','int'];
static $open=0;
static $qb='db';

function __construct(){
$r=['a','db','cb','cols','qb'];
foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
appx::install(array_combine(self::$cols,self::$typs));
sqlcreate('forms_vals',['bid'=>'int','uid'=>'int','q1'=>'var','q2'=>'var','q3'=>'var','q4'=>'var','q5'=>'var','q6'=>'var','q7'=>'var','q8'=>'var','q9'=>'var'],1);}

static function admin($p){$p['o']='1'; return appx::admin($p);}
static function injectJs(){return '';}
static function headers(){add_head('jscode',self::injectJs());}

#sys
static function del($p){
$p['db2']='forms_vals';
return appx::del($p);}

static function save($p){
return appx::save($p);}

static function modif($p){
return appx::modif($p);}

#editor
static function form($p){
return appx::form($p);}

static function edit($p){
$p['collect']='forms_vals';
$p['help']='forms_com';
return appx::edit($p);}

static function collect($p){
return appx::collect($p);}

static function usave($p){$id=val($p,'id'); $rid=val($p,'rid'); $vrf='';
$r=vals($p,['q1','q2','q3','q4','q5','q6','q7','q8','q9']);
for($i=1;$i<=9;$i++)$vrf.=$r['q1'];
if(!$vrf)return aj(self::$cb.$id.'|forms,play|id='.$id,langp('registration failed'),'alert');
$nid=sqlsav('forms_vals',[$id,ses('uid'),$r['q1'],$r['q2'],$r['q3'],$r['q4'],$r['q5'],$r['q6'],$r['q7'],$r['q8'],$r['q9']]);
return self::play($p);}

static function udel($p){$id=$p['id'];
sqldel('forms_vals',['bid'=>$id,'uid'=>ses('uid')]);
return self::play($p);}

static function sav_lead($p){$id=val($p,'id');
$r=vals($p,['tit','txt','com']);
$r['com']=str_replace("\n",'',$r['com']);
if($id)sqlups(self::$db,$r,$id);
else $bid=sqlsav(self::$db,[ses('uid'),$r['tit'],$r['txt'],$r['com'],'']);
return self::create($p);}

static function edit_form($p){$com=str_replace("\n",'',val($p,'com')); $id=val($p,'id');
if(!$com)$com='input,name|textarea,message|select,choice,a/b/c|checkbox,options,a/b|radio,choose one,a/b|bar,evaluation'; $com=str_replace('|',"|\n",$com); 
$ret=textarea('com',$com,40,4,lang('fields'),'console').br();
$ret.=div(aj('fscrpt|forms,edit_form|id='.$id.'|com',langp('preview'),'btn')).br();//preview
$r=form::buildfromstring($com);
$ret.=div(self::vue($r),'pane');
return $ret;}

static function create($p){$id=val($p,'id'); $rid=val($p,'rid'); $cb=self::$cb;
if($id)$r=sql('id,tit,txt,com,cl,dateup',self::$db,'ra',$id);
else $r=vals($p,['id','tit','txt','com','cl','date']);
$ret=aj($cb.'|forms,stream|id='.$id.',rid='.$rid,pic('back'),'btn');//back
$ret.=aj($cb.'|forms,sav_lead|id='.$id.',rid='.$rid.'|tit,txt,com',lang('save'),'btsav').br();
//$ret.=tag('h4','',lang('edit form'));
$ret.=input('tit',val($r,'tit'),28,lang('title')).br();
$ret.=textarea('txt',val($r,'txt'),28,4,lang('presentation')).br();//save
if($id)if(self::already($id))return $ret.br().br().help('form is not editable','alert');
$ret.=tag('h4','',lang('edit fields').' '.hlpbt('forms_com'));
$ret.=div(self::edit_form($r),'','fscrpt');
return $ret;}

static function template(){
return '[[(label)*class=cell:div][(inp)*class=cell:div]*class=row:div]';}

static function vue($p){return vue::read_r($p,self::template());}

static function answers($p){$id=val($p,'id');
$com=sql('com',self::$db,'v',$id);
$rv=explode(',',$com); $n=count($rv); for($i=1;$i<=$n;$i++)$vr[]='q'.$i; $vars=implode(',',$vr);
foreach($rv as $v)if($v)$rd[]=strprm($v,',',1);
$r=sql('uid,'.$vars,'forms_vals','rr','where bid='.$id);
array_unshift($rd,''); array_unshift($r,$rd);
return mktable($r);}

static function already($id){
return sql('id','forms_vals','v','where uid='.ses('uid').' and bid='.$id);}

static function play($p){$id=val($p,'id'); $rid=val($p,'rid');
if($id)$r=sql('id,tit,txt,com,cl,dateup',self::$db,'ra',$id);
if($r)$rb=form::buildfromstring($r['com']); else return help('id not exists','paneb');
$ret=div($r['tit'],'tit').div($r['txt'],'txt');
if($r['cl'])$form=help('form closed','alert');
elseif(self::already($id)){$form=help('form_filled','valid');
	$form.=aj(self::$cb.$id.'|forms,udel|id='.$id.',rid='.$rid,langp('remove'),'btdel');}
else{$form=self::vue($rb).br();
$n=substr_count($r['com'],','); for($i=1;$i<=$n;$i++)$vr[]='q'.$i; $vars=implode(',',$vr);
$form.=aj(self::$cb.$id.'|forms,usave|id='.$id.',rid='.$rid.'|'.$vars,langp('send'),'btsav');}//send
return $ret.div($form);}

static function conn($p){$id=val($p,'id'); $rid=val($p,'rid'); $com=val($p,'com'); $cb=self::$cb.$id;
if(!$com)return;
$r=form::buildfromstring($com);
$ret=self::vue($r);
$n=substr_count($com,','); for($i=1;$i<=$n;$i++)$vr[]='q'.$i; $vars=implode(',',$vr);
$ret.=div(aj($cb.'|forms,usave|id='.$id.',rid='.$rid.'|'.$vars,langp('send'),'btsav'));//send
return div($ret,'paneb',self::$cb.$id);}

static function stream($p){
return appx::stream($p);}

#interfaces
static function tit($p){
$p['t']='tit';
return appx::tit($p);}

//call (read)
static function call($p){
return div(self::play($p),'paneb',self::$cb.$p['id']);}

//com (edit)
static function com($p){
return appx::com($p);}

//interface
static function content($p){
//self::install();
return appx::content($p);}
}
?>