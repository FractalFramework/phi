<?php

class convert{

static function headers(){
	add_head('csscode','');
	add_head('meta',['attr'=>'property','prop'=>'description','content'=>'conversions encode decode characters']);}

static function admin(){
	$r[]=['','pop','core,help|ref=convert_app','','help'];
	$r[]=['editors','pop','txt','','txt'];
	$r[]=['editors','pop','pad','','pad'];
	$r[]=['editors','pop','convert','','convert'];
	return $r;}

static function clean_mail($ret){
	$ret=str_replace(".\n",'.µµ',$ret);
	$ret=str_replace("\n",'µ',$ret);
	$ret=str_replace('µµ',"\n\n",$ret);
	$ret=str_replace('µ',' ',$ret);
return $ret;}

static function ascii2utf8($d){$ret='';
	$r=explode(';',$d);
	foreach($r as $v){
		if(substr($v,0,2)=='&#'){$n=substr($v,2);
			//$va='%u'.utf8_encode(unicode(dechex($n)));
			$va=mb_convert_encoding('&#'.intval($n).';','UTF-8','HTML-ENTITIES');}
			else $va=$v;
		$ret.=$va;}
	return $ret;}

static function parser($d,$m){$d=str_replace("\n",' ',$d);
	$r=explode(' ',$d); foreach($r as $v)if($v)$ret[]=$m($v);
	return implode(' ',$ret);}

static function bin2ascii($d){$ret='';
	$d=str_replace("\n",'',$d); $d=str_replace(' ','',$d);
	$n=strlen($d); $nb=ceil($n/8);
	for($i=0;$i<$nb;$i++)$r[]=substr($d,$i*8,8);
	foreach($r as $v)$ret.=chr(bindec($v)).' ';
	return $ret;}

static function ascii2bin($d){$ret='';
	$r=str_split($d);
	foreach($r as $v)$ret.=str_pad(decbin(ord($v)),8,'0',STR_PAD_LEFT).' ';
	return $ret;}

static function php($d){
	$r=['=','(',')','{','}',',','.','[',']'];
	foreach($r as $k=>$v)$d=str_replace([' '.$v,$v.' '],$v,$d);
	$d=str_replace("\t",'',$d);
	return $d;}

static function rgb2hexa($d){$ret='';
	$d=str_replace(['rgba(','rgb(',')',';'],'',$d);
	$r=explode(',',$d);
	for($i=0;$i<3;$i++)$ret.=str_pad(dechex($r[$i]),2,'0');
	return $ret;}

static function exe($p,$d){
	switch($p){
		case('html2connectors'):$d=trans::convert($d); break;
		case('clean_mail'):$d=self::clean_mail($d); break;
		case('url-decode'):$d=rawurldecode($d); break;
		case('url-encode'):$d=rawurlencode($d); break;
		case('utf8-decode'):$d=utf8_decode($d); break;
		//case('utf8-decode'):$d=mb_convert_encoding($d,'HTML-ENTITIES','UTF-8'); break;
		case('utf8-encode'):$d=utf8_encode($d); break;
		case('base64-decode'):$d=base64_decode($d); break;
		case('base64-encode'):$d=base64_encode($d); break;
		case('htmlentities-encode'):$d=htmlentities($d,ENT_COMPAT,'UTF-8'); break;
		case('htmlentities-decode'):$d=html_entity_decode($d); break;
		case('timestamp-decode'):$d=date('d/m/Y H:i:s',$d); break;
		case('timestamp-encode'):$d=strtotime($d); break;
		case('bin-dec'):$d=self::parser($d,'bindec'); break;
		case('dec-bin'):$d=self::parser($d,'decbin'); break;//decbin()
		case('bin2hex'):$d=self::parser($d,'bin2hex'); break;
		case('hex2bin'):$d=self::parser($d,'hex2bin'); break;
		case('dec-hex'):$d=self::parser($d,'dechex'); break;
		case('hex-dec'):$d=self::parser($d,'hexdec'); break;
		case('hex-ascii'):$d=base_convert($d,16,2); $d.='=>'.self::bin2ascii($d); break;
		case('json-decode'):$d=print_r(json_decode($d,true),true); break;
		case('unicode (\u)'):$d=preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/',function($match){return mb_convert_encoding(pack('H*',$match[1]),'UTF-8','UCS-2BE');},$d); break;
		case('unicode (%u)'):$d=unicode($d); break;
		case('iconv'):setlocale(LC_ALL,'fr_FR.utf8');
			$d=iconv('UTF-8','ASCII//TRANSLIT',$d); break;
		case('ascii_encode'):$d=mb_convert_encoding($d,'US-ASCII','UTF-8'); break;
		case('ascii_decode'):$d=mb_convert_encoding($d,'ASCII'); break;
		case('ascii2utf8'):$d=self::ascii2utf8($d); break;
		case('bin2ascii'):$d=self::bin2ascii($d); break;
		case('ascii2bin'):$d=self::ascii2bin($d); break;
		case('ord'):$d=ord($d); break;
		case('xyz'):list($ad,$dc,$ds)=explode(',',$d); $d=implode(',',xyz($ad,$dc,$ds)); break;
		case('md5'):$d=md5($d); break;
		case('sha256'):$d=hash('sha256',$d); break;
		case('ripemd160'):$d=hash('ripemd160',$d); break;
		case('translate'):$d=yandex::com(['to'=>ses('lng'),'txt'=>$d,'dtc'=>1]); break;
		case('table2array'):$r=explode_array($d,"\n",'|'); $d=db::dump($r,''); break;
		case('php'):$d=self::php($d); break;
		case('deg2rad'):$d=deg2rad($d);break;//
		case('rad2deg'):$d=rad2deg($d);break;//
		case('test'):$d=deg2ra(49.27).'//'.dec2deg(11.5); break;
		case('rgb2hexa'):$d=self::rgb2hexa($d); break;
		case('hexa2rgb'):$d=hexrgb($d); break;
		case('soundex'):$d=soundex($d); break;
		default:$d=$p($d); break;}
	return $d;}

static function com($prm){$ret='';
	$conv=val($prm,'mode'); $txt=$prm['code'];
	return self::exe($conv,$txt);}

#content
static function content($prm){$ret='';
	$r=['translate','table2array','html2connectors','clean_mail','--codages--','url-decode','url-encode','utf8-decode','utf8-encode','htmlentities-decode','htmlentities-encode','base64-decode','base64-encode','timestamp-decode','timestamp-encode','php','md5','ripemd160','sha256','json-decode','unicode (%u)','unicode (\\u)','soundex','--clr--','rgb2hexa','hexa2rgb','--ascii--','ascii_encode','ascii_decode','ascii2utf8','hex-ascii','ord','iconv','bin2ascii','ascii2bin','--math--','bin-dec','dec-bin','bin2hex','hex2bin','dec-hex','hex-dec','deg2rad','rad2deg','--astro--','sunsz','al2km','al2parsec','parsec2km','parsec2al','ra2deg','deg2ra','dec2deg','deg2dec','xyz','test'];
	//foreach($r as $v)$ret.=aj('input,res|convert,com|mode='.$v.'|code',$v,'btn').' ';
	$ret=select('mode',$r,'convert...',1);
	$ret.=aj('input,res|convert,com||code,mode',lang('encode'),'btsav').' ';
	$ret.=br();
	$ret.=textarea('code','','','','','semiarea left','');
	$ret.=textarea('res','','','','','semiarea left','');
	$ret.div('','clear');
	return $ret;}
}

?>