<?php

class twitter{
	static $private='6';
	private static $rid;
	
	//js to append to the header of the parent page
	static function injectJs(){
		return 'exs=[]';//reinit the continuous scrolling
	}

	static function headers(){
		self::$rid=randid('plg');
		Head::add('jscode','	
		var exs=[];
		var call="after,'.self::$rid.'|twitter,call";
		var params=[];
		//params.push("headers=1");
		function twlive(e){
			var scrl=pageYOffset+innerHeight;
			var mnu=getbyid("'.self::$rid.'").getElementsByTagName("section");
			var load=mnu[mnu.length-4];
			var pos=getPositionRelative(load);
			var last=mnu[mnu.length-1];
			var id=last.id;
			var idx=exs.indexOf(id);
			if(idx==-1 && scrl>pos.y){exs.push(id);
				ajaxCall(call,\'max=\'+id,\'\');
			}}
		addEvent(document,"scroll",function(event){twlive(event)});');
	}

	private static function embed_url($d){$ret='';
	$d=str_replace("\n",' && ',$d); $r=explode(' ',$d);
	foreach($r as $v){
		if(strncmp($v,'http',4)===0)$ret.=href($v,$v).' '; 
		elseif(strncmp($v,'@',1)===0)$ret.=href('https://twitter.com/'.substr($v,1),$v).' '; 
		else $ret.=$v.' ';}
	return str_replace(' && ',br(),$ret);}
	
	private static function images($r){$ret='';
	if(isset($r) && is_array($r))foreach($r as $v)
		if(isset($v['media_url_https']) && isset($v['type'])){
		if($v['type']=='photo')$ret.=img($v['media_url_https']).br();
		elseif($v['type']=='video')$ret.=auto_video($v['media_url_https']).br();}
	return $ret;}
	
	private static function from($q){
	$name=utf8_decode($q['user']['name']);
	$url='https://twitter.com/'.$q['user']['screen_name'].'/status/'.$q['id'];
	return href($url,$name,'btn');}
	
	private static function twdate($q){
	$date=strftime('%H:%M - %d %b %Y',strtotime($q['created_at']));
	return tag('span','class=small',$date);}
	
	private static function reply($q){
	if(isset($q['in_reply_to_status_id'])){$id=$q['in_reply_to_status_id'];
	$name=utf8_decode($q['in_reply_to_screen_name']);
	$label=tag('span',array('class'=>'small'),'reply-to:').' ';
	$url='https://twitter.com/'.$q['in_reply_to_screen_name'].'/status/'.$id;
	$link=href($url,$name,'btn').' ';
	$thread=Ajax::j('popup|twitter,thread|id='.$q['id'],pic('caret-up').'Thread','btn').' ';
	return $label.$link.$thread;}}
	
	private static function favorited($q){
	$txt=isset($q['favorite_count'])?$q['favorite_count']:'0';
	return pic('heart').' '.$txt;}
	
	private static function retweeted($q){
	$txt=isset($q['retweet_count'])?$q['retweet_count']:'0';
	return pic('share').' '.$txt;}
	
	//write
	static function post($prm){
	$t=val($prm,'inp2'); 
	$t=new Twit;
	//if($t)$t->update($t);
	return self::build(val($prm,'p'),val($prm,'o'));}
	
	//datas
	/*private static function datas($q){
	$ret['name']=utf8_decode($q['name']);
	$ret['date']=strftime('%H:%M %d %b %Y',strtotime($q['created_at']));
	$ret['twit_url']='https://twitter.com/'.$q['user']['screen_name'].'/status/'.$q['id'];
	$ret['reply']=self::reply($q);
	$ret['text']=utf8_decode($q['text']);
	$ret['img']=self::images($q['entities']['media']);
	return $ret;}*/
	
	//read
	static function read($q){
	$id=$q['id'];
	$re['from']=self::from($q).' ';
	$re['date']=self::twdate($q);
	$re['reply']=self::reply($q).' ';
	$re['favs']=self::favorited($q).' '.self::retweeted($q);
	$txt=self::embed_url(utf8_decode($q['text']));
	$re['text']=div($txt,'track');//html_entity_decode()
	if(isset($q['entities']['media']))$re['img']=self::images($q['entities']['media']);
	return tag('section',array('id'=>$q['id']),implode('',$re));}
	
	static function read_batch($r){$ret='';
	if(is_array($r))foreach($r as $q)
		if(isset($q['id']))$ret.=self::read($q);
	return $ret;}
	
	//thread
	private static function thread_up($t,$p){
		$q=$t->read($p); $ret='';
		if(isset($q['in_reply_to_status_id']))
			$ret=self::thread_up($t,$q['in_reply_to_status_id']);
		if(isset($q['id']))$ret.=self::read($q);
	return $ret;}
	
	static function thread($prm){$t=new Twit; $p=val($prm,'id');
	return self::thread_up($t,$p);}
	
	//call from ajax
	static function call($prm){
		$id=val($prm,'id');//call one twit
		$max=val($prm,'max');//continuous scrolling
		$p=val($prm,'inp1');//change user
		//$o=val($prm,'opt1','10');//limit
		if($p)ses('twusr',$p);//memorize p for next calls
		$t=new Twit;
		if(is_numeric($id))$ret=self::read($t->read($id));
		elseif(is_numeric($p))$ret=self::read($t->read($p));
		else{
			if(!$p)$p=ses('twusr');
			$q=$t->user_timeline($p,10,$max);
			if($max)array_shift($q);
			$ret=self::read_batch($q);
		}
	return $ret;}
	
	//banner
	static function banner($t){$show=$t->show($p); //p($show);
	$img=isset($show['profile_image_url'])?img($show['profile_image_url']).' ':'';
	$txt=isset($show['screen_name'])?href('https://twitter.com/'.$show['screen_name'],$show['name']):'';
	return div($txt,'btn').br().br();}
	
	//build
	static function build($p,$o,$res=''){
		$p=isset($p)?$p:'philum_cms'; $o=$o?$o:10;
		ses('twusr',$p);
		$t=new Twit;
		if(is_numeric($p))$ret=self::read($t->read($p));
		else $ret=self::read_batch($t->user_timeline($p,$o));//self::banner($t).
	return $ret;}
	
	//interface
	private static function edit($p,$o){
	$ret=input('inp2','text to twit');
	$ret.=Ajax::j('div,'.self::$rid.',,injectJs|twitter,post|p='.$p.',o='.$o.'|inp2',pic('check'));
	return $ret;}
	
	private static function menu($p,$o){
	$ret=input('inp1',$p?$p:'twitter-user');
	//$ret.=input('opt1',$o?$o:'number of twits');
	$ret.=Ajax::j('div,'.self::$rid.',,injectJs|twitter,call||inp1',pic('check'));
	return $ret;}
	
	#content
	static function content($prm){
		$p=val($prm,'user','SocialGov_');
		$o=val($prm,'nb','10');
		$bt=self::menu($p,$o).' ';
		if(ses('auth')>4)$bt.=self::edit($p,$o);
		$ret=div(self::build($p,$o),'',self::$rid);
	return $bt.br().$ret;}
}
?>
