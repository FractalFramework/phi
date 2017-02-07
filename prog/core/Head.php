<?php

class Head{
static $add;

static function headerHtml(){
	return '<!DOCTYPE html>
<html lang="fr" xml:lang="fr">';}

static function meta($attr,$prop,$content=''){
	return '<meta '.$attr.'="'.$prop.'"'.($content?' content="'.$content.'"':'').'>';}

static function cssLink($url){
	if(strrchr($url,'.')=='.css')
		return '<link href="/'.ses('dev').$url.'" rel="stylesheet" type="text/css">';}

static function jsLink($url){
	if(substr($url,0,4)!='http')$root='/'.ses('dev'); else $root='';
	return '<script src="'.$root.$url.'"></script>';}

static function cssCode($code){
	return '<style type="text/css">'.$code.'</style>';}

static function jsCode($code){
	return '<script type="text/javascript">'.$code.'</script>';}

static function add($action,$values){
	self::$add[][$action]=$values;}

static function build(){$ret='';
	$headerArray=self::$add;
	if($headerArray){
		foreach($headerArray as $k=>$v){		
			if(is_array($v))$va=current($v);
			switch(key($v)){
				case('code'):$ret.=$va."\n"; break;
				case('csslink'):$ret.=self::cssLink($va)."\n"; break;
				case('jslink'):$ret.=self::jsLink($va)."\n"; break;
				case('csscode'):$ret.=self::cssCode($va)."\n"; break;
				case('jscode'):$ret.=self::jsCode($va)."\n"; break;
				case('rel'):$ret.='<link rel="'.$v['rel']['name'].'" href="'.$v['rel']['value'].'">'."\n"; break;
				case('meta'):$ret.=self::meta($v['meta']['attr'],$v['meta']['prop'],$v['meta']['content'])."\n"; break;
				case('Tag'):$ret.=tag($v['Tag']['tag'],$v['Tag']['props'],$v['Tag']['txt']); break;
			}
		}
		return $ret;
	}
}

static function generate(){
	return self::headerHtml().tag('head','',self::build());}

}