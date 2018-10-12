<?php
//https://github.com/tfairane/twitterAPI
class twit{
private $urlParams;
private $_max;
private $_count;
private $_DST;
private $_follow;
private $_method;
private $_oAuth_consumer_key;
private $_oAuth_consumer_secret;
private $_oAuth_nonce;
private $_oAuth_signature;
private $_oAuth_signature_method;
private $_oAuth_timestamp;
private $_oAuth_token;
private $_oAuth_token_secret;
private $_oAuth_version;
private $_parameter_string;
private $_query;
private $_signature_base_string;
private $_signing_key;
private $_url;
private $_user;
private $_id;

public function __construct($id=''){if(!$id)$id=2;
	/*require('cnfg/twitter_oAuth.php');//oAuth logins
	$this->_oAuth_token = $oAuth_token;
	$this->_oAuth_token_secret = $oAuth_token_secret;
	$this->_oAuth_consumer_key = $oAuth_consumer_key;
	$this->_oAuth_consumer_secret = $oAuth_consumer_secret;*/
	
	$cols='consumer_key,consumer_secret,token_key,token_secret';
	$r=sql($cols,'twitter','ra',$id); //pr($r);
	if($r){
		$this->_oAuth_token=$r['token_key'];
		$this->_oAuth_token_secret=$r['token_secret'];
		$this->_oAuth_consumer_key=$r['consumer_key'];
		$this->_oAuth_consumer_secret=$r['consumer_secret'];}
		
	$this->_oAuth_nonce=md5(rand());
	$this->_oAuth_signature_method='HMAC-SHA1';
	$this->_oAuth_timestamp=time();
	$this->_oAuth_version='1.0';
}

// build url from known Array
private function urlParams(){
	return array(
		'oAuth_consumer_key'=>$this->_oAuth_consumer_key,
		'oAuth_nonce'=>$this->_oAuth_nonce,
		'oAuth_signature'=>$this->_oAuth_signature,
		'oAuth_signature_method'=>$this->_oAuth_signature_method,
		'oAuth_timestamp'=>$this->_oAuth_timestamp,
		'oAuth_token'=>$this->_oAuth_token,
		'oAuth_version'=>$this->_oAuth_version
	);
}

private function buildUrlParams(){$ret='';
	$r=$this->urlParams(); unset($r['oAuth_signature']);
	foreach($r as $k=>$v)$rt[]=$k.'='.rawurlencode($v);
return implode('&',$rt);}

private function buildUrlArray(){$ret='';
	$r=$this->urlParams();
	foreach($r as $k=>$v)$rt[]=$k.'="'.rawurlencode($v).'"';
return implode(',',$rt);}

//used to open the websarvice of twitter
private function send($url,$postfields){
	$session=curl_init();
	curl_setopt($session,CURLOPT_URL,$url);
	curl_setopt($session,CURLOPT_HTTPHEADER,$this->_DST);
	if($postfields){
		curl_setopt($session,CURLOPT_POST,TRUE);
		curl_setopt($session,CURLOPT_POSTFIELDS,$postfields);}
	curl_setopt($session,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($session,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($session,CURLOPT_RETURNTRANSFER,1);
	$ret=json_decode(curl_exec($session),true);
return $ret;}

//publish a tweet
public function update($tweet){
	$this->_method='POST';
	$this->_query='status='.rawurlencode($tweet);
	$this->_url='https://api.twitter.com/1.1/statuses/update.json';
	$this->_parameter_string=$this->buildUrlParams().'&'.$this->_query;
	$this->gen();
	return $this->send($this->_url,$this->_query);
}

//follow an user
public function follow($id){
	$this->_method='POST';
	$this->_query='user_id='.rawurlencode($id);
	$this->_follow='follow=true';
	$this->_url='https://api.twitter.com/1.1/friendships/create.json';
	$this->_parameter_string=$this->_follow .'&'.$this->buildUrlParams().'&'.$this->_query;
	$this->gen();
	return $this->send($this->_url,$this->_follow.'&'.$this->_query);
}

//read timeline
public function user_timeline($user,$count,$max=''){
	$this->_method='GET';
	$this->_user='screen_name='.rawurlencode($user);
	$this->_count='count='.rawurlencode($count).'&include_rts=1';
	$this->_count.=$max?'&max_id='.rawurlencode($max):'';
	$this->_url='https://api.twitter.com/1.1/statuses/user_timeline.json';
	$this->_parameter_string=$this->_count.'&'.$this->buildUrlParams().'&'.$this->_user;
	$this->gen();
	return $this->send($this->_url.'?'.$this->_user.'&'.$this->_count,'');
}

//user infos
public function show($user){
	$this->_method='GET';
	$this->_user='screen_name='.rawurlencode($user);
	$this->_url='https://api.twitter.com/1.1/users/show.json';
	$this->_parameter_string=$this->buildUrlParams().'&'.$this->_user;
	$this->gen();
	return $this->send($this->_url.'?'.$this->_user,'');
}

//account infos
public function read($id){
	$this->_method='GET';
	//$this->_id='id='.rawurlencode($id);
	$this->_url='https://api.twitter.com/1.1/statuses/show/'.$id.'.json';
	$this->_parameter_string=$this->buildUrlParams();
	$this->gen();
	return $this->send($this->_url,'');
}

//replies
public function replies($id){
	$this->_method='GET';
	//$this->_id='id='.rawurlencode($id);
	$this->_url='https://api.twitter.com/1/related_results/show/'.$id.'.json?include_entities=1';
	$this->_parameter_string=$this->buildUrlParams();
	$this->gen();
	return $this->send($this->_url,'');
}

//Oauth signatures
private function gen(){
	$this->_signature_base_string=rawurlencode($this->_method).'&'.rawurlencode($this->_url).'&'.rawurlencode($this->_parameter_string);
	$this->_signing_key=rawurlencode($this->_oAuth_consumer_secret).'&'.rawurlencode($this->_oAuth_token_secret);
	$this->_oAuth_signature=base64_encode(hash_hmac('SHA1',$this->_signature_base_string,$this->_signing_key,TRUE));
	$this->_DST=array('authorization: Oauth '.$this->buildUrlArray());
}
}
?>