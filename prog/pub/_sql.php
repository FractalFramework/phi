<?php

class _sql{
	static $private='2';
	private static $db='test';
	
	//create
	static function install(){
		$r=array('ib'=>'int','val'=>'var','val4'=>'var');//support updates
		sqlcreate(self::$db,$r,'');
	}
	
	//insert
	static function insert($r){
		$r=array(1=>'hello',2=>'hey');
		if($r)sqlsav(self::$db,$r);
	}
	
	//read
	static function read($prm){$ret='';
		$r=sqlcall($prm,self::$db);
		//$ret=mktable($r);
		$ret=val($_GET,'sql');
		$ret.=pr($r,1);
		return $ret;
	}
	
	#content
	static function content($prm){$ret='';
		$rid=randid('sql');
		self::install();
		$ret.=aj($rid.',,y|_sql,read|cols=*,mode=','read all','btn');
		$ret.=aj($rid.',,y|_sql,read|cols=val,mode=k','read mode k','btn');
		$ret.=aj($rid.',,y|_sql,read|cols=val,mode=v','read mode v','btn');
		$ret.=aj($rid.',,y|_sql,read|cols=id-ib-val,mode=ra','read mode ra','btn');
		$ret.=aj($rid.',,y|_sql,read|cols=id-ib-val,mode=rr','read mode rr','btn');
		$ret.=aj($rid.',,y|_sql,read|cols=val-id,mode=kk','read mode kk','btn');
		$ret.=aj($rid.',,y|_sql,read|cols=id-ib-val,mode=kkv','read mode kkv','btn');
		$ret.=div('','',$rid);
	return $ret;}
}

?>
