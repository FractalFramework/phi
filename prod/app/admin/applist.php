<?php

class applist{
	public $ret;
	
	static function headers(){
		Head::add('csscode','
		.block{border:1px solid grey; border-radius:2px; background:white;
		padding:10px; margin:10px 0;}
		.block a:hover{text-decoration:underline;}
		.block span{display:block; cursor:auto;}
		.block span:hover{background:white;}
		.block div{}
		');
	}
	
	static function appdir($dir,$files){$ret='';
		$title=tag('div',array(),$dir);
		//popup
		$prm['com']='popup,,,injectJs';//append js to headers
		//$prm['class']='btn';
		//batch
		foreach($files as $k=>$v){
			if(!is_array($v))list($app,$ext)=explode('.',$v);
			$private=isset($app::$private)?$app::$private:0;
			if(!$private or ses('auth')>=$private){
			$prm['app']=$app;
			$prm['prm']='headers=1';//load headers
			//$pictoPop=pic('window-restore');
			//$pictoUrl=pic('chevron-right');
			$js=Ajax::js($prm);
			$ret.=tag('a',array('onclick'=>$js),div(langp($app),'btn'));
			//$link=tag('a',array('href'=>'/'.$app,'target'=>'_blank'),$app);
			//$ret.=tag('span',array('class'=>'btn'),$popup.' '.$link);
			}
		}
		if($ret)return div($title,'block').$ret;
	}
	
	static function content($prm){$ret='';
		if(isset($prm['iframe']))$mod=$prm['iframe'];
		else $mod=get('app');	
	    $r=Dir::read(ses('dev').'/app');
		if(isset($r)){
			foreach($r as $k=>$v){
				if(is_array($v))$ret.=self::appdir($k,$v);
				else $rb[$k]=$v;
			}
		}
		if(isset($rb))$first=self::appdir('root',$rb);
		else $first='';
		return $first.$ret;
	}
	
}

?>