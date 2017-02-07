<?php

class doc_ajax{
	static $private='0';

	static function headers(){
		Head::add('csscode','.console{
		background:#eee; border:1px solid #aaa; 
		}');
	}

	static function ajaxform(){$ret='';
		//send form
		$ret.=input('inp1','hello',16,1);
		$ret.=tag('input','type=checkbox,id=checkbox,checked=1','','shortTag');
		$ret.=tag('label','for=checkbox,class=small','checkbox','');
		$ret.=textarea('textarea','',20,4,'hello');
		$options=array(1=>'one','two','three','four','five');
		$ret.=select('select',$options,'two','v');
			
		$params=array(
			'com'=>'div,callback2,y',
			'app'=>'utils,result',
			'prm'=>'msg=text in input,verbose=1',
			'inp'=>'inp1,checkbox,textarea,select');
		$ret.=Ajax::call($params,'Send','btsav');
		$ret.=tag('div',array('id'=>'callback2'),'');
		return $ret;
	}
	
	#content
	static function content($prm){
		$ret='';
		
		#Ajax::call
		$ret.=tag('h2','','Ajax::call()');
		//test
		$params=array(
			'com'=>'div,callback1',
			'app'=>'File,fdate',
			'prm'=>'fileRoot=prog/app/pub/doc_ajax.php');
		$ret.=Ajax::call($params,'Test: Ajax::call()','btn');
		$ret.=tag('div',array('id'=>'callback1'),'');
		//code
		$ret.=tag('div','class=console',Build::Code('
//4 types of parameters:
$params=array(
	//com: 4 parameters : callbackType,callbackId,callbackOption,InjectJs
	\'com\'=>\'div,callback1\',
	//app: 2 parameters : appName,appMethod
	\'app\'=>\'File,fdate\',
	//prm: any parameters to send to the App
	\'prm\'=>\'fileRoot=prog/app/pub/doc_ajax.php\',
	//inp: any parameters to capture before to send to the App
	\'inp\'=>\'\');
//build the button and specify the css
$ret=Ajax::call($params,\'test Ajax::call()\',\'btn\');
//div of callback
$ret.=tag(\'div\',array(\'id\'=>\'callback1\'),\'\');'));
		$ret.=br();
		
		#aj()
		$ret.=tag('h3','','aj()');
		//test
		$ret.=aj('popup|File,fdate|fileRoot=prog/app/pub/doc_ajax.php','Test: aj()','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
//popup,option,injectJs|app,method|key1=p1,key2=p2|inp1,checkbox
$ret.=aj(\'popup|File,fdate|fileRoot=prog/app/pub/doc_ajax.php\',\'aj()\',\'btn\');'));
		$ret.=br();
		
		#popup()
		$ret.=tag('h3','','popup()');
		//test
		$ret.=popup('File,fdate|fileRoot=prog/app/pub/doc_ajax.php','Test: popup()','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$ret.=popup(\'File,fdate|fileRoot=prog/app/pub/doc_ajax.php\',\'popup()\',\'btn\');'));
		$ret.=br();
		
		#bubble()
		$ret.=tag('h3','','bubble()');
		//test
		$ret.=bubble('File,fdate|fileRoot=prog/app/pub/doc_ajax.php','Test: bubble()','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$ret.=bubble(\'File,fdate|fileRoot=prog/app/pub/doc_ajax.php\',\'bubble()\',\'btn\');'));
		$ret.=br();
		
		#pagup()
		$ret.=tag('h3','','pagup()');
		//test
		$ret.=pagup('File,fdate|utils,resistance','Test: pagup()','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$ret.=pagup(\'File,fdate|utils,resistance\',\'pagup()\',\'btn\');'));
		$ret.=br();
		
		#ajax form
		$ret.=tag('h2','','ajax form');
		//test
		$params=array('com'=>'popup','app'=>'doc_ajax,ajaxform');
		$ret.=Ajax::call($params,'Test: ajax form','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
//inputs
$ret.=input(\'inp1\',\'hello\',16,1);
$ret.=tag(\'input\',\'type=checkbox,id=checkbox,checked=1\',\'\',\'shortTag\');
$ret.=tag(\'label\',\'for=checkbox,class=small\',\'checkbox\',\'\');
$ret.=textarea(\'textarea\',\'\',20,4,\'hello\');

//select
$options=array(1=>\'one\',\'two\',\'three\',\'four\',\'five\');
$ret.=select(\'select\',$options,\'two\',\'v\');

//send button
$params=array(
	\'com\'=>\'div,callback2,y\',//reset position of popup
	\'app\'=>\'utils,result\',
	\'prm\'=>\'msg=text in input,verbose=1\',//verbose
	\'inp\'=>\'inp1,checkbox,textarea,select\');
$ret.=Ajax::call($params,\'Send\',\'btsav\');
$ret.=tag(\'div\',array(\'id\'=>\'callback2\'),\'\');'));
		$ret.=br();
	
	return $ret;}
}
?>
