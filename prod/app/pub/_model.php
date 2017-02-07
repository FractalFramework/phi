<?php

class _model{	
	#set this App reserved to loged users
	//0=public,1:page open, menu closed,2:closed,3,4,5,6,7:needed auth level
	static $private='0';

	#js to append to the header of the parent page (who call this App in a popup)
	//add param 4 'injectJs' of the callbacks params, to overload that in the headers
	static function injectJs(){
		return '';
	}
	
	#header to display with the App
	//add param headers=1 when call by ajax a method who use css
	static function headers(){
		Head::add('csscode','.deco{
		color:#424242; background-color:#dbdbdb; border:1px solid #aaa;
		padding:7px 10px; border-radius:2px; box-shadow: 2px 2px 4px #aaa;
		}');
		Head::add('jscode',self::injectJs());
		Head::add('meta',array('attr'=>'property','prop'=>'description','content'=>'object _model for Ph1'));
	}
	
	#menus to add to the admin
	static function admin(){
		//see Menus
		$r[]=array('','j','popup|_model,content','plus',lang('open'));
		return $r;
	}
	
	#install db
	static function install(){
		Sql::create('model',array('mid'=>'int','mname'=>'var'),0);}//1=update
	
	#titles to display in popup for each method
	static function titles($d){
		$d=val($p,'appMethod');
		$r['content']='welcome';
		$r['build']='model';
		if(isset($r[$d]))return lang($r[$d]);//lang()=vocable
	}
	
	#called by ajax
	static function call($prm){
		return $prm['msg'].': '.$prm['inp1'];
	}
	
	#interface with other Apps
	static function com(){
	}
	
	#content
	//url: '/_model:p1=val1,p2=val2';
	static function content($prm){$ret='';
		//self::install();
		$p1=val($prm,'p1');
		$p2=val($prm,'p2');
		$p['rid']=randid('md');
		$p['p1']=val($p,'param',val($p,'p1'));//unamed param
		//$ret=hlpbt('underbuilding');
		$ret=input('inp1','value1','','1');
		$ret.=ajax('popup','_model,call','msg=text','inp1',lang('send'),'btn');
		$ret=tag('div',array('class'=>'deco'),$ret);
	return $ret;}
}
?>
