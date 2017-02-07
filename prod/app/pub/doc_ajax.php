<?php

class doc_ajax{
	static $private='2';

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
		$ret.=Ajax::call($params,'Send','btn');
		$ret.=tag('div',array('id'=>'callback2'),'');
		$params=array(
			'com'=>'bubble,bb3',
			'app'=>'File,fdate',
			'prm'=>'fileRoot=app/pub/doc_ajax.php');
		$ret.=Ajax::call($params,'Test: Ajax::call(bubble)','btn');
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
			'prm'=>'fileRoot=app/pub/doc_ajax.php');
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
	\'prm\'=>\'fileRoot=app/pub/doc_ajax.php\',
	//inp: any parameters to capture before to send to the App
	\'inp\'=>\'\');
//build the button and specify the css
$ret=Ajax::call($params,\'test Ajax::call()\',\'btn\');
//div of callback
$ret.=tag(\'div\',array(\'id\'=>\'callback1\'),\'\');'));
		$ret.=br();
		
		#popup
		$ret.=tag('h3','','Ajax::call(popup)');
		//test
		$params=array(
			'com'=>'popup','app'=>'File,fdate',
			'prm'=>'fileRoot=app/pub/doc_ajax.php');
		$ret.=Ajax::call($params,'Test: Ajax::call(popup)','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$params=array(
	\'com\'=>\'popup\',
	\'app\'=>\'File,fdate\',
	\'prm\'=>\'fileRoot=app/pub/doc_ajax.php\');
$ret.=Ajax::call($params,\'Ajax::call(popup)\',\'btn\');'));
		$ret.=br();
		
		#popup
		$ret.=tag('h3','','Ajax::call(popup) using array for prm');
		//test
		$params=array(
			'com'=>'popup','app'=>'utils,result',
			'prm'=>array('msg'=>'display , and =','inp1'=>'msg:'));
		$ret.=Ajax::call($params,'Test: Ajax::call(popup)','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$params=array(
	\'com\'=>\'popup\',
	\'app\'=>\'utils,result\',
	\'prm\'=>array(\'msg\'=>\'display , and =\',\'inp1\'=>\'msg:\'));
$ret.=Ajax::call($params,\'Ajax::call(popup)\',\'btn\');'));
		$ret.=br();
		
		#pagup
		$ret.=tag('h3','','Ajax::call(pagup)');
		//test
		$params=array('com'=>'pagup','app'=>'utils,resistance');
		$ret.=Ajax::call($params,'Test: Ajax::call(pagup)','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$params=array(\'com\'=>\'pagup\',\'app\'=>\'utils,resistance\');
$ret.=Ajax::call($params,\'Ajax::call(pagup)\',\'btn\');'));
		$ret.=br();
		
		#bubble
		$ret.=tag('h3','','Ajax::call(bubble)');
		//test
		$params=array(
			'com'=>'bubble,bbl','app'=>'File,fdate',
			'prm'=>'fileRoot=app/pub/doc_ajax.php');
		$ret.=Ajax::call($params,'Test: Ajax::call(bubble)','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$params=array(
	\'com\'=>\'bubble,bb1\',\'app\'=>\'File,fdate\',
	\'prm\'=>\'fileRoot=app/pub/doc_ajax.php\');
$ret.=Ajax::call($params,\'Ajax::call(bubble)\',\'btn\');'));
		$ret.=br();
		
		#aj()
		$ret.=tag('h3','','Ajax::j()');
		//test
		$ret.=aj('popup|File,fdate|fileRoot=app/pub/doc_ajax.php','Test: Ajax::j()','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
//popup,option,injectJs|app,method|key1=p1,key2=p2|inp1,checkbox
$ret.=Ajax::j(\'div,popup|File,fdate|fileRoot=app/pub/doc_ajax.php\',\'Ajax::j()\',\'btn\');'));
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
$ret.=Ajax::call($params,\'Send\',\'btn\');
$ret.=tag(\'div\',array(\'id\'=>\'callback2\'),\'\');'));
		$ret.=br();
	
	return $ret;}
}
?>
