<?php

class twitter{
	static $private='6';
	private static $rid='tw';
	
	//js to append to the header of the parent page
	static function injectJs(){
		return 'exs=[]';//reinit the continuous scrolling
	}

	static function headers(){
		self::$rid=randid('plg');
		add_head('jscode','	
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
		if(strncmp($v,'http',4)===0)$ret.=lk($v,$v).' '; 
		elseif(strncmp($v,'@',1)===0)$ret.=lk('https://twitter.com/'.substr($v,1),$v).' '; 
		else $ret.=$v.' ';}
	return str_replace(' && ',br(),$ret);}
	
	private static function images($r){$ret='';
	if(isset($r) && is_array($r))foreach($r as $v)
		if(isset($v['media_url_https']) && isset($v['type'])){
		if($v['type']=='photo')$ret.=img($v['media_url_https']).br();
		elseif($v['type']=='video')$ret.=auto_video($v['media_url_https']).br();}
	return $ret;}
	
	private static function from($q){
	$name=($q['user']['name']);//utf8_decode
	$url='https://twitter.com/'.$q['user']['screen_name'].'/status/'.$q['id'];
	return lk($url,$name,'btn');}
	
	private static function twdate($q){
	$date=strftime('%H:%M - %d %b %Y',strtotime($q['created_at']));
	return tag('span','class=small',$date);}
	
	private static function reply($q){
	if(isset($q['in_reply_to_status_id'])){$id=$q['in_reply_to_status_id'];
	$name=($q['in_reply_to_screen_name']);//utf8_decode
	$label=tag('span',array('class'=>'small'),'reply-to:').' ';
	$url='https://twitter.com/'.$q['in_reply_to_screen_name'].'/status/'.$id;
	$link=lk($url,$name,'btn').' ';
	$thread=aj('popup|twitter,thread|id='.$q['id'],ico('caret-up').'Thread','btn').' ';
	return $label.$link.$thread;}}
	
	private static function favorited($q){
	$txt=isset($q['favorite_count'])?$q['favorite_count']:'0';
	return ico('heart').' '.$txt;}
	
	private static function retweeted($q){
	$txt=isset($q['retweet_count'])?$q['retweet_count']:'0';
	return ico('share').' '.$txt;}
	
	//write
	static function post($p){
	$txt=val($p,'inp2'); 
	$t=new twit(val($p,'twid')); if($t)$res=$t->update($txt); //pr($res);
	if(array_key_exists('errors',$res))$p['error']=$res['errors'][0]['message'];
	if(isset($er))return help('error','alert');
	else return self::build($p);}
	
	//datas
	/*private static function datas($q){
	$ret['name']=$q['name'];
	$ret['date']=strftime('%H:%M %d %b %Y',strtotime($q['created_at']));
	$ret['twit_url']='https://twitter.com/'.$q['user']['screen_name'].'/status/'.$q['id'];
	$ret['reply']=self::reply($q);
	$ret['text']=$q['text'];
	$ret['img']=self::images($q['entities']['media']);
	return $ret;}*/
	
	//read
	static function read($q){
	$id=$q['id'];
	$re['from']=self::from($q).' ';
	$re['date']=self::twdate($q);
	$re['reply']=self::reply($q).' ';
	$re['favs']=self::favorited($q).' '.self::retweeted($q);
	$txt=self::embed_url(($q['text']));//utf8_decode
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
	
	static function thread($p){
		$t=new twit(ses('twid'));
		return self::thread_up($t,val($p,'id'));}
	
	//banner
	static function banner($t,$usr){$show=$t->show($usr); //p($show);
	$img=isset($show['profile_image_url'])?img($show['profile_image_url']).' ':'';
	$txt=isset($show['screen_name'])?lk('https://twitter.com/'.$show['screen_name'],$show['name']):'';
	return div($txt,'btn').br().br();}
	
	//build
	static function build($p){
		$usr=val($p,'usr','philum_cms'); $o=val($p,'o',10);
		ses('twusr',$usr);
		$t=new twit(val($p,'twid'));
		if(is_numeric($usr)){$q=$t->read($usr); $ret=self::read($q);}
		else{$q=$t->user_timeline($usr,$o); $ret=self::read_batch($q);}//self::banner($t,$usr).
		if(array_key_exists('errors',$q))$er=$q['errors'][0]['message']; //pr($q);
		if(isset($er))return help('error','alert').$er;
		return $ret;}
	
	//call from ajax
	static function call($p){
		$id=val($p,'id');//call one twit
		$max=val($p,'max');//continuous scrolling
		$usr=val($p,'inp1');//change user
		//$o=val($p,'opt1','10');//limit
		if($usr)ses('twusr',$usr);//memorize usr for next calls
		
		$t=new twit(val($p,'twid'));
		if(is_numeric($id))$ret=self::read($t->read($id));
		elseif(is_numeric($usr))$ret=self::read($t->read($usr));
		else{
			if(!$usr)$usr=ses('twusr');
			$q=$t->user_timeline($usr,10,$max); //pr($q);//
			if(array_key_exists('errors',$q))$er=$q['errors'][0]['message'];
			if(isset($er))return help('error','alert').$er;
			if($max)array_shift($q);
			$ret=self::read_batch($q);
		}
	return $ret;}
	
	//admin
	private static function edit($p){
		$usr=val($p,'usr'); $twid=val($p,'twid');
		$ret=input('inp2','text to twit');
		$ret.=aj(self::$rid.',,injectJs|twitter,post|usr='.$usr.',twid='.$twid.'|inp2',pic('send'));
		return $ret;}
	
	private static function menu($p){
		$usr=val($p,'usr'); $o=val($p,'o',10);
		$ret=input('inp1',$usr?$usr:'twitter-user');
		$ret.=aj(self::$rid.',,injectJs|twitter,call|twid='.val($p,'twid').'|inp1',ico('eye'));
		return $ret;}
	
	/*static function api(){
		$r=sql('id,owner','twitter','kv',['uid'=>ses('uid')]);
		foreach($r as $k=>$v)
			$tw[]=aj('sndtw|tlxcall,twit|id='.$id.',twid='.$k,ico('twitter',24).$v);
		$ret=span(implode('',$tw),'','sndtw');
		return $ret;}*/
	
	#content
	static function content($p){$bt='';
		$p['usr']=val($p,'user','tlexfr'); $p['o']=val($p,'nb','10');
		$p['twid']=sql('id','twitter','v',['uid'=>ses('uid')]);
		ses('twid',$p['twid']);
		if($p['twid']){
			$bt=self::menu($p).' ';
			if(auth(4))$bt.=self::edit($p);}
		if(ses('uid'))$bt.=aj('popup|admin_twitter',pic('params'));
		$ret=div(self::build($p),'',self::$rid);
		return $bt.br().$ret;}
}
?>
