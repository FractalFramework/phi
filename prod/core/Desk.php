<?php
class desk{	
#sample
static function menus(){
//array('folder','pop/lk','action','picto','text')
$r[]=array('/menu1','j','popup|txt','text','textpad');
return $r;}

#call //$p=structure
static function call($p){$ret='';
$dir=val($p,'dir'); $app=val($p,'app'); $mth=val($p,'mth'); $rid=val($p,'rid');
$dsp=ses('dskdsp',val($p,'display'));
if($dsp=='list'){$css='licon'; $sz=24;} else{$css='cicon'; $sz=32;}
//$auth=auth(6);
$rdir=explode('/',$dir);
$current_depht=substr_count($dir,'/');
if(array_key_exists($current_depht,$rdir))
	$current_level=$rdir[$current_depht];
$auth=ses('auth')?ses('auth'):0;
//load
if($app && $mth)$r=$app::$mth();
if(isset($r))foreach($r as $k=>$v){
	$level=explode('/',$v[0]); $depht=count($level)-1;
	//next_level: 1/[2]/3
	if(array_key_exists($current_depht+1,$level))
		$next_level=$level[$current_depht+1];
	else $next_level='';
	$private=class_exists($v[2]) && isset($v[2]::$private)?$v[2]::$private:0;
	if($auth>=$private){$ok=0;
		if($v[0]==$dir){
			if(icon_ex($v[4]))$ico=ics($v[4],$sz); else $ico=ico($v[3],$sz);
			$btn=span($v[4]);
			//$rap=atr(after($v[2],'|')); $id=$rap['id'];
			//if(isset($id)){$app=before($v[2],',');
				//if(class_exists($app))$ok=sql('uid',$app::$db,'v',$id);}
			if($ok)$btn=span(aj($k.'|desktop,modifbt|dir=/'.$current_level.',id='.$k.'|mdfbt',$v[4],'btxt'),'',$k);//modifbt
			//if($v[1]=='img')$bt=tlex::playthumb($v[2],'micro',1);//thumb
			if($v[1]=='' && class_exists($v[2])){
				//$bt=ico($ico,$sz).span($v[4]);//.span($v[4])
				$ret[]=span(popup($v[2].'|headers=1',$ico).$btn,$css);}
			elseif($v[1]=='j')$ret[]=span(aj($v[2],$ico).$btn,$css);
			elseif($v[1]=='pop')$ret[]=span(popup($v[2].',headers=1',$ico).$btn,$css);
			elseif($v[1]=='pag')$ret[]=span(pagup($v[2].',headers=1',$ico).$btn,$css);
			//elseif($v[1]=='img')$ret[]=imgup('img/full/'.$v[2],$ico,$css);
			elseif($v[1]=='img')$ret[]=div(tlex::playthumb($v[2],'micro').$btn,$css);
			elseif($v[1]=='audio')$ret[]=span(popup('tlex,objplayer|obj=reader,p1='.$v[2],$ico).$btn,$css);
			elseif($v[1]=='video')$ret[]=span(pogup('tlex,objplayer|obj=reader,p1='.$v[2],$ico).$btn,$css);
			elseif($v[1]=='in')$ret[]=br().app($v[2],$v[3]);
			elseif($v[1]=='lk')$ret[]=lk('/app'.$v[2],$ico,$css,1);}
		elseif(substr($v[0],0,strlen($dir))==$dir && $depht>$current_depht){//dir
			//can use popup instead of div
			$btd=span($next_level);//pic('edit',12)
			if($ok)$btd=span(aj($k.'|desktop,modifdir|dir='.$dir.'/'.$next_level,$next_level,'btxt'),'',$k);
			$bt=span(aj('div,'.$rid.',2|desk,call|dir='.$dir.'/'.$next_level.',app='.$app.',mth='.$mth.',rid='.$rid.',title='.$dir.'/'.$next_level,ico('folder',$sz)).$btd,$css);
			//else $bt.=span($next_level);
			$ret[$next_level]=$bt;}}}
//nav
$dr=''; $back=''; $edit=''; $n=count($rdir);
if($rdir)foreach($rdir as $k=>$v){
	if($v)$dr.='/'.$v; else $v='/';
	$back.=aj('div,'.$rid.',2|desk,call|dir='.$dr.',app='.$app.',mth='.$mth.',rid='.$rid.',title='.$dr.'/',$v,$k==$n-1?'btok':'btoff');}
//edit
$prm='dir='.$dir.',app='.$app.',mth='.$mth.',rid='.$rid.'';
$edit=aj('div,'.$rid.',y|desk,call|'.$prm.',display=grid',ico('th-large'),'');
$edit.=aj('div,'.$rid.',y|desk,call|'.$prm.',display=list',ico('list'),'');
if(ses('uid'))$edit.=aj('popup|desktop,manage|dir='.$dir,langpi('edit'),'');
$edit.=lk('/'.$app.'/dir:'.substr($dir,1),ico('link'),'btn');
$edit=span($edit,'right');
if($ret)return div($edit.$back).implode('',$ret);}
//elseif($dir){$p['dir']=before($dir,'/'); return self::call($p);}

static function load($app,$mth,$dir=''){$rid=randid('dsk');
$p=['dir'=>$dir,'app'=>$app,'mth'=>$mth,'rid'=>$rid];
return div(self::call($p),'',$rid);}
}