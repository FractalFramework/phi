<?php

class convert{

	static function headers(){
		Head::add('csscode','
		.txarea{
			border:1px dotted grey; 
			padding:8px 12px; 
			width:50%; 
			height:70vh; 
			overflow-y:auto;
		}');
		Head::add('meta',array('attr'=>'property','prop'=>'description','content'=>'conversions encode decode characters'));
	}
	
	static function clean_mail($ret){
		$ret=str_replace(".\n",'.µµ',$ret);
		$ret=str_replace("\n",'µ',$ret);
		$ret=mb_ereg_replace('µµ',"\n\n",$ret);
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
		return $ret;
	}
	
	static function bin2ascii($d){$ret='';
		$d=str_replace("\n",'',$d); $d=str_replace(' ','',$d);
		$n=strlen($d); $nb=ceil($n/8);
		for($i=0;$i<$nb;$i++)$r[]=substr($d,$i*8,8);
		foreach($r as $v)$ret.=chr(bindec($v));
		return $ret;
	}
	
	static function ascii2bin($d){$ret='';
		$r=str_split($d);
		foreach($r as $v)$ret.=str_pad(decbin(ord($v)),8,'0',STR_PAD_LEFT).' ';
		return $ret;
	}
	
	static function exe($p,$d){
		switch($p){
			case('connectors'):$d=Trans::convert($d); break;
			case('clean_mail'):$d=self::clean_mail($d); break;
			case('url-decode'):$d=rawurldecode($d); break;
			case('url-encode'):$d=rawurlencode($d); break;
			case('utf8-decode'):$d=utf8_decode($d); break;
			//case('utf8-decode'):$d=mb_convert_encoding($d,'HTML-ENTITIES','UTF-8'); break;
			case('utf8-encode'):$d=utf8_encode($d); break;
			case('base64-decode'):$d=utf8_decode($d); break;
			case('base64-encode'):$d=base64_encode($d); break;
			case('htmlentities-encode'):$d=htmlentities($d,ENT_COMPAT,'UTF-8'); break;
			case('htmlentities-decode'):$d=html_entity_decode($d); break;
			case('timestamp-decode'):$d=date('d/m/Y H:i:s',$d); break;
			case('timestamp-encode'):$d=strtotime($d); break;
			case('bin-dec'):$d=bindec($d); break;
			case('dec-bin'):$d=decbin($d); break;
			case('bin-hex'):$d=bin2hex($d); break;
			case('hex-bin'):$d=hex2bin($d); break;
			case('dec-hex'):$d=dechex($d); break;
			case('hex-dec'):$d=hexdec($d); break;
			case('json-decode'):$d=print_r(json_decode($d,true),true); break;
			case('unicode (\u)'):$d=preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/',function($match){return mb_convert_encoding(pack('H*',$match[1]),'UTF-8','UCS-2BE');},$d); break;
			case('unicode (%u)'):$d=unicode($d); break;
			case('iconv'):setlocale(LC_ALL,'en_GB.utf8');
				$d=iconv('UTF-8','ASCII//TRANSLIT',$d); break;
			case('ascii_encode'):$d=mb_convert_encoding($d,'US-ASCII','UTF-8'); break;
			case('ascii_decode'):$d=mb_convert_encoding($d,'ASCII'); break;
			case('ascii2utf8'):$d=self::ascii2utf8($d); break;
			case('bin2ascii'):$d=self::bin2ascii($d); break;
			case('ascii2bin'):$d=self::ascii2bin($d); break;
			case('ord'):$d=ord($d); break;
			case('md5'):$d=md5($d); break;
		}
		return $d;
	}
	
	static function com($prm){$ret='';
		$conv=val($prm,'mode'); $txt=val($prm,'code');
		return self::exe($conv,$txt);
	}
	
	#content
	static function content($prm){$ret='';
		$r=array('connectors','clean_mail','url-decode','url-encode','utf8-decode','utf8-encode','htmlentities-decode','htmlentities-encode','base64-decode','base64-encode','timestamp-decode','timestamp-encode','bin-dec','dec-bin','bin-hex','hex-bin','dec-hex','hex-dec','json-decode','unicode (%u)','unicode (\\u)','iconv','ascii_encode','ascii_decode','ascii2utf8','bin2ascii','ascii2bin','ord','md5');
		foreach($r as $v)$ret.=Ajax::j('input,res|convert,com|mode='.$v.'|code',$v,'btn').' ';
		$ret.=br();
		$ret.=tag('textarea',array('id'=>'code','class'=>'txarea left'),'');
		$ret.=tag('textarea',array('id'=>'res','class'=>'txarea'),'');
		$ret.div('','clear');
		return $ret;
	}
}

?>