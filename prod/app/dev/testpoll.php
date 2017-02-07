<?php

class testpoll{
	static $private='4';
	
	static function r($na,$nb){
		for($i=0;$i<$na;$i++){
			$position=rand(1,2);
			$ra[$i]=$position;
			$choice=rand(1,2);
			$votes=rand(1,$nb);
			$rb[$i][$choice]=$votes;
			$rc[]=array($i,$position,$choice,$votes);
		}
		return array($ra,$rb,$rc);
	}

	static function build($prm){$ret='';
		list($ra,$rb,$rc)=self::r($prm['nbargs'],$prm['nbvotesmax']);
		$res=vote::algo($ra,$rb);
		$ret=Build::table($res).br();
		$ret.=Build::table($rc);
		return $ret;
	}

	static function content($prm){$ret='';
		$prm['nbargs']='50'; $prm['nbvotesmax']='50';
		$bt=input('nbargs',50).input('nbvotesmax',50);
		$bt.=Ajax::j('testcnt|testpoll,build||nbargs,nbvotesmax','ok','btn');
		$ret=self::build($prm);
		return $bt.div($ret,'','testcnt');
	}
	
}

?>