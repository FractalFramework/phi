<?php

class tlxapp{
	static $private='0';
	
	//interface with other Apps
	static function com(){
	}
	
	//apps
	static function addapp($p){$app=val($p,'app');
		//$ret=input_label('inp'.$app,'',$app);
		//$ret=aj('tlxapps|telex,menuapps',pic('close'),'btn');
		$ret=btj(pic('close'),'closediv(\'tlxapps\')','btn right');
		switch($app){
			case('telex'):$ret=input_label('inp'.$app,'',$app); break;
		}
		return $ret;}
	
	//keep
	static function keepsave($p){//dir,type,com,picto,bt
		$id=val($p,'id'); $com=val($p,'com'); $d=val($p,'p1'); $o=val($p,'p2'); $t=val($p,'tit');
		if($com=='img'){$com='imgup|Img::read|f=img/telex/full/'.$d; $ic='image';}
		if($com=='web'){$com='pagup|telex,objplayer|obj=playweb,p1='.$d.',p2='.$o; $ic='external-link';}
		if($com=='video'){$com='pagup|Video,call|p='.$d.',id='.$id; $ic='video';}
		if($com=='chat'){$com='pagup,,,1|chat|param='.$d.',headers=1'; $ic='chat';}
		if($com=='article'){$com='pagup|article,read|popwidth:550px,tlx=1,id='.$d; $ic='document';}
		if($com=='gps'){$com='pagup|map,com|coords='.$d; $ic='map';}
		$nid=Sql::insert('desktop',[ses('uid'),'/documents','j',$com,$ic,$t,1]);
		return div(lang('added to desktop'),'pane');
	}
	
	static function keep($p){
		$id=val($p,'id'); $idv=val($p,'idv'); $com=val($p,'conn'); $ret=''; $ex=''; $txt='';
		//if($conn)$ex=Sql::read('id','desktop','v','where id='.ses('uid').' and com="'.$com.'"');
		//if($ex)return lang('already exists');
		$dir=val($p,'dir'); $pic=val($p,'pic'); $bt=val($p,'bt'); $auth=val($p,'auth');
		if(!$dir){
			if($id)$txt=Sql::read('txt','telex_xt','v','where id='.$id);
			telex::$objects='';
			if($txt)$msg=Conn::load(['msg'=>$txt,'app'=>'telex','mth'=>'reader']); $r=telex::$objects;
			if($r)foreach($r as $kr=>$vr)foreach($vr as $k=>$v){$im=''; $pic=''; $t=''; list($p,$o)=$v;
			switch($kr){
			case('img'):$im=telex::thumb($p,'micro'); break;
			case('video'):$rt=telex::playmetas($p); $t=isset($rt[0])?$rt[0]:nohttp($p);
				if(isset($rt[2]))$im=telex::thumb($rt[2],'micro'); $pic='youtube'; break;
			case('web'):$rt=telex::playmetas($p); $t=isset($rt[0])?$rt[0]:nohttp($p);
				if(isset($rt[2]))$im=telex::thumb($rt[2],'micro'); $pic='newspaper-o';  break;
			case('chat'):$rb=Sql::read_inner('name','chatlist','login','ruid','rv','where roid='.$p);
				if($rb)$t=lang('with').' '.implode(', ',$rb); else $t='#'.$p; $pic='wechat'; break;
			case('article'):$t=Sql::read('tit','articles','v','where id='.$p);
				$pic='file-text-o'; break;
			case('gps'):$t=$t=Gps::com(['coords'=>$p]); $pic='map-marker'; break;
			}
			if($pic)$pic=pic($pic,24); if($im)$im=img('/'.$im,45); $bt=($im?$im:$pic).$t;
			$ret.=pagup('tlxapp,keepsave|com='.$kr.',p1='.nohttp($p).',p2='.$o.',tit='.$t,$bt,'btn');}
			//p(telex::$objects);
		}
		return div(lang('add2desktop'),'btit').div($ret,'bloc_content objects');}

}
?>
