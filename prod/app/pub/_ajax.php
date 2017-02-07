<?php

class _ajax{

	static function headers(){
		Head::add('csscode','');
	}

	static function test(){
		//$ret=Ajax::j('bubble,,1|utils,result|msg=txt,inp1=ok','bubble as menu','btn');
		$ret=dropdown('utils,result|msg=txt,inp1=ok','bubble as menu','btn');
		//$ret.=Ajax::j('callback|utils,resistance','no loading','btn');
		$ret.=aj('callback|utils,resistance','no loading','btn');
		$ret.=aj('callback,,x|utils,result|msg=txt,inp1=ok,','close popup','btn');
		$ret.=aj('callback,,y|utils,result|msg=txt,inp1=ok,','resize popup','btn');
		return div($ret,'','cbtest');
	}
	
	#content
	static function content($prm){
		$ret='';
		
		#using call()
		$params=array(
		//4 types of parameters:
			//callbackType,callbackId,callbackOption,InjectJs
			'com'=>'div,callback',
			//appName,appMethod
			'app'=>'File,fdate',
			//any parameters to send to the App
			'prm'=>'fileRoot=app/pub/_ajax.php',
			//values to capture before to send to the App
			'inp'=>'inp1');
		//build the button and specify the css
		$ret=Ajax::call($params,'call:fdate','btn');
		
		//popup
		$params=array('com'=>'popup','app'=>'File,fdate','prm'=>'fileRoot=app/pub/_ajax.php');
		$ret.=Ajax::call($params,'popup','btn');
		
		//pagup
		$params=array('com'=>'pagup','app'=>'utils,resistance','prm'=>'');
		$ret.=Ajax::call($params,'full page','btn');
		
		$ret.=br().br();
		
		//bubble
		$params=array('com'=>'bubble,bbl','app'=>'File,fdate',
			'prm'=>'fileRoot=app/pub/_ajax.php');
		$ret.=Ajax::call($params,'bubble','btn');
		
		//menu
		$params=array('com'=>'bubble,bb2,1','app'=>'utils,resistance');//1 mean vertical
		$js=Ajax::js($params);
		$params=array('id'=>'bb2','class'=>'btn','onclick'=>$js);
		$ret.=tag('a',$params,'bigbubble');
		
		//using j()
		$ret.=Ajax::j('div,callback|File,fdate|fileRoot=app/pub/_ajax.php,verbose=1,format=ymd.His','call:ftime','btn');
		
		$ret.=br();
		
	    //send form
		$ret.=input('inp1','hello',16,1);
		$ret.=tag('input','type=checkbox,id=inp2,checked=1','','shortTag');
		$ret.=tag('label','for=inp2,class=small','checkbox','');
		$ret.=textarea('inp3','',20,4,'hello');
		
		$options=array(1=>'one','two','three','four','five');
		$ret.=select('inp4',$options,'two','v');
			
		$params=array(
			'com'=>'div,callback,y',
			'app'=>'utils,result',
			'prm'=>'msg=text in input,verbose=1',
			'inp'=>'inp1,inp2,inp3,inp4');
		$ret.=Ajax::call($params,'call:form','btn');
		$ret.=br().br();
		
		//options
		$ret.=Ajax::j('popup|_ajax,test','ajax options','btn');
		$ret.=br().br();
		
		$ret.=tag('div',array('id'=>'callback'),'callback').br();
	
	return $ret;}
}
?>
