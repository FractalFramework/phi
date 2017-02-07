<?php
class Ajax{
	static function js($p){
		$call=$p['com'].'|'.$p['app'];
		if(isset($p['prm']) && is_array($p['prm'])){$ret='';	
			foreach($p['prm'] as $k=>$v)if($v)$ret[]=$k.'='.jurl($v);
			$prm=implode(',',$ret);}
		else $prm=isset($p['prm'])?_jr(explode(',',$p['prm'])):'';
		$inp=isset($p['inp'])?$p['inp']:'';
		return atjr('ajaxCall',array($call,$prm,$inp));
	}
	static function call($p,$btn,$css=''){
		if($css)$attr['class']=$css;
		$call=explode(',',$p['com']);
		if($call[0]=='bubble' or $call[0]=='menu')$attr['id']=$call[1];
		$js=self::js($p);
		$attr['onclick']=$js;
		return tag('a',$attr,$btn);
	}
	static function j($ajaxParams,$btn,$css='',$attr=''){
		$r=stringToArray($ajaxParams,'|',',');
		$cbs=array('div','popup','pagup','imgup','bubble','menu','input','after','before','begin');
		if(!in_array($r[0][0],$cbs)){$r[0][1]=$r[0][0]; $r[0][0]='div';}
		if(($r[0][0]=='bubble' or $r[0][0]=='menu') && !val($r[0],1))$r[0][1]=randid('bub');
		if($r[0][0]=='div')$r[2]['target']=$r[0][0];//prm give target
		if(isset($r[2]))$prm=_jr($r[2]); else $prm='';//prm
		if(isset($r[3]))$inp=implode(',',$r[3]); else $inp='';//inp
		$call=implode(',',$r[0]).'|'.implode(',',$r[1]);//js
		$js=atjr('ajaxCall',array($call,$prm,$inp));
		if($css)$attr['class']=$css;
		if($r[0][0]=='bubble' or $r[0][0]=='menu')$attr['id']=$r[0][1];
		if(isset($attr['onclick']))$attr['onclick']=$js.$attr['onclick']; 
		else $attr['onclick']=$js;
		return tag('a',$attr,$btn);
	}
}
?>