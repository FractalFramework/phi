<?php

class trans{
static $conn=['b'=>'b','i'=>'i','u'=>'u','em'=>'b','strike'=>'k','small'=>'s','sup'=>'e','sub'=>'n'];
static $conb=['h1'=>'h1','h2'=>'h2','h3'=>'h3','blockquote'=>'q','ul'=>'list','ol'=>'numlist'];

static function tags($tag,$atb,$txt){switch($tag){
case('a'): $u=segment($atb,'href="','"');
	if($u)return '['.trim($u).($txt?'*'.trim($txt):'').':url]'; break;//
case('img'): $u=segment($atb,'src="','"'); $w=segment($atb,'width="','"');
	$h=segment($atb,'height="','"'); if($w)$u.='*'.$w.'-'.$h;
	return '['.$u.']'; break;//:img
case('table'): if(substr($txt,-1,1)=='¬')$txt=substr($txt,0,-1);
	//if(post('th')){$o='*1'; $_POST['th']='';} else $o='';
	return '['.trim($txt).':table]';break;//.$o
case('tr'): if(substr($txt,-1,1)=='|')$txt=trim(substr($txt,0,-1)); return $txt.'¬'; break;
case('th'): $_POST['th']=1; return trim($txt).'|'; break;
case('td'): return trim($txt).'|'; break;
case('li'): return trim($txt)."\n"; break;}
$r=self::$conn; if($txt && isset($r[$tag]))return '['.$txt.':'.$r[$tag].']';
$r=self::$conb; if($txt && isset($r[$tag]))return "\n".'['.$txt.':'.$r[$tag].']'."\n";
return $txt;}

static function recursearch($v,$ab,$ba,$tag){//pousse si autre balise similaire
$bb=strpos($v,'>',$ba); $txt=self::ecart($v,$ab,$ba); 
if(strpos($txt,'<'.$tag)!==false){$bab=strpos($v,'</'.$tag,$ba+1);
	if($bab!==false)$ba=self::recursearch($v,$bb,$bab,$tag);}
return $ba;}

static function ecart($v,$a,$b){return substr($v,$a+1,$b-$a-1);}

static function cleanconn($d){
$d=str_replace('['."\n","\n".'[',$d);
$r=self::$conn+self::$conb;
foreach($r as $k=>$v){
	$d=str_replace("\n".':'.$v.']',':'.$v.']'."\n",$d);
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
$d=val($p,'txt');
//$d=unicode($d);
if(!val($p,'brut'))$d=deln($d);
$d=del_p($d);
$d=clean_firstspace($d);
$d=clean_n($d);
$d=self::convert($d);
$d=self::cleanconn($d);
$d=clean_n($d);
return $d;}
}

?>