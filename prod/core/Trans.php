<?php

class Trans{
	static $conn=['h1'=>'h1','h2'=>'h2','h3'=>'h3','b'=>'b','i'=>'i','u'=>'u','blockquote'=>'q','em'=>'b','sup'=>'e','strike'=>'k','small'=>'s','sup'=>'sup','sub'=>'sub','ul'=>'list','ol'=>'numlist'];

	static function tags($tag,$atb,$txt){switch($tag){
	case('a'): $u=segment($atb,'href="','"'); return '['.$u.'*'.$txt.':url]'; break;
	case('img'): $u=segment($atb,'src="','"'); return '['.$u.':img]'; break;
	case('table'): return '['.$txt.':table]';break;
	case('tr'): return $txt.'¬'; break;
	case('td'): return $txt.'|'; break;
	case('li'): return $txt."\n"; break;}
	$r=self::$conn;
	foreach($r as $k=>$v)if($txt && $k==$tag)$txt='['.$txt.':'.$v.']';
	return $txt;}

	static function recursearch($v,$ab,$ba,$tag){//pousse si autre balise similaire
	$bb=strpos($v,'>',$ba); $txt=self::ecart($v,$ab,$ba); 
	if(strpos($txt,'<'.$tag)!==false){$bab=strpos($v,'</'.$tag,$ba+1);
		if($bab!==false)$ba=self::recursearch($v,$bb,$bab,$tag);}
	return $ba;}
	
	static function ecart($v,$a,$b){return substr($v,$a+1,$b-$a-1);}
	static function cleanconn($d){$r=self::$conn;
	$d=str_replace('['."\n","\n".'[',$d);
	foreach($r as $k=>$v){
		$d=str_replace("\n".':'.$v.']',':'.$v.']',$d);
		$d=str_replace(' :'.$v.']',':'.$v.'] ',$d);
		$d=str_replace('[:'.$v.']','',$d);}
	return $d;}
	
	static function convert($v,$x=''){
	$tag=''; $atb=''; $txt=''; $before='';
	$aa=strpos($v,'<'); $ab=strpos($v,'>');//tag 
	if($aa!==false && $ab!==false && $ab>$aa){
	$before=substr($v,0,$aa);//...<
	$atb=self::ecart($v,$aa,$ab);//<...>
		$aa_end=strpos($atb,' ');
		if($aa_end!==false)$tag=substr($atb,0,$aa_end);
		else $tag=$atb;}
	$ba=strpos($v,'</'.$tag,$ab); $bb=strpos($v,'>',$ba);//end
	if($ba!==false && $bb!==false && $tag && $bb>$ba){ 
		$ba=self::recursearch($v,$ab,$ba,$tag);
		$bb=strpos($v,'>',$ba);
		$tagend=self::ecart($v,$ba,$bb);
		$txt=self::ecart($v,$ab,$ba);}
	elseif($ab!==false)$bb=$ab;
	else{$bb=-1;}
	$after=substr($v,$bb+1);//>...
	$tag=strtolower($tag);
	//itération
	if(strpos($txt,'<')!==false)$txt=self::convert($txt,$x);
	if(!$x)//interdit l'imbrication
		$txt=self::tags($tag,$atb,$txt);
	//sequence
	if(strpos($after,'<')!==false)$after=self::convert($after,$x);
	$ret=$before.$txt.$after;
	return $ret;}
	
	static function call($p){
	$txt=val($p,'txt');
	//$txt=unicode($txt);
	if(!val($p,'brut'))$txt=deln($txt);
	$txt=del_p($txt);
	$txt=clean_firstspace($txt);
	$txt=clean_n($txt);
	$txt=Trans::convert($txt);
	$txt=Trans::cleanconn($txt);
	return $txt;}
}

?>