<?php

class admin_db{
	static $private='6';
	static $open;
	
	static function headers(){
		Head::add('csscode','');
	}

	static function save($prm){
		$root=val($prm,'root');
		//$inputName=val($prm,'inputName');//give inp1
		$inp1=$prm['inp1'];//$prm[$inputName]
		Data::add($root,$inp1);
		return self::read($root);
	}
	
	static function add($root){
		$ret=tag('input','type=text,id=inp1','','shortTag');
		$ret.=aj('dataTable|dbtest,save|root='.$root.'|inp1',lang('add'),'btn');
		return $ret;
	}
	
	static function read($p){
		if(isset($p['file']))$d=$p['file']; else return;
		$d=after($d,'/',1);
		$r=Data::read($d);
		return Build::table($r);
	}
	
	static function init($root){
		if(!is_file(Data::file($root))){
			$datas=array(1=>'one',2=>'two',3=>'three');
			Data::write($root,$datas);}
	}
	
	static function menus(){$ret='';
		$r=Dir::explore('db');
		foreach($r as $k=>$v){
			$v=before($v,'.');
			$file=after($v,'/'); 
			$dir=before($v,'/');
			$ret[]=array($dir,'j','popup|admin_db,read|file='.$v,'map',$file);
			//echo $dir.br();
		}
		return $ret;
	}
	static function planeNav($p){$ret='';
		$d=isset($p['root'])?$p['root']:'db';
		$db=str_replace('/','',$d);
		$r=Dir::read($d); //pr($r);
		if($r)foreach($r as $k=>$v)
			if(is_numeric($k))$ret.=Ajax::j($db.'|admin_db,read|file='.$d.'/'.before($v,'.'),$v,'btn');
			else $ret.=Ajax::j($db.'|admin_db,planeNav|root='.$d.'/'.$k,$k,'btn');
				//$ret.=href('/app/admin_db:root='.$d.'/'.$k,$k);
		$ret.=tag('div',array('id'=>$db),'');
		return $ret;
	}
	
	/*
		$root='one/two'; //Data::del($root);
		self::init($root);
		$datas=Data::read($root); //p($datas);
		$ret=tag('div','',self::add($root));
		$ret.=tag('div','id=dataTable',Build::table($datas));
		return $ret;
	*/
	static function content($prm){$ret='';
		//$ret=Menu::call(array('app'=>'admin_db','method'=>'menus'));
		//Data::del('ummo');
		//Dir::remove('db/ummo');
		$ret.=self::planeNav($prm);
		return $ret;
	}
	
	
}