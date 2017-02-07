<?php

class blockchains{
	static $private=4;

	static function injectJs(){
		return '
		function connread(){
			ajaxCall("div,blkc,2|Conn,load","app=blockchains,mth=reader","msg");}';}
	static function headers(){
		Head::add('jscode',self::injectJs());}
	
	//editor
	static function edit($id){
		$ret=btj('[]','embed_slct(\'[\',\']\',\''.$id.'\')','btn');
		$r=array('furniture','alimentation','building','work','resources');
		foreach($r as $k=>$v)
			$ret.=btj($v,'embed_slct(\'[\',\':'.$v.']\',\''.$id.'\')','btn');
	return $ret;}
	
	//blocks
	static function furniture($p,$o){return $p*$o+1;}
	static function alimentation($p,$o){return $p*$o+($p*2);}
	static function building($p,$o){return $p*$o+($p);}
	static function work($p,$o){return $p*$o-($o/2);}
	static function resources($p,$o){return $p*$o-($p/2);}
	
	//secondary connectors
	static function reader($d,$p=''){
		list($p,$o,$c)=readconn($d);
		if(method_exists('blockchains',$c))$ret=self::$c($p,$o);
		//else $ret=Conn::reader($d,$p);//default connectors
		//$_SESSION['bctot'][]=$ret;
	return $ret;}
	
	//sample
	static function ex(){
		return '[1234*0:furniture]
[1234*1:alimentation]
[1234*2:building]
[1234*3:work]
[1234*4:resources]';}
	
	//interface
	static function content($p){
		$p['rid']='blkc';
		$p['inp']='bcmsg';
		$p['msg']=val($p,'msg',self::ex()); 
		$p['app']='blockchains'; $p['mth']='reader';//use local connectors
		$ret=self::edit('msg');
		$ret.=hlpbt('blockchains');
		$ret.=tag('textarea',array('id'=>'msg','cols'=>'100%','rows'=>'10','onkeyup'=>'connread()'),$p['msg']);
		//param (app=blockchains,mth=reader) will use local connectors instead of default
		$ret.=aj($p['rid'].'|Conn,load|app=blockchains,mth=reader|msg',lang('convert'),'btn').br();
		return $ret.div(Conn::load($p),'',$p['rid']);}
}
?>