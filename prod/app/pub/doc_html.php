<?php

class doc_html{
	static $private='0';

	static function headers(){
		Head::add('csscode','.console{
		background:#eee; border:1px solid #aaa; 
		}');
	}
	
	#content
	static function content($prm){
		$ret='';
		
		#html tag
		$ret.=tag('h2','','tag()');
		//test
		$params=array('com'=>array('popup'),'app'=>array('doc_html','htmltag'));
		$ret.=tag('span',array('class'=>'btn'),'hello');
		//code
		$ret.=tag('div','class=console',Build::Code('
//Array attributes
$ret=tag(\'span\',array(\'class\'=>\'btn\'),\'hello\');
//String attributes
$ret=tag(\'span\',\'class=btn\',\'hello\');'));
		$ret.=br();
		
		#div
		$ret.=tag('h2','','div()');
		//test
		$ret.=div('hello','btn','id');
		//code
		$ret.=tag('div','class=console',Build::Code('
$ret=div(\'hello\',\'btn\',\'id\');'));
		$ret.=br();
		
		#a
		$ret.=tag('h2','','href()');
		//test
		$ret.=href('http://ph1.fr','ph1.fr','btn');
		//code
		$ret.=tag('div','class=console',Build::Code('
$ret=tag(\'a\',array(\'href\'=>\'//ph1.fr\'),\'hello\');
$ret=href(\'//ph1.fr\',\'ph1.fr\',\'btn\');'));
		$ret.=br();
		
		#img
		$ret.=tag('h2','','img()');
		//test
		$ret.=img('usr/telex/account-login-2x.png');
		//code
		$ret.=tag('div','class=console',Build::Code('
//4th param close tag with />
$ret=tag(\'img\',array(\'src\'=>\'usr/telex/account-login-2x.png\'),\'\',1);
$ret=img(\'usr/telex/account-login-2x.png\');'));
		$ret.=br();
		
		#input
		$ret.=tag('h2','','input()');
		//test
		$ret.=input('inp1','hello',16,1);
		//code
		$ret.=tag('div','class=console',Build::Code('
$ret.=input(\'inp1\',\'hello\',16,1);'));
		$ret.=br();
		
		#textarea
		$ret.=tag('h2','','textarea()');
		//test
		$ret.=textarea('inp2','',20,4,'hello');
		//code
		$ret.=tag('div','class=console',Build::Code('
$ret.=textarea(\'inp2\',\'\',20,4,\'hello\');'));
		$ret.=br();
		
		#select
		$ret.=tag('h2','','select()');
		//test
		$options=array(1=>'one','two','three','four','five');
		$ret.=select('inp4',$options,'two','v');
		//code
		$ret.=tag('div','class=console',Build::Code('
$options=array(1=>\'one\',\'two\',\'three\',\'four\',\'five\');
$ret.=select(\'inp4\',$options,\'two\',\'v\');'));
		$ret.=br();
		
		#checkbox
		$ret.=tag('h2','','checkbox()');
		//test
		$opts=array('1a'=>lang('yes'),'2a'=>lang('no'));
		$ret.=checkbox('options',$opts,'1',' ');
		//code
		$ret.=tag('div','class=console',Build::Code('
$opts=array(\'1\'=>lang(\'yes\'),\'2\'=>lang(\'no\'));
$ret.=checkbox(\'options\',$opts,\'1\');'));
		$ret.=br();
		
		#radio
		$ret.=tag('h2','','radio()');
		//test
		$opts=array('1b'=>lang('yes'),'2b'=>lang('no'));
		$ret.=radio($opts,'position','1');
		//code
		$ret.=tag('div','class=console',Build::Code('
$opts=array(\'1\'=>lang(\'yes\'),\'2\'=>lang(\'no\'));
$ret.=radio($opts,\'position\',\'1\');'));
		$ret.=br();
		
		#datalist
		$ret.=tag('h2','','datalist()');
		//test
		$opts=array('1'=>lang('yes'),'2'=>lang('no'));
		$ret.=datalist($opts,'id','',8,'label');
		//code
		$ret.=tag('div','class=console',Build::Code('
$opts=array(\'1\'=>lang(\'yes\'),\'2\'=>lang(\'no\'));
$ret.=datalist($opts,\'id\',\'\',8,\'label\');'));
		$ret.=br();
	
	return $ret;}
}
?>