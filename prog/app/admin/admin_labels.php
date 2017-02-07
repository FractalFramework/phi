<?php

class admin_labels{
	static $private='6';

	static function headers(){
		Head::add('csscode','');
	}
	
	//install
	static function install(){
		Sql::create('labels',['ref'=>'var','icon'=>'var']);
	}
	
	//save
	static function update($prm){$rid=$prm['rid'];
		Sql::update('labels','icon',$prm[$rid],$prm['id']);
		return self::com($prm);
	}
	
	static function del($prm){
		$nid=Sql::delete('labels',$prm['id']);
		return self::com($prm);
	}
	
	static function save($prm){//$lang=val($prm,'lang');,$lang
		$nid=Sql::insert('labels',array($prm['ref'],$prm['icon']));
		return self::com($prm);
	}
	
	static function edit($prm){$rid=randid('labels');//id
		$r=Sql::read('ref,icon','labels','ra','where id='.$prm['id']);
		$ret=label($rid,$r['ref']);
		$ret.=goodinput($rid,$r['icon']);
		$ret.=Ajax::j('admm,,x|admin_labels,update|id='.$prm['id'].',rid='.$rid.'|'.$rid,lang('save'),'btsav');
		$ret.=Ajax::j('admm,,x|admin_labels,del|id='.$prm['id'],lang('del'),'btdel');
		$ret.=aj('popup|fontawesome',pic('eye'),'btn');
		return $ret;
	}
	
	static function add($prm){//ref,icon
		$ref=val($prm,'ref'); $icon=val($prm,'icon');
		$ret=input('ref',$ref?$ref:'',16,'ref').input('icon',$icon?$icon:'',16,'icon');
		$ret.=Ajax::j('admm,,x|admin_labels,save||ref,icon',lang('save'),'btn');
		return $ret;
	}
	
	//table
	static function select(){$ret='';
		if(ses('auth')>6){
			$ret.=Ajax::j('popup|admin_labels,add',langp('add'),'btn');
			$ret.=Ajax::j('popup|Sql,mkbcp|b=labels',langp('backup'),'btsav');
			if(Sql::exists('labels_bak'))
			$ret.=Ajax::j('popup|Sql,rsbcp|b=labels',langp('restore'),'btdel');}
			$ret.=Ajax::j('admm|admin_labels',langp('reload'),'btn').br();
		return $ret;
	}
	
	static function com(){$rb=array();
		$bt=self::select().br();
		$r=Sql::read('id,ref,icon','labels','','order by ref');
		if($r)foreach($r as $k=>$v){
			$ref=Ajax::j('popup|admin_labels,edit|id='.$v[0],$v[1],'btn');
			$icon=pic($v[2],32);
			if(!$v[2])$rc[$k]=array($ref,$icon); 
			else $rb[$k]=array($ref,$icon.' '.$v[2]);}
		if(isset($rc))$rb=array_merge($rc,$rb);
		array_unshift($rb,array('ref','icon'));
		return $bt.Build::table($rb,'bkg');
	}
	
	//content
	static function content($prm){$ret='';
		//self::install();
		$ret=self::com();
		return div($ret,'','admm');
	}

}

?>