<?php

class dev{
	static $private='6';

	static function injectJs(){
		return '';
	}
	static function headers(){
		Head::add('jscode',self::injectJs());
		Head::add('csscode','');
	}
	
	static function admin(){
		//$r[]=array('','j','popup|dev,menu','file',lang('open'));
		//return $r;
	}
	
	static function menubt($dr,$f){
		return Ajax::j('devedit|dev,read|f='.$dr.'/'.$f,$f);
		//$r[]=array($dr,'j','popup|dev,menu','file',$f);
	}
	
	static function menu_old(){$ret='';
		$rb=Dir::walk('dev','menubt','prog','',0);
		if($rb)$ret=implode('',$rb);
		return div($ret,'list');
	}
	
	
	static function menu(){
		$r=Dir::explore('prog');
		foreach($r as $k=>$v){
			$f=after($v,'/');
			$dr=before($v,'/');
			$rb[$f]= Ajax::j('devedit|dev,read|f='.$dr.'/'.$f,$f);
		}
		if($rb)ksort($rb,SORT_STRING);
		if($rb)$ret=implode('',$rb);
		return div($ret,'list');
	}
	
	static function model($prm){
		$app=val($prm,'app');
		$d=File::read('app/model.php');
		$d=str_replace('model',$app,$d);
		return $d;
	}
	
	static function add($prm){
		$ret=input('inp1','app name','',1);
		$ret.=Ajax::j('devedit|dev,create||inp1',lang('create'),'btn').' ';
		return $ret;
	}
	
	static function exists($f,$dr){
		$r=Dir::explore($dr);
		foreach($r as $k=>$v)$r[$k]=portion($v,'/','.',1,1);
		if(in_array($f,$r))return true;
	}
	
	static function create($prm){
		$new=val($prm,'inp1');
		$prm['f']='app/dev/'.$new.'.php';
		$prm[ses('rid')]=self::model(array('app'=>$new));
		$ex=self::exists($new,'app');
		if(!$ex)$ex=self::exists($new,'core');
		if(!$ex)return self::save($prm); else return lang('already exists');
	}
	
	static function save($prm){
		$f=val($prm,'f');
		$d=val($prm,ses('rid'));
		if(auth(6) && $f)File::write($f,$d);
		return self::read($prm);
	}
	
	static function see($prm){return val($prm,ses('rid'));}
	static function del($prm){unlink(val($prm,'f'));}
	
	static function read($prm){
		$f=val($prm,'f'); $app=portion($f,'/','.',1,1);
		if($f)$txt=file_get_contents($f); else return '';
		$bt=Ajax::j('devedit|dev,read|f='.$f.'|',langp('reload'),'btn').' ';
		//$bt.=Ajax::j('popup|dev,see|f='.$f.'|'.ses('rid'),langp('open'),'btn').' ';
		$bt.=Ajax::j(ses('rid').'|dev,model|app='.$app,langp('reset'),'btn').' ';
		$bt.=Ajax::j('devedit|dev,save|f='.$f.'|'.ses('rid'),langp('save'),'btsav').' ';
		$bt.=Ajax::j(ses('rid').'|dev,del|f='.$f,langp('erase'),'btdel').' ';
		$bt.=Ajax::j('popup|'.$app,langp('load'),'btn').' ';
		$bt.=href('/app/'.$app,langp('open'),'btn','',1).br();
		$ret=tag('textarea',array('id'=>ses('rid'),'class'=>'txarea'),htmlentities($txt));
		return $bt.$ret;
	}
	
	static function com($prm){
		$ret=self::read($prm);
		ses('rid',randid('dev'));
		return div($ret,'','devedit');
	}
	
	static function content($prm){
		$bt=Ajax::j('popup|dev,add',langp('new'),'btn').' ';
		$bt.=Ajax::j('popup|dev,menu',langp('open'),'btn').' ';
		return $bt.self::com($prm);
	}
}
?>
