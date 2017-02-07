<?php

/**
* editor of mysql table used for the structure of a desktop menu
* icons are attached to the uid (private icons) and the uid=0 (public icons, edited with auth>6)
*/
class desktop{
	static $private='1';

	static function headers(){
		Head::add('csscode','fieldset, legend{border:0; background:#ddd; width:44%; display:table-cell;}');
		//Head::add('jscode','');
	}

	static function admin(){
		if(ses('uid')){
			$r[]=array('','j','popup|desktop,manage','pencil',lang('manage'));
			return $r;}
	}
	
	static function install(){
		Sql::create('desktop',array('uid'=>'int','dir'=>'var','type'=>'var','com'=>'var','picto'=>'var','bt'=>'var','auth'=>'int'));
	}
	
	//fill sql from existing apps
	static function readapps(){
	    $dirs=Dir::read('app');
		if(is_array($dirs))foreach($dirs as $dir=>$files){
			if(is_array($files) && $dir)foreach($files as $k=>$file){
				if(is_string($file)){$app=before($file,'.');
					if($app)$private=isset($app::$private)?$app::$private:0;
					$dr='/phi/'.$dir;
					if(!$private or ses('auth')>=$private)
						$r=array('uid'=>'0','dir'=>$dr,'type'=>'','com'=>$app,'picto'=>ics($app),'bt'=>$app);
						$nid=Sql::insert('desktop',$r);
				}
			}
		}
	}
	
	static function reload(){
		return aj('page|desktop',lang('reload'),'btn');		
	}
	
	#admin
	//displace
	static function savemdfdr($p){
		$where=auth(6)?' or uid="0"':'';
		$r=Sql::read('id,dir','desktop','rr','where uid="'.ses('uid').'"'.$where);
		if($p['mdfdr'])
		foreach($r as $k=>$v){$vb=str_replace($p['dir'],$p['mdfdr'],$v['dir']);
			if($vb!=$v['dir'])
				Sql::query('update desktop set dir="'.$vb.'" where id="'.$v['id'].'"');}
		return Desk::load('desktop','com',before($p['mdfdr'],'/'));
	}
	static function modifdir($p){$sz=val($p,'sz',8);
		$j=Ajax::js(array('com'=>'div,page,2','app'=>'desktop,savemdfdr','prm'=>'dir='.$p['dir'],'inp'=>'mdfdr'));
		$prm=array('type'=>'text','id'=>'mdfdr','value'=>$p['dir'],'size'=>$sz,'onblur'=>$j);
		$ret=tag('input',$prm,'',1);
		return $ret;
	}
	//rename
	static function savemdfbt($p){
		if(auth(6) && $p['id'])Sql::update('desktop','bt',$p['mdfbt'],$p['id']);
		return Desk::load('desktop','com',$p['dir']);
	}
	static function modifbt($p){
		$r=Sql::read('bt,dir','desktop','ra','where id="'.$p['id'].'"');
		$j=Ajax::js(array('com'=>'div,page,2','app'=>'desktop,savemdfbt','prm'=>'id='.$p['id'].',dir='.$r['dir'],'inp'=>'mdfbt'));
		$prm=array('type'=>'text','id'=>'mdfbt','value'=>$r['bt'],'size'=>8,'onblur'=>$j);
		$ret=tag('input',$prm,'',1);
		return $ret;}
	//del
	static function del($p){
		$nid=Sql::delete('desktop',$p['id']);
		return self::manage($p);
	}
	//update
	static function update($p){
		$keys='dir,type,com,picto,bt'; $r=explode(',',$keys);
		foreach($r as $k=>$v)Sql::update('desktop',$v,$p[$v],$p['id']);
		//return lang('updated').' '.self::reload();
		return self::manage($p);
	}
	
	static function edit($p){$ret='';
		$keys='dir,type,com,picto,bt';
		$r=Sql::read($keys,'desktop','ra','where id="'.$p['id'].'"');
		foreach($r as $k=>$v)$ret.=goodinput($k,$v).' '.label($k,$k).br();
		$ret.=aj('dskmg|desktop,update|id='.$p['id'].'|'.$keys,lang('save'),'btsav');
		$ret.=aj('dskmg|desktop,del|id='.$p['id'],lang('del'),'btdel');
		return div($ret,'','dskdt');
	}
	
	static function save($p){
		$r=Sql::columns('desktop',1);
		foreach($r as $k=>$v)$rb[$k]=val($p,$k);
		$nid=Sql::insert('desktop',$rb);
		if($nid)self::manage($p);
	}
	
	static function add($p){
		$r=Sql::columns('desktop',1);
		$keys=implode(',',array_keys($r)); unset($r['uid']);
		$ret=hidden('uid',ses('uid'));
		foreach($r as $k=>$v)$ret.=input($k,$k,16,1).br();
		$ret.=aj('dskpop|desktop,save||'.$keys,lang('add'),'btn');
		return div($ret,'','dskpop');
	}
	
