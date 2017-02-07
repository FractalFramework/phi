<?php

class tabler{

	static function headers(){
	}
	static function add_quotes($r){
		foreach($r as $k=>$v)foreach($v as $ka=>$va)$r[$k][$ka]="'".addslashes($va)."'";
		return $r;}
	
	static function unparse($v){
		return str_replace(array('&amp;',"&lt;","&gt;"),array('&','<','>'),$v);}
	
	static function mode($r,$mode){
		$rb=self::add_quotes($r);
		if($mode=='html')return Build::table($r);
		//if($mode=='html')return self::build($r);
		elseif($mode=='csv')return arrayToString($rb,"\n",',');
		elseif($mode=='connectors')return arrayToString($r,'¬','|');
		elseif($mode=='json')return json_encode($r,JSON_PRETTY_PRINT);
		elseif($mode=='sql')return Sql::mysql_array2($r);
		elseif($mode=='php'){$ret='';
			foreach($rb as $k=>$v){
				$v=str_replace(',','(coma)',$v);
				$ret.='$r['.($k+1).']=array('.implode(',',$v).');'."\n";}
			$ret=str_replace('(coma)',',',$ret);
			return $ret;}
	}
	
	static function build($p){
		$d=urldecode(val($p,'txarea'));
		$d=self::unparse($d);
		$d=str_replace(array('<table>','</table>','</tr>','</td>'),'',$d);
		$d=str_replace('"','\"',$d);
		//$d=delbr($d,"\n");
		$ra=explode('<tr',$d);
		array_shift($ra);
		foreach($ra as $k=>$v){
			$v=substr($v,strpos($v,'>')+1);
			$rb=explode('<td',$v);
			array_shift($rb);
			foreach($rb as $kb=>$vb){
				$vb=substr($vb,strpos($vb,'>')+1);
				$rc[$k][$kb]=trim(strip_tags($vb));
			}
		}
		if(!isset($rc))return;
		if(val($p,'data'))return $rc;
		if(isset($rc))return self::mode($rc,$p['mode']);
	}
	
	//tlx
	static function comsav($p){
		$r=self::build($p);
		if(!$r)return;
		$root='tlx/'.ses('user').val($p,'tbt');
		$file=Data::write($root,$r,0);
		$ret=insertbt(langp('use'),ses('user').val($p,'tbt').':tabler',val($p,'rid'));
		//$ret=telex::publishbt(ses('user').val($p,'tbt'),'tabler');
		return $ret.Build::table($r);
	}
	
	static function menu($p){
		
	}
	
	static function com($p){
		$bt=input('tbt',date('ymdhi'),'',lang('title'));
		$bt.=aj('tablr|tabler,comsav|rid='.$p['rid'].',data=1|txarea,tbt',lang('convert'),'btn').' ';
		$bt.=hlpbt('tabler_app').' ';
		$ret=tag('div',array('id'=>'txarea','contenteditable'=>'true'),'');
		return $bt.div($ret,'','tablr');
	}
	
	static function call($p){
		$root=val($p,'id');
		$r=Data::read('tlx/'.$root,'',0);
		$ret=Build::table($r);
		return div($ret,'panec');
	}
	
	#content
	static function content($p){
		$bt=hlpbt('tabler_app').' ';
		$pr=array('html','php','json','csv','connectors','sql');
		$bt.=radio($pr,'mode','html',1).' ';
		$bt.=aj('popup|tabler,build||txarea,mode',lang('convert'),'btn').br();
		$bt.=tag('div',array('id'=>'txarea','contenteditable'=>'true'),'');
		return $bt.div('','','tablr');
	}
}

?>