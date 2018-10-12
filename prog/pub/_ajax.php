<?php

class _ajax{

	static function headers(){
		add_head('csscode','');
	}

	static function test(){
		//$ret=aj('bubble,,1|utils,result|msg=txt,inp1=ok','bubble as menu','btn');
		$ret=bubble('utils,result|msg=txt,inp1=ok','bubble as menu','btn',1);
		//$ret.=aj('callback|utils,resistance','no loading','btn');
		$ret.=aj('callback|utils,resistance','no loading','btn');
		$ret.=aj('callback,,x|utils,result|msg=txt,inp1=ok,','close popup','btn');
		$ret.=aj('callback,,y|utils,result|msg=txt,inp1=ok,','resize popup','btn');
		return div($ret,'','cbtest');
	}
	
	#content
	static function content($p){
		$ret='';
		
		#using call()
		$prm=array(
		//4 types of parameters:
			//callbackType,callbackId,callbackOption,InjectJs
			'com'=>'div,callback',
			//appName,appMethod
			'app'=>'file,fdate',
			//any parameters to send to the app
			'prm'=>'fileRoot=app/pub/_ajax.php',
			//values to capture before to send to the app
			'inp'=>'inp1');
		//build the button and specify the css
		$ret=aj(implode('|',$prm),'call:fdate','btn');
		
		//popup
		$ret.=popup('file,fdate|fileRoot=app/pub/_ajax.php','popup','btn');
		
		//pagup
		$ret=pagup('utils,resistance','call:fdate','btn');
		$ret.=br().br();
		
		//bubble
		$ret.=bubble('file,fdate|fileRoot=app/pub/_ajax.php','bubble','btn');
		
		//menu
		$js=ajs('bubble,bb2,1|utils,resistance','','');
		$prm=array('id'=>'bb2','class'=>'btn','onclick'=>$js);
		$ret.=tag('a',$prm,'bigbubble');
		
		//using j()
		$ret.=aj('div,callback|file,fdate|fileRoot=app/pub/_ajax.php,verbose=1,format=ymd.His','call:ftime','btn');
		
		$ret.=br();
		
	    //send form
		$ret.=input('inp1','hello',16,1);
		$ret.=tag('input','type=checkbox,id=inp2,checked=1','','shortTag');
		$ret.=tag('label','for=inp2,class=small','checkbox','');
		$ret.=textarea('inp3','',20,4,'hello');
		
		$options=array(1=>'one','two','three','four','five');
		$ret.=select('inp4',$options,'two','v');
		
		$ret.=aj('callback,,y|utils,result|msg=text in input,verbose=1|inp1,inp2,inp3,inp4','call:fdate','btn');
		$ret.=br().br();
		
		//options
		$ret.=aj('popup|_ajax,test','ajax options','btn');
		$ret.=br().br();
		
		$ret.=tag('div',array('id'=>'callback'),'callback').br();
	
	return $ret;}
}
?>
