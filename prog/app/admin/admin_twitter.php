<?php

class admin_twitter{
static $private='1';
static $db='admin_twitter';
	
static function injectJs(){return '';}

static function cols(){
	return ['owner','consumer_key','consumer_secret','token_key','token_secret'];}

static function install(){$cl=['uid'=>'int'];
	$cols=self::cols(); foreach($cols as $v)$cl[$v]='var';
	Sql::create(self::$db,$cl,1);}

#operations
static function del($p){
	$id=val($p,'id'); $rid=val($p,'rid');
	if(val($p,'ok'))Sql::delete(self::$db,$id);
	else return aj($rid.'|admin_twitter,del|ok=1,rid='.$rid.',id='.$id,lang('confirm deleting'),'btdel');
	return self::menu($p);}

static function modif($p){$id=val($p,'id');
	$cols=self::cols(); $r=['uid'=>ses('uid')];
	foreach($cols as $v)$r[$v]=val($p,$v);
	Sql::updates(self::$db,$r,$id);
	return self::edit($p);}

static function save($p){
	$cols=self::cols(); $r=[ses('uid')];
	foreach($cols as $v)$r[]=val($p,$v);
	$p['id']=Sql::insert(self::$db,$r);
	return self::edit($p);}
	
static function add($p){
	$id=val($p,'id'); $rid=val($p,'rid');
	$cols=self::cols(); $colstr=implode(',',$cols);
	$ret=aj($rid.'|admin_twitter,menu|rid='.$rid,langp('back'),'btn');
	$ret.=aj($rid.'|admin_twitter,save|rid='.$rid.'|'.$colstr,lang('save'),'btsav').br();
	foreach($cols as $v)$ret.=div(input($v,'',54,$v));
	return $ret;}
	
#editor	
static function edit($p){
	$id=val($p,'id'); $rid=val($p,'rid');
	$cols=self::cols(); $colstr=implode(',',$cols);
	$r=Sql::read($colstr,self::$db,'ra',['id'=>$id]);
	$ret=aj($rid.'|admin_twitter,menu|rid='.$rid,langp('back'),'btn');
	$ret.=aj($rid.',,load|admin_twitter,modif|rid='.$rid.',id='.$id.'|'.$colstr,langp('modif'),'btsav');
	$ret.=aj($rid.'|admin_twitter,del|rid='.$rid.',id='.$id,langp('delete'),'btdel');
	$ret.=aj('popup|admin_twitter,call|rid='.$rid.',id='.$id,langp('view'),'btn');
	foreach($cols as $v)$ret.=div($v,'tit').input($v,$r[$v],54);
	return $ret;}
	
#reader
static function build($p){$id=val($p,'id');
	$cols=self::cols(); $colstr=implode(',',$cols);
	$ret=Sql::read($colstr,self::$db,'ra',['id'=>$id]);
	return $ret;}
	
static function menu($p){$rid=val($p,'rid');
	$r=Sql::read('id,owner',self::$db,'rr',['uid'=>ses('uid')]);
	//$ret['help']=hlpbt('admin_twitter');
	if(!$r)$ret['add']=aj($p['rid'].'|admin_twitter,add|rid='.$p['rid'],langp('add'),'btn');
	if($r)foreach($r as $k=>$v){$btn=$v['owner']?$v['owner']:$v['id'];
		$ret['obj'][]=aj($rid.'|admin_twitter,edit|rid='.$rid.',id='.$v['id'],$btn);}
	$structure=['add','list'=>'obj'];
	return Phylo::read($ret,$structure);}

static function call($p){
	$r=self::build($p);
	return Phylo::read($r,self::cols());}

//interface
static function content($p){
	//self::install();
	$p['rid']=randid('md');
	$id=val($p,'id',val($p,'param'));
	if($id && val($p,'edit'))$ret=self::edit($p);
	elseif($id)$ret=self::call(['id'=>$id]);
	else $ret=self::menu($p);
	return div($ret,'board',$p['rid']);}
}

?>