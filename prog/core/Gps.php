<?php

class Gps{
	
	static function example(){
		$p['lat']=48.8390804;
		$p['lon']=2.23537670;
	}
	
	//http://adresse.data.gouv.fr/api/
		//$url=$host.'search/?q=8 bd du port&limit=15';
		//$url=$host.'search/?q=8 bd du port&lat=48.789&lon=2.789';
		//$url=$host.'search/?q=8 bd du port&postcode=44380';
		//$url=$host.'search/?q=paris&type=street';
		//$url=$host.'reverse/?lat=48.8390804&lon=2.23537670&type=street';
	static function api($p){
		$lat=val($p,'lat');
		$lon=val($p,'lon');
		$req=val($p,'req');
		$mode=val($p,'mode');
		$limit=val($p,'limit',5);
		$host='http://api-adresse.data.gouv.fr/';
		if($mode=='search')$url=$host.'search/?q='.$req.'&limit='.$limit;
		//elseif($mode=='postcode')$url=$url=$host.'search/?q=&postcode='.$req.'';
		else $url=$host.'reverse/?lat='.$lat.'&lon='.$lon.'';
		$d=File::read($url);
		//$d=utf8_decode($d);
		//echo Json::error();
	//	$d=mb_convert_encoding($d,'UCS-2BE','UTF-8');
		//$d=utf8_decode($d);
		//$d=self::json_utf($d);
		//$d=self::unicode2html($d);
		if($d)return json_decode($d,true);//,512,JSON_UNESCAPED_UNICODE
	}
	
	//give gps from town
	//Gps::search(['req'=>'address','mode'=>'search']);
	static function search($p){
		$req=val($p,'request');
		$mode=val($p,'mode','search');//postcode
		$r=self::api(['req'=>$req,'mode'=>$mode]); //pr($r);
		return $r;}
	
	/*
		[street] => Rue de Châteaudun
		[label] => 1 TER Rue de Châteaudun 92100 Boulogne-Billancourt
		[distance] => 7
		[context] => 92, Hauts-de-Seine, Île-de-France
		[id] => 92012_1430_187b83
		[postcode] => 92100
		[citycode] => 92012
		[name] => 1 TER Rue de Châteaudun
		[city] => Boulogne-Billancourt
		[housenumber] => 1 TER
		[score] => 0.99999997442737
		[type] => housenumber*/
		//$ret=$r['features'][0]['properties']['label'];
		//$ret=$r['features'][0]['properties']['city'];
	
	static function unicode2html($d){$i=65535;
		while($i>0){$hex=dechex($i); $d=str_replace("\u$hex","&#$i;",$d); $i--;}
		return $d;}
		
	static function json_utf($d){return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/',function($match){return mb_convert_encoding(pack('H*',$match[1]),'UTF-8','UCS-2BE');},$d);}
	
	//give town from gps
	//Gps::com(['coords'=>'']);
	static function com($p){
		$coords=val($p,'coords'); if($d=sesr('apigps',$coords))return $d;
		list($lat,$lon)=explode('/',$coords);
		$r=self::api(['lat'=>$lat,'lon'=>$lon]);
		$ret=$r['features'][0]['properties']['city'];
		sesr('apigps',$coords,$ret);
		return $ret;}
		
	//interface
	static function content($p){$ret='';
		$type=val($p,'type');
		$r=self::api($p); //pr($r);
		$ret=$r['features'][0]['properties'][$type];
		return $ret;}
	
}

?>