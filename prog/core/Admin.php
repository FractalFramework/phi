<?php

class Admin{
	
	#see code
	static function seeCode($p){
		if(isset($p['appSee'])){$dr=ses('dev').'/app';
			$f=$p['appSee'].'.php';
			$r=scandir($dr);
			if($r)foreach($r as $k=>$v)if(is_dir($dr.'/'.$v))
				if(is_file($dr.'/'.$v.'/'.$f))$appFile=$dr.'/'.$v.'/'.$f;
			if($appFile)$ret=file_get_contents($appFile);
			if(auth(6))$bt=aj('popup|dev,com|headers=1,f='.$appFile,lang('edit'),'btn');
			if(isset($ret))return div($bt.Build::Code($ret),'pane');
		}
	}
	
	static function comdir(){
	    $dirs=Dir::read('app');
		foreach($dirs as $dir=>$files){
			if(is_array($files) && $dir)foreach($files as $k=>$file){
			if(is_string($file))$app=before($file,'.');
			if($app)$private=isset($app::$private)?$app::$private:0;
			$dr='Apps/'.$dir;
			if(!$private or ses('auth')>=$private)
				$r[]=array($dr,'j','popup,,,injectJs|'.$app.'|headers=1','',$app);
				//$r[]=array($dr,'lk','/'.$app,'',$app);
			}
		}
		return $r;
	}
	
	static function com(){
		$keys='id,dir,type,com,picto,bt';
		$r=Sql::read($keys,'desktop','id','where uid="'.ses('uid').'" or auth=0 order by dir');
		if(is_array($r))foreach($r as $k=>$v)$r[$k][0]='Apps'.$r[$k][0];//add root
		return $r;
	}

	#menus
	//array('folder','j/lk/in/t','action','picto','text')
	static function menus(){
		$ra[]=array('','lk','/','home','home');
		$login=Auth::logbt(1);
		//$login=App::open('login');
		$r=self::com();
		if(!$r)$r=self::comdir();
		$r=array_merge($ra,$r);
		$app=ses('app');
		$r[]=array('','t','','-',$login);
		//if(!class_exists($app))return $r;
		//$r[]=array('','lk','/app/'.$app,'',$app);
		if(auth(4) && $app)$r[]=array('','j','pagup|Admin,seeCode|appSee='.$app,'code','Code');
		if($app && method_exists($app,'admin')){$rb=$app::admin(); if($rb)$r=array_merge($r,$rb);}
		if(auth(6)){
			$r[]=array(ses('dev').'/dev','lk','/?app='.$app.'&dev==','wrench','dev');
			$r[]=array(ses('dev').'/dev','lk','/?app='.$app.'&dev=prod','wrench','prod');
			$r[]=array(ses('dev'),'j','popup|dev2prod','cloud-upload','publish');
			$r[]=array('','t','','timer',chrono('load'));}
		elseif(ses('dev')=='prog')$r[]=array('','lk','/?app='.$app.'&dev=prod','wrench','prod');
		return $r;
	}
	
	#content
	static function content($p){
		$app=val($p,'app'); ses('app',$app);
		$ret=Menu::call(array('app'=>'Admin','method'=>'menus','css'=>'fix'));
		return $ret;
	}
	
}

?>