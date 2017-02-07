<?php

class connectors{
	static $private=0;

	static function injectJs(){
		return '
		function connread(){
			ajaxCall("div,conn,2|Conn,load","app=connectors,mth=reader","msg");}';}
	static function headers(){
		Head::add('jscode',self::injectJs());}
	
	//editor
	static function edit($id){
		$ret=btj('[]','embed_slct(\'[\',\']\',\''.$id.'\')','btn');
		$r=array('h','b','i','u','q','s','list','a','url','web','img','tag','pic','art');
		foreach($r as $k=>$v)
			$ret.=btj($v,'embed_slct(\'[\',\':'.$v.']\',\''.$id.'\')','btn');
	return $ret;}
	
	//secondary connectors
	static function reader($d,$p=''){$ret='';
		list($p,$o,$c)=readconn($d);
		switch($c){
			//this will be added to default connectors
			case('art'):return App::open($c,['param'=>$p,'headers'=>1]); break;
			case('web'):$ret=telex::playweb($p); break;
			case('pub'):$t=Sql::read('tit',article::$db,'v','where id="'.$p.'"');
				return aj('popup|art|param='.$p,pic('file-text-o').' '.$t,'btn'); break;
			default:$ret=Conn::reader($d,$p); break;}//default connectors
	return $ret;}
	
	//sample
	static function ex(){
		return '[Div*btn:div][Span*btn:span] [Italic:i] [Bold:b] [http://ph1.net] [[http://tlex.fr*Telex:url]*btn:span] jhgjhg
		
[Quote:q]

//use app*method:app
[clock:app:no]

//use personalized connector
[9:pub]

//use param*method:appName
[9:art]

[http://1nfo.net/img/newsnet_118636_b7a43e.jpg]
';}

	/*static function read($p){
		return Conn::load(val($p,'msg'));}*/
	
	//interface
	static function content($p){
		$p['rid']='conn';
		$p['msg']=val($p,'msg',self::ex()); 
		$p['app']='connectors'; $p['mth']='reader';//use local connectors
		$ret=self::edit('msg');
		$ret.=hlpbt('connectors');
		$ret.=tag('textarea',array('id'=>'msg','cols'=>'100%','rows'=>'10','onkeyup'=>'connread()'),$p['msg']);
		//param (app=connectors,mth=reader) will use local connectors instead of default
		$ret.=aj($p['rid'].'|Conn,load|app=connectors,mth=reader|msg',lang('convert'),'btn').br();
		return $ret.div(Conn::load($p),'board',$p['rid']);}
}
?>