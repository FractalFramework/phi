<?php

class admin_lang{
	static $private='6';

	static function headers(){
		Head::add('csscode','');
	}
	static function admin(){
		$r[]=array('manage/admin','','admin_lang','monitor','admin_lang');
		$r[]=array('manage/admin','','admin_help','monitor','admin_help');
		$r[]=array('manage/admin','','admin_icons','monitor','admin_icons');
		return $r;}
	
	//install
	static function install(){
		Sql::create('lang',array('ref'=>'var','voc'=>'var','app'=>'var','lang'=>'var'));
	}
	
	static function equilibrium($prm){
		$r=Sql::read('ref,lang,voc','lang','kkv','where app="'.$prm['app'].'"');
		$rb=array_keys($r);
		foreach($rb as $k=>$v)
			if(!isset($r[$v][$prm['lang']]))
				Sql::insert('lang',array($v,isset($r[$v]['en'])?$r[$v]['en']:'',$prm['app'],$prm['lang']));
		return self::com($prm);
	}
	
	//save
	static function update($prm){$rid=$prm['rid'];
		Sql::update('lang','voc',$prm[$rid],$prm['id']);
		Sql::update('lang','app',$prm['app'.$rid],$prm['id']);
		sesclass('Lang','com',$prm['lang'],1);//update session
		return self::com($prm);
	}
	
	static function del($prm){
		$nid=Sql::delete('lang',$prm['id']);
		sesclass('Lang','com',$prm['id'],1);//update session
		return self::add($prm).br().self::com($prm);
	}
	
	static function save($prm){
		$nid=Sql::insert('lang',array($prm['ref'],$prm['voc'],$prm['app'],$prm['lang']));
		sesclass('Lang','com',$prm['lang'],1);//update session
		return self::add($prm).br().self::com($prm);
	}
	
	static function edit($prm){$rid=randid('voc');//id
		$r=Sql::read('ref,voc,lang,app','lang','ra','where id='.$prm['id']);
		$ret=label($rid,$r['ref'].' ('.$r['lang'].')').input($rid,$r['voc'],16);
		$ro=Sql::read('distinct(app)','lang','rv','');
		$ret.=datalist($ro,'app'.$rid,$r['app'],8,'app');
		$ret.=aj('admlng,,x|admin_lang,update|id='.$prm['id'].',rid='.$rid.',lang='.$r['lang'].',app='.$r['app'].'|'.$rid.',app'.$rid,langp('save'),'btsav');
		$ret.=aj('admlng,,x|admin_lang,del|id='.$prm['id'].',lang='.$r['lang'].',app='.$r['app'],langp('del'),'btdel');
		return $ret;
	}
	
	static function add($prm){//ref,voc
		$ref=val($prm,'ref'); $voc=val($prm,'voc');
		$ret=input('ref',$ref?$ref:'',16,'ref').input('voc',$voc?$voc:'',16,'icon');
		$ret.=aj('admlng,,x|admin_lang,save||app,lang,ref,voc',langp('save'),'btn');
		return $ret;
	}
	
	//table
	static function select($app,$lang){
		$ret=hidden('app',$app).hidden('lang',$lang);
		//langs
		$r=Sql::read('distinct(lang)','lang','rv','');
		foreach($r as $v){$c=$v==$lang?' active':'';
			$rc[]=aj('admlng|admin_lang,com|lang='.$v.'|app',$v,'btn'.$c);}
		$bt=implode(' ',$rc).' :: ';
		//apps
		$r=Sql::read('distinct(app)','lang','rv','order by app');
		if(!$r)$r=Lang::$langs;
		$c=$app=='all'?' active':'';
		$rb[]=aj('admlng,,y|admin_lang,com|app=all|lang','all','btn'.$c);
		foreach($r as $v){$c=$v==$app?' active':'';
			$rb[]=aj('admlng,,y|admin_lang,com|app='.$v.'|lang',$v,'btn'.$c);}
		$bt.=implode(' ',$rb);
		$ret.=div($bt,'pane');
		if(ses('auth')>6){
			$ret.=aj('popup|admin_lang,add|app='.$app,langp('add'),'btn');
			$ret.=aj('admlng|admin_lang,equilibrium||app,lang',langp('update'),'btn');
			$ret.=aj('popup|Sql,mkbcp|b=lang',langp('backup'),'btsav');
			if(Sql::exists('lang_bak'))
			$ret.=aj('popup|Sql,rsbcp|b=lang',langp('restore'),'btdel');}
			$ret.=aj('admlng|admin_lang',langp('reload'),'btn').br();
		return $ret;
	}
	
	static function com($prm){$rb=array();
		$app=val($prm,'app','all'); $lang=val($prm,'lang');
		$bt=self::select($app,$lang).br();
		if($app!='all')$wh=' and app="'.$app.'" '; else $wh='';
		$r=Sql::read('id,ref,voc','lang','','where lang="'.$lang.'"'.$wh.' order by ref');
		foreach($r as $k=>$v){
			if(ses('auth')>6)$ref=aj('popup|admin_lang,edit|id='.$v[0],$v[1],'btn');
			else $ref=$v[1];
			if($v[2])$rb[$k]=array($ref,$v[2]);
			else $rc[$k]=array($ref,$v[2]);}
		if(isset($rc))$rb=array_merge($rc,$rb);
		array_unshift($rb,array('ref',$lang));
		return $bt.Build::table($rb,'bkg');
	}
	
	//content
	static function content($prm){$ret='';
		//self::install();
		$app=val($prm,'app',''); $lang=val($prm,'lang',Lang::$lang);
		$ret=self::com(array('app'=>$app,'lang'=>$lang));
		return div($ret,'','admlng');
	}

}

?>