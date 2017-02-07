<?php

class biogen{
	static $private='1';

	static function injectJs(){
		return '';
	}
	static function headers(){
		Head::add('csscode','
		.rnd{display:inline-block; width:20px; height:20px; border-radius:10px; margin:2px;}
		.gr{background:#00aa00;}
		.yl{background:#aaaa00;}
		');
		Head::add('jscode',self::injectJs());
	}
	
	static function obj($type){
		$c=$type?'gr':'yl';
		$ret=div('','rnd '.$c);
		return $ret;
	}
	
	static function iter($r){$ret='';
		foreach($r as $v)
			$ret.=self::obj($v);
		return div($ret);
	}
	
	/**/
	static function algo_exec($p){
		$p1=val($p,'p1'); $it=val($p,'it'); $rid=val($p,'rid');
		foreach($p as $k=>$v)if(is_numeric($k))$r[$k]=$v;
		$nb=rand(0,count($r)-1);
		$ret[]=$r[$nb];
		//del used emplacement
		unset($r[$nb]); sort($r);
		$ret=aj($rid.'|biogen,algo_exec|'.prm($r),lang('next'),'btn');
		return $ret;
	}
	
	static function algo($p1,$n){
		$na=$n*$p1; $r=[]; //echo $na.'/'.$n;
		//emplacements
		$r=array_pad($r,round($na),1); $r=array_pad($r,$n,0); //p($r);
		for($i=0;$i<$n;$i++){
			$nb=rand(0,count($r)-1);
			$ret[]=$r[$nb];
			//del used emplacement
			unset($r[$nb]); sort($r);
		}
		return self::iter($ret);
	}
	
	static function build($p){$ret='';
		$p1=val($p,'p1'); $it=val($p,'it',1); $rid=val($p,'rid')+1;
		$n=pow(2,$it);
		$ret=self::algo($p1,$n);
		$prm='p1='.$p1.',it='.($it+1).',rid='.$rid;
		$ret.=aj($rid.'|biogen,build|'.$prm,lang('iteration').($it+1),'btn');
		return div($ret).div('','',$rid);
	}
	
	static function content($p){
		$p['rid']=randid('gen');
		$p1=val($p,'p1','0.90'); $it=val($p,'it',1);
		$bt=hlpbt('biogen');
		$bt.=input_label('p1',$p1,'dominance').br();
		$bt.=self::iter(array(1,0));
		$bt.=aj($p['rid'].'|biogen,build||p1',lang('iteration').$it,'btn');
		return $bt.div('','',$p['rid']);
	}
}
?>
