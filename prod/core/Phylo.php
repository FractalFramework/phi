<?php
class Phylo{
static function read($datas,$structure){$ret='';
	foreach($structure as $k=>$v){
		if(is_array($v))$ret.=div(self::read($datas,$v),'',$k);
		elseif(array_key_exists($v,$datas)){$va=$datas[$v];
			if(is_array($va)){
				if(!is_numeric($k))$ret.=div(implode('',$va),$k);
				else $ret.=implode('',$va);}
			elseif(!is_numeric($k))$ret.=div($va,$k);
			else $ret.=$va;}}
	return $ret;}
}
?>