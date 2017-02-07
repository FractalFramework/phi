<?php

class article{
	static $private='0';
	static $db='articles';
	
	//install
	static function install(){
		$r=array('uid'=>'int','tit'=>'var','txt'=>'text','pub'=>'int');
		Sql::create('articles',$r);}
	
	static function injectJs(){
		return 'function format(p,o){document.execCommand(p,false,o?o:null);}
		function fontsize(n){var txt=document.getSelection(); alert(txt);}';}
	
	static function headers(){
		Head::add('csscode','.wrapper{max-width:600px; margin:0 auto;}');
		Head::add('jscode',self::injectJs());}
	
	static function wysiwyg($id){$ret='';//'insertHTML'=>'conn'
		//$ret.=tag('button',array('onclick'=>'format(\'FontSize\');'),pic('font'));
		//$ret.=tag('button',array('onclick'=>'format(\'FontSize\',\'22\');'),pic('font'));
		$r=array('bold'=>'bold','italic'=>'italic','underline'=>'underline','insertUnorderedList'=>'list-ul','insertOrderedList'=>'list-ol','Indent'=>'indent','Outdent'=>'outdent','JustifyLeft'=>'align-left','JustifyCenter'=>'align-center','createLink'=>'link','delete'=>'ban','inserthorizontalrule'=>'minus');
		foreach($r as $k=>$v)
			$ret.=tag('button',array('onclick'=>'format(\''.$k.'\');'),pic($v,14));
		//$ret.=tag('button',array('onclick'=>'format(\'foreColor\',\'#ff0000\');'),pic('paint-brush'));
		//$ret.=tag('button',array('onclick'=>'format(\'foreColor\',\'#000000\');'),pic('paint-brush'));
	return $ret;}
	
	//open
	static function menu(){$ret='';
		$where=ses('uid')?' or uid="'.ses('uid').'"':'';
		$r=Sql::read('id,tit','articles','kv','where uid="0"'.$where);
		foreach($r as $k=>$v)$ret.=href('/app/article/'.$k,$v,'');
		return div($ret,'list');}
	
	//edit
	static function del($p){$id=val($p,'id');
		if(!val($p,'confirm'))return aj('art'.$id.'|article,del|id='.$id.',confirm=1',langp('confirm deleting').' art. '.$id,'btdel'); else{Sql::delete('articles',$id);
			Sql::delete('desktop','pagup|article,read|headers=1,tlx=1,id='.$id,'com');}
		return href('/app/article',langp('new'),'btn');}
	static function mkpub($p){
		Sql::update('articles','uid','0',val($p,'id'));
		return self::read($p);}
	static function mkico($p){
		Sql::update('articles','pub',val($p,'pub'),val($p,'id'));
		return self::read($p);}
	
	//save
	static function save($p){$id=val($p,'id');
		$tit=val($p,'tit'); $txt=val($p,'txt'); $tlx=val($p,'tlx');
		if(!$tit or !$txt)
			return div(aj('tlxart|article,edit_telex|rid='.val($p,'rid'),lang('empty form')),'alert');
		$p['txt']=$txt;//replace memtmp
		$txt=Trans::call($p);
		$savr=array('uid'=>ses('uid'),'tit'=>$tit,'txt'=>$txt,'pub'=>'0');
		if(!$id){$id=Sql::insert('articles',$savr); $p['id']=$id;
				$com='pagup|article,read|headers=1,tlx=1,id='.$id;//desk
			$nid=Sql::insert('desktop',[ses('uid'),'/documents','j',$com,'file',$tit,2]);}
		else Sql::updates('articles',$savr,$id);
		if($tlx)return self::read_telex($p);
		else return self::read(['id'=>$id]);}

	//telex
	static function menux($p){$rid=val($p,'rid'); $ret='';
		$where=ses('uid')?' or uid="'.ses('uid').'"':'';
		$r=Sql::read('id,tit','articles','kv','where uid="0"'.$where);
		foreach($r as $k=>$v)$ret.=aj('tlxapps|article,read_telex|id='.$k.',rid='.$rid,$v,'');
		return div($ret,'list');}
	
	static function read_telex($p){$id=val($p,'id'); $rid=val($p,'rid');
		$cols='id,tit,txt,pub';
		$r=Sql::read($cols,'articles','ra','where id='.$id); $r['rid']=$rid;
		return self::edit_telex($r);}
	
	static function edit_telex($p){$id=val($p,'id'); $rid=val($p,'rid');
		$ret=input('tit',val($p,'tit'),40,lang('title'),'tit'); $ok='';
		//$ret=hidden('tit','tlex'.$id);
		if($id)$ok=insertbt(langp('use'),$id.':article',$rid);
		//if($id)$ok=telex::publishbt($id,'article');
		$mnu=dropdown('article,menux|rid='.$rid,langpi('open'),'btn');
		$bt=langp($id?'modif':'save');
		$sav=aj('tlxart|article,save|tlx=1,id='.$id.',rid='.$rid.'|tit,txt',$bt,$id?'btn':'btsav');
		$ret.=$mnu.$sav.$ok;
		$ret.=self::wysiwyg($id);
		$txt=Conn::load(['msg'=>val($p,'txt'),'ptag'=>1]);
		$ret.=tag('div',['contenteditable'=>'true','id'=>'txt','class'=>'txth'],$txt);
		return div($ret,'','tlxart');}
	
	//edit
	static function edit($p){
		$id=val($p,'id'); $name=val($p,'name'); $date=val($p,'date'); $pub=val($p,'pub');
		$ret=div(input('tit',val($p,'tit'),28,'','tit'));
		$ret.=aj('art'.$id.',,y|article,save|id='.$id.'|tit,txt',langp('save'),'btsav');
		if($id && auth(2) && $name==ses('user')){
			$ret.=aj('art'.$id.'|article,del|id='.$id,langpi('delete'),'btdel');
			$ret.=aj('art'.$id.'|article,mkpub|id='.$id,langpi('make public'),'btn');
			if($pub)$ret.=aj('art'.$id.'|article,mkico|id='.$id.',pub=0',langpi('hide icon'),'btn');
			else $ret.=aj('art'.$id.'|article,mkico|id='.$id.',pub=1',langpi('make icon'),'btn');}
		$ret.=self::wysiwyg($id);
		return $ret;}
	
	static function editconn($p){$id=val($p,'id'); if(!$id)return;
		list($tit,$txt)=Sql::read('tit,txt','articles','rw','where id='.$id);
		$ret=aj('art'.$id.',,y|article,read|id='.$id.',edit=1',pico('back'),'btn').' ';
		$ret.=aj('art'.$id.',,y|article,save|brut=1,id='.$id.'|tit,txt',langp('save'),'btsav').br();
		$ret.=connectors::edit('txt');
		$ret.=textarea('txt',$txt,64,24).hidden('tit',$tit);
		return $ret;}
	
	//read
	static function art($p){$mnu=''; $edition='';
		$id=val($p,'id'); $name=val($p,'name'); 
		$date=val($p,'date'); $priv=val($p,'private');
		$title=val($p,'tit'); $txt=val($p,'txt'); $edit=val($p,'edit');
		if(ses('uid'))$mnu=aj('art'.$id.',,y|article,read|id='.$id,pico('refresh'),'btn').' ';
		if(ses('user')==$name or !$name){
			if($edit)$edition=self::edit($p);
			else $mnu.=aj('art'.$id.',,y|article,read|id='.$id.',edit=1',langp('edit'),'btn').' ';
			if($edit)$prm=array('contenteditable'=>'true','id'=>'txt','class'=>'txt');
			else $prm=array('class'=>'article');}
		else $prm=array('class'=>'article');
		if($edit)$mnu.=aj('art'.$id.',,y|article,editconn|id='.$id.',edit=1',pico('edit'),'btn').' ';
		if(ses('uid'))$mnu.=aj('popup|article',langpi('folder'),'btn').' ';
		//$mnu.=href('/app/article',langp('new'),'btn');
		//$mnu.=dropdown('article,menu|id='.$id,langpi('open'),'btn').' ';
		if(ses('index')=='telex' && $id)$url='art/'.$id; 
		else $url='app/article'.($id?'/'.$id:'');
		if(ses('uid'))$mnu.=href('/'.$url,langpi('url'),'btn');
		$ret['mnu']=span($mnu,'right');
		if($title && !$edit)$ret['t']=tag('h1','',$title);
		if($name && ses('uid'))$ret['by']=span(tag('h4','',lang('by').' '.$name.', '.$date),'small');
		$ret['edit']=$edition;
		//$txt=ptag($txt);
		$txt=Conn::load(['msg'=>$txt,'ptag'=>1]);
		$ret['m']=tag('div',$prm,$txt);
		return implode('',$ret);}
	
	static function artlx($p){
		$id=val($p,'id'); $txt=val($p,'txt');
		$ret=btj(pic('close'),'Close(\'popup\');','btn');
		$ret.=span(href('/art/'.$id,pic('link')),'right');
		$txt=Conn::load(['msg'=>$txt,'ptag'=>1]);
		$ret.=div($txt,'article');
		return $ret;}
	
	static function read($p){$id=val($p,'id'); $tlx=val($p,'tlx');
		$cols='name,tit,txt,DATE_FORMAT(articles.up,"%d/%m/%Y %H:%i") as date,pub';
		if($id)$r=Sql::read_inner($cols,'articles','login','uid','ra','where articles.id='.$id);
		if(isset($r))$p=merge($p,$r); else{$p['id']=''; $p['edit']=1;}
		$apf=val($p,'appFrom');
		if($apf){$apf::$title=$r['tit'];//meta
			$apf::$description=substr(strip_tags($r['txt']),0,200); $apf::$image='';}
		if($tlx)return self::artlx($p);
		else return self::art($p);}
	
	static function tit($p){$id=val($p,'id');
		if($id)return Sql::read('tit','articles','v','where id='.$id);}
	
	//tlex
	static function call($p){
		$p['id']=val($p,'id');
		if(!$p['id'])$p['edit']=1;
		$ret=self::read($p);
	return div($ret,'wrapper','art'.$p['id']);}
	
	#content
	static function content($p){$ret='';
		$p['id']=val($p,'param',val($p,'id'));
		if($p['id'])return self::call($p);
		//self::install();
		$ret.=aj('popup,,,1|article,call',pic('plus',36).div(lang('new')),'bicon');
		$r=Sql::read('id,tit','articles','kv','where uid=0 or uid="'.ses('uid').'" or pub="1"');
		if($r)foreach($r as $k=>$v)if($v){
			$bt=pic('file',32).div($v);
			$ret.=aj('popup,,,1|article,call|id='.$k,$bt,'bicon');}
		return $ret;}
}
?>
