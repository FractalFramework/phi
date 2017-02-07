<?php

class Form{
	
	static function build($r){$ret='';
		foreach($r as $k=>$v){
			$ret[$k]['label']=div(label($k,$v['label']),'fcell');
			switch($v['type']){
				case('input'):$d=input($k,$v['value'],20,'','100p'); break;
				case('textarea'):$d=textarea($k,$v['value'],40,10,'','100p'); break;
				case('select'):$d=select($k,$v['opts'],$v['value'],''); break;
				case('checkbox'):$d=checkbox($k,$v['opts'],$v['value']); break;
				case('radio'):$d=radio($v['opts'],$k,$v['value']); break;
				case('hidden'):$d=hidden($k,$v['value']); break;
				case('bar'):$d=bar($k,$v['value']); break;}
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
		if($id)Sql::updates($table,$p,$id,'',1);
		else $nid=Sql::insert($table,$p);
		if($id)return 'updated';
		elseif(isset($nid))return 'saved'.$nid;
		else return 'nothing';}
	
	static function read($r){
		$rb=self::build($r);
		$tmp='[[_label*class=cell:div][_inp*class=cell:div]*class=row:div]';
		return Vue::read_r($rb,$tmp);}
	
	static function com($p){$ret=''; 
		$table=val($p,'table'); $id=val($p,'id');
		if($id)$ra=Sql::read('*',$table,'ra','where id='.$id);
		$r=Sql::columns($table,'1'); $rb='';
		foreach($r as $k=>$v){
			$val=isset($ra[$k])?$ra[$k]:'';
			if($v=='var' or $v=='int')
				$rb[$k]=['type'=>'input','id'=>$k,'value'=>$val,'size'=>'10','label'=>$k];
			if($v=='text')
				$rb[$k]=['type'=>'textarea','id'=>$k,'value'=>$val,'cols'=>'40','rows'=>'4','label'=>$k];
			if($v=='select')$rb[$k]=['name'=>$k,'opts'=>explode(',',$val)];
			if($v=='checkbox')$rb[$k]=['type'=>'checkbox','name'=>$k,'ppts'=>explode(',',$val)];
			if($v=='radio')$rb[$k]=['type'=>'checkbox','name'=>$k,'ppts'=>explode(',',$val)];
		}
		$ret=self::read($rb);
		$inps=implode(',',array_keys($r));
		$vb=$id?',id='.$id:'';
		$ret.=aj('popup,,x|Form,save|table='.$table.$vb.'|'.$inps,langp('save'),'btsav');
		return $ret;
	}

}
?>