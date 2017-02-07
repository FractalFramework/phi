<?php

class App{
	static $alx=[];

	static function open($app,$p=''){$ret='';
		//when calling not by ajax
		if(isset($p['prm']))$p=_jrb($p['prm']);
		if(!is_array($p) && strpos($p,'{')!==false)$p=json_decode($p,true);
		if(!isset($app))return lang('no app loaded');
		//default method
		if(isset($p['appMethod']) && $p['appMethod'])$mth=$p['appMethod']; else $mth='content';
		//load
		if(method_exists($app,$mth)){
			$private=isset($app::$private)?$app::$private:0;
			if($private<2 or ses('auth')>=$private)$ret.=$app::$mth($p);
			else $ret='need auth '.$private;
			//when param=array('headers'=>'1')
			if(method_exists($app,'headers')){
				if(!array_key_exists($app,self::$alx))
					if(!get('appName') or isset($p['headers']))
						$app::headers();
				self::$alx[$app]=1;
			}
		}
		return $ret;
	}

}

?>