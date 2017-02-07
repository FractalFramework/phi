<?php

class map{
	static $private='1';
	static $db='telex';
	
	static function injectJs(){}
	static function headers(){}
	
	//gps (for telex)
	static function search($p){$rid=val($p,'rid');
		$r=Gps::search($p); $ret=''; //p($p);
		if($r)foreach($r['features'] as $k=>$v){
			$city=mb_convert_encoding($v['properties']['city'],'UCS-2BE','UTF-8');
			$t=$city.' '.$v['properties']['postcode'];
			$loc=$v['geometry']['coordinates'][1].'/'.$v['geometry']['coordinates'][0];//lat,lon
			$ret.=popup('map,com|coords='.$loc,$t,'btn');
			$ret.=insertbt(lang('use'),$loc.':gps',$rid).br();
			//$ret.=telex::publishbt($loc,'gps').br();
			}
		return $ret;}
	
	static function gps($p){$rid=val($p,'rid');
		$ret=btj(langp('use my location'),'geo2(\''.$rid.'\')','btsav').' ';
		$ret.=input('request','',30,lang('gps',1));
		$ret.=aj('gpsback|map,search|rid='.$rid.'|request',langp('find'),'btn');
		$ret.=div('','','gpsback');
		return $ret;}
		
	static function com($p){
		$pw=val($p,'pagewidth');
		$w=val($p,'w','600');
		$h=val($p,'h','400');
		if($pw){if($w>$pw){$w=$pw-50; $h=$pw*1.4;} else{$w=$pw-140; $h=$pw*0.5;}}
		list($lat,$lon)=explode('/',val($p,'coords'));
		$f='http://cartosm.eu/map?lon='.$lon.'&lat='.$lat.'&zoom=14&width='.$w.'&height='.$h.'&mark=true&nav=true&pan=true&zb=inout&style=default&icon=down';
		return iframe($f,$w.'px',$h.'px');}
	
	static function request($p){
		$r=Gps::search($p);//pr($r);
		//$r=Gps::api(['req'=>val($p,'request'),'mode'=>'search','limit'=>'1']);
		if($r)$rb=$r['features'][0]['geometry']['coordinates'];
		$pr['coords']=$rb[1].'/'.$rb[0];
		if($pr)return self::com($pr);}
	
	//interface
	static function content($p){$ret='';
		$ret=input('request','',28);
		$ret.=aj('cbkmap|map,request||request',lang('ok',1),'btn');
		return $ret.div('','','cbkmap');}
}
?>
