<?php

class _sql{
	static $private='2';
	private static $db='test';
	
	//create
	static function install($init=0){
		$r=array('ib'=>'int','val'=>'var','val4'=>'var');//support updates
		Sql::create(self::$db,$r,$init);
	}
	
	//insert
	static function insert($r){
		$r=array(1=>'hello',2=>'hey');
		if($r)Sql::insert(self::$db,$r);
	}
	
	//read
	static function read($prm){$ret='';
		$r=Sql::call($prm,self::$db);
		//$ret=Build::table($r);
		$ret=val($_GET,'sql');
		$ret.=pr($r,1);
		return $ret;
	}
	
	#content
	static function content($prm){$ret='';
		$rid=randid('sql');
		//self::install(0);
		$ret.=Ajax::j($rid.',,y|_sql,read|cols=*,mode=','read all','btn');
		$ret.=Ajax::j($rid.',,y|_sql,read|cols=val,mode=k','read mode k','btn');
		$ret.=Ajax::j($rid.',,y|_sql,read|cols=val,mode=v','read mode v','btn');
		$ret.=Ajax::j($rid.',,y|_sql,read|cols=id-ib-val,mode=ra','read mode ra','btn');
		$ret.=Ajax::j($rid.',,y|_sql,read|cols=id-ib-val,mode=rr','read mode rr','btn');
		$ret.=Ajax::j($rid.',,y|_sql,read|cols=val-id,mode=kk','read mode kk','btn');
		$ret.=Ajax::j($rid.',,y|_sql,read|cols=id-ib-val,mode=kkv','read mode kkv','btn');
		$ret.=div('','',$rid);
	return $ret;}
}

?>
