<?php

class model{
static $private='1';
static $db='model';
	
static function injectJs(){return '';}
		
static function headers(){
	Head::add('csscode','.txt textarea{width:100%;}');
	Head::add('jscode',self::injectJs());}

static function cols(){return ['tit','txt'];}
	
static function install(){$cols=self::cols();
	Sql::create(self::$db,['uid'=>'int',$cols[0]=>'var',$cols[1]=>'text'],1);}
	
#operations
	/*static function sysedit($p){//default editor
	return Form::com(['table'=>self::$db,'id'=>val($p,'id')]);}*/
	
static function del($p){
	$id=val($p,'id'); $rid=val($p,'rid');
	if(val($p,'ok'))Sql::delete(self::$db,$id);
	else return aj($rid.'|model,del|ok=1,rid='.$rid.',id='.$id,lang('confirm deleting'),'btdel');
	return self::com($p);}
	
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
	$id=val($p,'id'); $rid=val($p,'rid'); $xid=val($p,'xid');
	$cols=self::cols(); $colstr=implode(',',$cols);
	$ret=aj($rid.'|model,menu|rid='.$rid.',xid='.$xid,langp('back'),'btn');
	$ret.=aj($rid.'|model,save|rid='.$rid.'|'.$colstr,lang('save'),'btsav').br();
	$ret.=input('tit','').br();
	$ret.=textarea('txt','','70',7);
	return $ret;}
	
#editor	
static function edit($p){
	$id=val($p,'id'); $rid=val($p,'rid'); $xid=val($p,'xid');
	$cols=self::cols(); $colstr=implode(',',$cols);
	$r=Sql::read($colstr,self::$db,'ra',['id'=>$id]);
	$ret=aj($rid.'|model,menu|rid='.$rid.',xid='.$xid,langp('back'),'btn');
	$ret.=aj($rid.',,load|model,modif|rid='.$rid.',xid='.$xid.',id='.$id.'|'.$colstr,langp('modif'),'btsav');
	$ret.=aj($rid.'|model,del|rid='.$rid.',xid='.$xid.',id='.$id,langp('delete'),'btdel');
	$ret.=aj('popup|model,call|rid='.$rid.',xid='.$xid.',id='.$id,langp('view'),'btn');
	//$ret.=aj('popup|model,sysedit|id='.$id,langp('edit'),'btsav');//default editor
	if($xid)$ret.=insertbt(lang('use'),$id.':model',$xid);
	$ret.=div(input('tit',$r['tit']),'tit');
	$ret.=div(textarea('txt',$r['txt'],'',7),'txt');
	return $ret;}
	
#reader
static function build($p){$id=val($p,'id');
	$cols=self::cols(); $colstr=implode(',',$cols);
	$r=Sql::read($colstr,self::$db,'ra',['id'=>$id]);
	$ret['tit']=$r['tit'];
	//telex will use Conn; this var is sent by telex::reader
	if(val($p,'brut'))$ret['txt']=$r['txt'];
	//connectors can be personalised using app::method
	else $ret['txt']=Conn::load(['msg'=>$r['txt'],'app'=>'','mth'=>'','ptag'=>1]);
	return $ret;}
	
static function menu($p){$rid=val($p,'rid'); $xid=val($p,'xid');
	$r=Sql::read('id,tit,dateup',self::$db,'rr','order by id desc limit 20');
	$ret['head']=hlpbt('model');
	$ret['head'].=aj($p['rid'].'|model,add|rid='.$p['rid'],langp('add'),'btn');
	if($r)foreach($r as $k=>$v){$btn=$v['tit']?$v['tit']:$v['id'];//.$v['date']
		$ret['obj'][]=aj($rid.'|model,edit|rid='.$rid.',xid='.$xid.',id='.$v['id'],$btn);}
	//Phylogeny (motor of templates)
	$structure=['head','list'=>'obj'];
	//returns $ret['head'].div($ret['obj'],'list')
	return Phylo::read($ret,$structure);}
	
#interfaces
//title (used by object of desktop and shares)
static function tit($p){$id=val($p,'id');
	if($id)return Sql::read('dateup',self::$db,'v','where id='.$id);}

//template made with connectors
static function template(){
	return '
[
	[_tit*class=tit:div]
	[_txt*class=txt:div]
*class=board:div]';}
	
//call (used by connectors)
static function call($p){
	$r=self::build($p);
	$template=self::template();
	//Vue (motor of templates)
	$ret=Vue::read($r,$template);
	return $ret;}
	
//com (apps)
static function com($p){$id=val($p,'id');
	//rid (will focus on telex editor), rid (used for load onplace)
	$p['xid']=val($p,'rid');
	$p['edit']=1;//objects used for edition don't appear to public
	return self::content($p);}
	
//interface
static function content($p){
	self::install();
	$p['rid']=randid('md');
	$id=val($p,'id',val($p,'param'));
	if($id && val($p,'edit'))$ret=self::edit($p);
	elseif($id)$ret=self::call(['id'=>$id]);
	else $ret=self::menu($p);
	return div($ret,'board',$p['rid']);}
}
?>