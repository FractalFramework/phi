<?php
//https://github.com/tfairane/TwitterAPI
class Twit{
	private $urlParams;
	private $_max;
	private $_count;
	private $_DST;
	private $_follow;
	private $_method;
	private $_oauth_consumer_key;
	private $_oauth_consumer_secret;
	private $_oauth_nonce;
	private $_oauth_signature;
	private $_oauth_signature_method;
	private $_oauth_timestamp;
	private $_oauth_token;
	private $_oauth_token_secret;
	private $_oauth_version;
	private $_parameter_string;
	private $_query;
	private $_signature_base_string;
	private $_signing_key;
	private $_url;
	private $_user;
	private $_id;
	
	public function __construct($id=''){if(!$id)$id=2;
		/*require('cnfg/twitter_oAuth.php');//oAuth logins
		$this->_oauth_token = $oauth_token;
		$this->_oauth_token_secret = $oauth_token_secret;
		$this->_oauth_consumer_key = $oauth_consumer_key;
		$this->_oauth_consumer_secret = $oauth_consumer_secret;*/
		
		$cols='consumer_key,consumer_secret,token_key,token_secret';
		$r=Sql::read($cols,'admin_twitter','ra',['id'=>$id]); //pr($r);
		if($r){
			$this->_oauth_token=$r['token_key'];
			$this->_oauth_token_secret=$r['token_secret'];
			$this->_oauth_consumer_key=$r['consumer_key'];
			$this->_oauth_consumer_secret=$r['consumer_secret'];}
			
		$this->_oauth_nonce=md5(rand());
		$this->_oauth_signature_method='HMAC-SHA1';
		$this->_oauth_timestamp=time();
		$this->_oauth_version='1.0';
	}
	
	// build url from known Array
	private function urlParams(){
		return array(
			'oauth_consumer_key'=>$this->_oauth_consumer_key,
			'oauth_nonce'=>$this->_oauth_nonce,
			'oauth_signature'=>$this->_oauth_signature,
			'oauth_signature_method'=>$this->_oauth_signature_method,
			'oauth_timestamp'=>$this->_oauth_timestamp,
			'oauth_token'=>$this->_oauth_token,
			'oauth_version'=>$this->_oauth_version
		);
	}
	
	private function buildUrlParams(){$ret='';
		$r=$this->urlParams(); unset($r['oauth_signature']);
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
	
	//OAuth signatures
	private function gen(){
		$this->_signature_base_string=rawurlencode($this->_method).'&'.rawurlencode($this->_url).'&'.rawurlencode($this->_parameter_string);
		$this->_signing_key=rawurlencode($this->_oauth_consumer_secret).'&'.rawurlencode($this->_oauth_token_secret);
		$this->_oauth_signature=base64_encode(hash_hmac('SHA1',$this->_signature_base_string,$this->_signing_key,TRUE));
		$this->_DST=array('Authorization: OAuth '.$this->buildUrlArray());
	}
}
?>