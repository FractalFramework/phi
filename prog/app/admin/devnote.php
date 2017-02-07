<?php

class devnote{
	static $private='1';
	static $db='devnote';
	
	static function injectJs(){return '';}
		
	static function headers(){
	Head::add('csscode','.txt textarea{width:100%;}');
	Head::add('jscode',self::injectJs());}
		
	static function install(){
	Sql::create(self::$db,array('duid'=>'int','tit'=>'var','txt'=>'text'),1);}
	
	#operations
	/*static function sysedit($p){//default editor
	return Form::com(['table'=>self::$db,'id'=>val($p,'id')]);}*/
	
	static function save($p){$tit=val($p,'tit'); $txt=val($p,'txt');
	$p['id']=Sql::insert(self::$db,[ses('uid'),$tit,$txt]);
	return self::edit($p);}
	
	static function del($p){
	$id=val($p,'id'); $rid=val($p,'rid');
	if(val($p,'ok'))Sql::delete(self::$db,$id);
	else return aj($rid.'|devnote,del|ok=1,rid='.$rid.',id='.$id,lang('confirm deleting'),'btdel');
	return self::com($p);}
	
	static function modif($p){$id=val($p,'id');
	$r=['tit'=>val($p,'tit'),'txt'=>val($p,'txt')];
	Sql::updates(self::$db,$r,$id);
	return self::edit($p);}
	
	static function add($p){
	$id=val($p,'id'); $rid=val($p,'rid'); $xid=val($p,'xid');
	$ret=aj($rid.'|devnote,menu|rid='.$rid.',xid='.$xid,langp('back'),'btn');
	$ret.=aj($rid.'|devnote,save|rid='.$rid.'|tit,txt',lang('save'),'btsav').br();
	$ret.=input('tit','').br();
	$ret.=textarea('txt','','70',7);
	return $ret;}
	
	#editor	
	static function edit($p){
	$id=val($p,'id'); $rid=val($p,'rid'); $xid=val($p,'xid');
	$r=Sql::read('tit,txt',self::$db,'ra',['id'=>$id]);
	$ret=aj($rid.'|devnote,menu|rid='.$rid.',xid='.$xid,langp('back'),'btn');
	$ret.=aj($rid.',,load|devnote,modif|rid='.$rid.',xid='.$xid.',id='.$id.'|tit,txt',langp('modif'),'btsav');
	$ret.=aj($rid.'|devnote,del|rid='.$rid.',xid='.$xid.',id='.$id,langp('delete'),'btdel');
	$ret.=aj('popup|devnote,call|rid='.$rid.',xid='.$xid.',id='.$id,langp('view'),'btn');
	//$ret.=aj('popup|devnote,sysedit|id='.$id,langp('edit'),'btsav');//default editor
	if($xid)$ret.=insertbt(lang('use'),$id.':devnote',$xid);
	$ret.=div(input('tit',$r['tit']),'tit');
	$ret.=div(textarea('txt',$r['txt'],'',7),'txt');
	return $ret;}
	
	#reader
	static function read($p){$id=val($p,'id');
	$r=Sql::read('tit,txt',self::$db,'ra',['id'=>$id]);
	$ret=div($r['tit'],'stit');
	if(val($p,'brut'))$ret.=div($r['txt'],'stxt');
	//connectors can use personalised connectors from app::method
	else $ret.=Conn::load(['msg'=>$r['txt'],'app'=>'','mth'=>'','ptag'=>1]);
	return $ret;}
	
	static function menu($p){$ret='';
	$rid=val($p,'rid'); $xid=val($p,'xid');
	//$dt=val($p,'dt',0); $date=time()-$dt;
	$r=Sql::read('id,tit,dateup',self::$db,'rr','order by id desc limit 20');
	$bt=hlpbt('model');
	$bt.=aj($p['rid'].'|devnote,add|rid='.$p['rid'],langp('add'),'btn');
	if($r)foreach($r as $k=>$v){$btn=$v['tit']?$v['tit']:$v['id'];//.$v['date']
		$ret[]=aj($rid.'|devnote,edit|rid='.$rid.',xid='.$xid.',id='.$v['id'],$btn);}
	if($r)$ret=implode('',$ret);
	return $bt.div($ret,'list');}
	
	#interfaces
	//title for object of desktop
	static function tit($p){$id=val($p,'id');
	return Sql::read('dateup',self::$db,'v','where id='.$id);}
	
	//call (by connectors)
	static function call($p){
	$rb=vals($p,['content']);//empty variables for template
	//$p['brut']=1;//telex will use Conn
	$rb['content']=self::read($p);
	$template='[_content*class=board:div]';
	$ret=Vue::read($rb,$template);
	return $ret;}
	
	//com (apps)
	static function com($p){$id=val($p,'id');
	//rid (will focus on telex editor), rid (used for load onplace)
	$p['xid']=val($p,'rid');
	$p['edit']=1;//objects used for edition don't appear to public
	return self::content($p);}
	
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
