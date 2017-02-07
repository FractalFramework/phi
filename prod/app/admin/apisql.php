<?php

class apisql{
	static $private=6;
	static $server='tlex.fr';
	
	static function admin(){
		$r[]=array('','j','popup|apisql,content','plus',lang('open'));
		return $r;
	}
	
	static function build($p){
		$table=val($p,'app','');
		$r=Sql::read('*',$table,'rr','');
		return Api::build($r);
	}
	
	static function call($p){
		$p=val($p,'app','');
		$f='http://'.self::$server.'/api.php?app=apisql&mth=render&p='.$p;
		$d=File::get($f);
		//if(!ses('enc'))$d=utf8_decode($d);//json do it
		if($d)$r=json_decode($d,true); //pr($r);
		if($_SERVER['HTTP_HOST']!=self::$server)
		if(isset($r) && is_array($r)){Sql::insert2($p,$r,1,0); return 'merge '.$p.' ok';}
		else return 'nothing'.Json::error();
	}
	
	static function render($table){
		$keys=Sql::columns($table,3);
		if($table=='login')return;
		elseif($table=='desktop')$wh='where auth=0 or auth=6';
		else $wh='';
		$r=Sql::read($keys,$table,'rr',$wh,0); //pr($r);
		//$ret=json_encode($r);//,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE
		//echo Json::error();
		$ret=json_r($r);
		return $ret;
	}
	
	static function menu($p){//system tables
		$r=array('lang','icons','help','labels','desktop','sys');
		foreach($r as $k=>$v)
			if($v!='login')$ret[]=aj($p['rid'].'|apisql,call|app='.$v,$v,'btn');
		return implode('',$ret);
	}
	
	static function content($p){
		$p['rid']=randid('md');
		$p['p1']=val($p,'param',val($p,'p1'));//unamed param before
		$bt=hlpbt('apisql');
		//$bt=input('app','value1',$p['p1'],'1');
		//$bt.=aj($p['rid'].'|apisql,call||app',lang('import'),'btsav');
		$bt.=self::menu($p);
		return $bt.div('','',$p['rid']);
	}
}
?>