<?php

class Desk{
	
	#sample
	static function menus(){
		//array('folder','pop/lk','action','picto','text')
		$r[]=array('/menu1','j','popup|txt','text','textpad');
		return $r;
	}
	
	#call //$prm=structure
	static function call($prm){$ret='';
		//$dir=ses('dskdr',$dir);
		$dir=val($prm,'dir'); $app=val($prm,'app'); $mth=val($prm,'mth'); $rid=val($prm,'rid');
		$dsp=ses('dskdsp',val($prm,'display'));
		if($dsp=='list'){$css='licon'; $sz=24;}else{$css='cicon'; $sz=32;}
		$auth=auth(6);
		$rdir=explode('/',$dir);
		$current_depht=substr_count($dir,'/');
		if(array_key_exists($current_depht,$rdir))
			$current_level=$rdir[$current_depht];
		$auth=ses('auth')?ses('auth'):0;
		//load
		if($app && $mth)$r=$app::$mth();
		if(isset($r))foreach($r as $k=>$v){
			//$auth=ses('uid')==$v[5]?1:$auth;
			$level=explode('/',$v[0]); $depht=count($level)-1;
			//next_level: 1/[2]/3
			if(array_key_exists($current_depht+1,$level))
				$next_level=$level[$current_depht+1];
			else $next_level='';
			$private=class_exists($v[2]) && isset($v[2]::$private)?$v[2]::$private:0;
			if($auth>=$private){
				if($v[0]==$dir){
					$bt=pic($v[3],$sz).span($v[4]);//thumb
					//if($v[1]=='img')$bt=telex::playthumb($v[2],'micro',1);
					if($v[1]=='' && class_exists($v[2])){
						$bt=span(pic($v[3],$sz).span($v[4]),$css);
						$bt=aj('popup,,,1|'.$v[2].'|headers=1',$bt);
						//if($auth)$bt.=span(aj($k.'|desktop,modifbt|id='.$k,$v[4]),'',$k);
						//else $bt.=span($v[4]);
						$ret[]=$bt;}
					elseif($v[1]=='j')$ret[]=Ajax::j($v[2],$bt,$css);
					elseif($v[1]=='pop')$ret[]=popup($v[2].',headers=1',$bt,$css);
					elseif($v[1]=='pag')$ret[]=pagup($v[2].',headers=1',$bt,$css);
					//elseif($v[1]=='img')$ret[]=imgup('img/full/'.$v[2],$bt,$css);
					elseif($v[1]=='img')$ret[]=telex::playthumb($v[2],'micro','',$css);
					elseif($v[1]=='in')$ret[]=br().App::open($v[2],$v[3]);
					elseif($v[1]=='lk')$ret[]=href('/app'.$v[2],$bt,$css,'',1);
				}
				elseif(substr($v[0],0,strlen($dir))==$dir && $depht>$current_depht){
					//$bt=pic('folder',$sz).div($next_level);
					//can use popup instead of div
					$bt=span(pic('folder',$sz).span($next_level),$css);
					$bt=aj('div,'.$rid.',2|Desk,call|dir='.$dir.'/'.$next_level.',app='.$app.',mth='.$mth.',rid='.$rid.',title='.$dir.'/'.$next_level,$bt);
					//if($auth)$bt.=span(aj($k.'|desktop,modifdir',
					//'dir='.$dir.'/'.$next_level,'',$next_level),'',$k);
					//else $bt.=span($next_level);
					$ret[$next_level]=$bt;
				}
			}
		}
		//nav
		$dr=''; $back=''; $edit='';
		if($rdir)foreach($rdir as $k=>$v){
			if($v)$dr.='/'.$v; else $v='/';
			$back.=aj('div,'.$rid.',2|Desk,call|dir='.$dr.',app='.$app.',mth='.$mth.',rid='.$rid.',title='.$dr.'/',$v,'btn');}
		//edit
		$prm='dir='.$dir.',app='.$app.',mth='.$mth.',rid='.$rid.'';
		$edit=aj('div,'.$rid.',y|Desk,call|'.$prm.',display=grid',pic('th-large'),'');
		$edit.=aj('div,'.$rid.',y|Desk,call|'.$prm.',display=list',pic('list'),'');
		if(ses('uid'))$edit.=aj('popup|desktop,manage|dir='.$dir,langpi('edit'),'');
		//$edit.=href('/app/'.$app.'/dir:'.$dir,pic('link'),'btn');
		$edit=span($edit,'right');
		if($ret)return div($edit.$back).implode('',$ret);
	}
	
	static function load($app,$mth,$dir=''){$rid=randid('dsk');
		$prm=array('dir'=>$dir,'app'=>$app,'mth'=>$mth,'rid'=>$rid);
		return div(self::call($prm),'desktop',$rid);
	}

}
?>