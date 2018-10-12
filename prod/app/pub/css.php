<?php

class css{	
static $private=6;
static $db='css';
static $a='css';

static function install(){
sqlcreate(self::$db,['tit'=>'var','txt'=>'var','css'=>'var'],1);}

static function admin(){
$r[]=['','j','popup|css,content','plus',lang('open')];
$r[]=['','pop','core,help|ref=css_app','help','-'];
if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=css','code','Code'];
return $r;}

static function injectJs(){return;}
static function headers(){
add_head('csscode','');
add_head('jscode',self::injectJs());}

static function titles($p){
$d=val($p,'appMethod');
$r['content']='welcome';
$r['build']='model';
if(isset($r[$d]))return lang($r[$d]);}

#build
static function build($p){$css=$p['css'];
$d=file_get_contents(ses('dev').'/css/'.$css.'.css');
$n=strlen($d);
for($i=0;$i<$n;$i++){

}

/**/$r=explode('}',$d);
foreach($r as $k=>$v){
	$tit=before($v,'{',1);
	if(strrpos($tit,'/*')!==false)$tit=substr($tit,strrpos($tit,'/*')+2);
	if(strrpos($tit,'*/')!==false)$tit=substr($tit,strrpos($tit,'*/')+2);
	$txt=segment($v,'{','}');
	$ret[]=[trim($tit),trim($txt),$css];}
	
	//$ret.=autoggle('','css,read|id=',$tit);
	//sqldel(self::$db,$css,'css');
	//sqlsav2(self::$db,$ret);
	//$r=sql('all',self::$db,'ra',$id);
return $ret;}

static function edit($p){$d=val($p,'txt'); $ret='';
$r=self::build($p);
pr($r);
return $ret;}

#read
static function call($p){
$r=self::build($p);
$ret=self::edit($p);
return $ret;}

static function com(){
return self::content($p);}

#content
static function content($p){
self::install();
$p['p1']=val($p,'param',val($p,'p1'));
$ret=div(batch(['global','apps','tlex'],self::$a.'|css,edit|css=$v'),'sticky');
return $ret.div('','pane',self::$a);}
}
?>