<?php

class Form{
	
	static function build($r){$ret='';
		foreach($r as $k=>$v){
			$ret[$k]['label']=div(label($k,$v['label']),'fcell');
			switch($v['type']){
				case('input'):$d=input($k,'',20,'','100p'); break;//$v['value']
				case('textarea'):$d=textarea($k,'',40,10,'','100p'); break;
				case('select'):$d=select($k,$v['opts'],'',''); break;
				case('checkbox'):$d=checkbox($k,$v['opts']); break;
				case('radio'):$d=radio($v['opts'],$k,''); break;
				case('hidden'):$d=hidden($k,$v['value']); break;
				case('bar'):$d=bar($k); break;}
			$ret[$k]['inp']=div($d,'fcell');}
	return $ret;}
	
	static function buildfromstring($fcom){
		$r=explode(',',$fcom);
		foreach($r as $k=>$v)if(trim($v)){$vb=explode(':',trim($v));//readconn(trim($v))
			$id='q'.($k+1); $type=trim($vb[0]); $val=$vb[1];
			$rb[$id]=['type'=>$type,'label'=>$val];
			//if($type=='var' or $type=='int' or $type=='input')$rb[$id]['value']=$val;
			//if($type=='text' or $type=='textarea')$rb[$id]['value']=$val;
			if($type=='select')$rb[$id]['opts']=explode(';',$vb[2]);
			if($type=='checkbox')$rb[$id]['opts']=explode(';',$vb[2]);
			if($type=='radio')$rb[$id]['opts']=explode(';',$vb[2]);
		}
	return self::build($rb);}
	
	static function save($p){
		unset($p['pagewidth']); unset($p['appName']); unset($p['appMethod']);
		$table=val($p,'table'); unset($p['table']);
		$id=val($p,'id'); if($id)unset($p['id']);
		if($id)$ok=Sql::updates($table,$p,$id);
		else $ok=Sql::insert($table,$p);
		return $id?'updated':'saved:'.$ok;}
	
	static function com($p){$ret=''; 
		$table=val($p,'table'); $id=val($p,'id');
		if($id)$ra=Sql::read('*',$table,'ra','where id='.$id);
		$r=Sql::columns($table,'1'); $rb='';
		foreach($r as $k=>$v){
			$val=isset($ra[$k])?$ra[$k]:'';
			if($v=='var' or $v=='int')
				$rb[]=['type'=>'input','id'=>$k,'value'=>$val,'size'=>'10','label'=>$k];
			if($v=='text')
				$rb[]=['type'=>'textarea','id'=>$k,'value'=>$val,'cols'=>'40','rows'=>'4','label'=>$k];
			if($v=='select')$rb[]=['name'=>$k,'opts'=>explode(',',$val)];
			if($v=='checkbox')$rb[]=['type'=>'checkbox','name'=>$k,'ppts'=>explode(',',$val)];
			if($v=='radio')$rb[]=['type'=>'checkbox','name'=>$k,'ppts'=>explode(',',$val)];
		}
		$ret=self::build($rb).br();
		$inps=implode(',',array_keys($r));
		if($id)$ret.=aj('popup|Form,save|table='.$table.',id='.$id.'|'.$inps,langp('save'),'btn');
		else $ret.=aj('popup|Form,save|table='.$table.'|'.$inps,langp('save'),'btn');
		return $ret;
	}

}
?>