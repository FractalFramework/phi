<?php

class map extends appx{
static $private='0';
static $a='map';
static $db='map';
static $cb='map';
static $cols=['address','coords'];
static $typs=['var','var'];

function __construct(){
	$r=['a','db','cb','cols'];
	//self::$cols=sqlcols(self::$db,6);
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));}

static function admin($p){$p['o']='1';
	return appx::admin($p);}

#edit
static function form($p){$loc=val($p,'coords'); $ret=''; $pop='';
	$bt=aj('gpsinp|map,search||address',langp('find',1),'btsav');
	$bt.=btj(langp('use my location'),'geo2(\'coords\')','btsav').' ';
	$ret=div(input('address',val($p,'address'),28,lang('address')).$bt);
	if($loc)$pop=popup('map,play|coords='.$loc,ico('map'),'btn');;
	$ret.=div(input('coords',$loc,28,lang('gps coords')).$pop);
	return $ret.div('','','gpsinp');}

static function del($p){
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function create($p){
	return appx::create($p);}
	
static function modif($p){
	return appx::modif($p);}

static function edit($p){
	return appx::edit($p);}

//gps (for tlex)
static function search($p){
	$r=gps::search($p); $ret='';
	if($r)foreach($r['features'] as $k=>$v){
		$city=mb_convert_encoding($v['properties']['city'],'UCS-2BE','UTF-8');
		$t=$city.' '.$v['properties']['postcode'];
		$loc=$v['geometry']['coordinates'][1].'/'.$v['geometry']['coordinates'][0];//lat,lon
		$slct=btj($t,atj('valfromval',['gps'.$k,'coords']),'btn').hidden('gps'.$k,$loc);
		$pop=popup('map,call|coords='.$loc,pic('gps'),'btn');
		$ret.=div($slct.$pop);}
	return $ret;}

static function request($p){
	$r=self::search($p);
	//$r=gps::api(['req'=>val($p,'address'),'mode'=>'search','limit'=>'1']);
	$rb=$r['features'][0]['geometry']['coordinates']; $gps=$rb[1].'/'.$rb[0];
	$ret.=input('coords',$gps,28,lang('gps coords'));
	$ret.=popup('map,call|coords='.$loc,pic('map'),'btn');
	return $ret;}

static function build($p){
	return sql('address,coords',self::$db,'ra',$p['id']);}

static function play($p){
	$pw=val($p,'pagewidth'); $w=val($p,'w','600'); $h=val($p,'h','400');
	if($pw){if($w>$pw){$w=$pw-50; $h=$pw*1.4;} else{$w=$pw-140; $h=$pw*0.5;}}
	list($lat,$lon)=explode('/',val($p,'coords'));
	$f='http://cartosm.eu/map?lon='.$lon.'&lat='.$lat.'&zoom=14&max-width='.$w.'&height='.$h.'&mark=true&nav=true&pan=true&zb=inout&style=default&icon=down';
	return iframe($f,'100%',$h.'px');}

static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	return appx::tit($p);}

//call (read)
static function call($p){
	if(val($p,'id'))$r=self::build($p);
	if(!isset($r))return help('id not exists','paneb');
	if($r)return self::play($r);}

//com (write)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>