	//edit on place
	static function mdfbtn($p){
		if($p['col']=='picto')$ico=pic($p['val']).' '; else $ico='';
		return aj($p['cbk'].'|desktop,modif|id='.$p['id'].',col='.$p['col'].',val='.jurl($p['val']).',cbk='.$p['cbk'],$ico.$p['val'],'btn');
	}
	
	static function savemdf($p){$p['val']=$p[$p['idv']];
		Sql::update('desktop',$p['col'],$p['val'],$p['id']);
		return self::mdfbtn($p);
	}
	
	static function modif($p){
		$idv='mdf'.$p['id'].$p['col'];
		//$ret=input($idv,$p['val'],16);
		$r=array('type'=>'text','id'=>$idv,'value'=>$p['val'],'size'=>16);//,'onblur'=>'closeinput(\''.$idv.'\')'
		$ret=tag('input',$r,'',1);
		$ret.=aj('div,'.$p['cbk'].',2|desktop,savemdf|cbk='.$p['cbk'].',id='.$p['id'].',col='.$p['col'].',idv='.$idv.'|'.$idv,lang('ok',1),'btn');
		return $ret;
	}
	
	//manage
	static function manage($p){$ret=''; $ra=''; $dir=val($p,'dir');
		if(isset($p['addrow'])){$r=Sql::columns('desktop',1);
			foreach($r as $k=>$v)$rb[$k]='';
			$rb['uid']=ses('uid'); $rb['dir']=$dir;
			$nid=Sql::insert('desktop',$rb);}
		//$ret.=aj('dskmg|desktop,addline',lang('add'),'btn');
		if(auth(4))$ret=aj('dskmg|desktop,manage|dir='.$dir.',addrow=1',langp('add'),'btn');
		//$ret.=aj('dskmg|desktop,manage|dir='.$dir,langp('refresh'),'btn');
		//$ret.=aj('page|desktop|dir='.$dir,langp('reload'),'btn');
		//$ret.=aj('popup|desktop,readapps|'.langs('reflush,apps'),'btn');
		//$ret.=hlpbt('desktop').br().br();
		//table
		if(auth(4))$keys='id,dir,type,com,picto,bt,auth'; else $keys='id,dir,picto,bt,auth';
		$kr=explode(',',$keys); $n=count($kr);
		if($dir)$wh=' and dir="'.$p['dir'].'"'; else $wh='';
		$r=Sql::read($keys,'desktop','','where uid="'.ses('uid').'" and auth<="'.ses('auth').'" '.$wh.' order by id');
		foreach($r as $k=>$v){
			//$ra[$k][0]=aj('popup|desktop,edit|id='.$v[0],$v[0],'btn');
			for($i=1;$i<$n;$i++){$cbk='inp'.$k.$i;//public can edit $v[6]
				if($kr[$i]=='picto')$ti=pic($v[$i]);
				else $ti=strlen($v[$i])>20?substr($v[$i],0,16).'...':$v[$i];
				if($kr[$i]=='com')$v[$i]=jurl($v[$i]);
				$bt=aj($cbk.'|desktop,modif|dir='.$dir.',id='.$v[0].',col='.$kr[$i].',val='.$v[$i].',cbk='.$cbk,$ti,'btn');
				$ra[$k][]=span($bt,'',$cbk);
			}
			$ra[$k][]=aj('dskmg|desktop,del|dir='.$dir.',id='.$v[0],lang('delete'),'btdel');
		}
		$modes=hlpbt('desktop_modes','mode','btn');
		$icons=aj('popup|fontawesome','icon');
		$auth=hlpbt('desktop_auth','auth','btn');
		if(auth(4))$rk=array('root',$modes,'app',$icons,'button',$auth);
		else $rk=array('root',$icons,'button',$auth);
		if($ra)array_unshift($ra,$rk); else $ra[]=$rk;
		$ret.=Build::table($ra);
		return div($ret,'','dskmg');
	}
	
	//$r[]=array('dir','//j/in/lk','app','method','icon');
	static function com(){
		//$r=Sql::columns('desktop',1); unset($r['uid']); $keys=implode(',',array_keys($r));
		$keys='id,dir,type,com,picto,bt,auth';
		return Sql::read($keys,'desktop','id','where uid="'.ses('uid').'" or auth="0" order by dir');
	}
	
	//content
	static function content($p){$ret='';
		//self::install();
		$ret=Desk::load('desktop','com',val($p,'dir'));
		if(val($p,'dir') && !$ret)$ret=Desk::load('desktop','com','');
		return $ret;
	}

}

?>