<?php

class doc_html{
	static $private='0';

	static function headers(){
		add_head('csscode','.console{
		background:#eee; border:1px solid #aaa; 
		}');
	}
	
	#content
	static function content($p){
		$ret='';
		
		#html tag
		$ret.=tag('h2','','tag()');
		//test
		$ret.=tag('span',array('class'=>'btn'),'hello');
		//code
		$ret.=tag('div','class=console',build::Code('
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
		$ret.=tag('div','class=console',build::Code('
$ret=div(\'hello\',\'btn\',\'id\');'));
		$ret.=br();
		
		#a
		$ret.=tag('h2','','lk()');
		//test
		$ret.=lk('http://tlex.fr','tlex.fr','btn');
		//code
		$ret.=tag('div','class=console',build::Code('
$ret=tag(\'a\',array(\'href\'=>\'//tlex.fr\'),\'hello\');
$ret=lk(\'//tlex.fr\',\'tlex.fr\',\'btn\');'));
		$ret.=br();
		
		#img
		$ret.=tag('h2','','img()');
		//test
		$ret.=img('/usr/tlex/img/home.jpg');
		//code
		$ret.=tag('div','class=console',build::Code('
//4th param close tag with />
$ret=tag(\'img\',array(\'src\'=>\'/usr/tlex/logo/tlex.jpg\'),\'\',1);
$ret=img(\'usr/tlex/logo/tlex.png\');'));
		$ret.=br();
		
		#input
		$ret.=tag('h2','','input()');
		//test
		$ret.=input('inp1','hello',16,1);
		//code
		$ret.=tag('div','class=console',build::Code('
$ret.=input(\'inp1\',\'hello\',16,1);'));
		$ret.=br();
		
		#textarea
		$ret.=tag('h2','','textarea()');
		//test
		$ret.=textarea('inp2','',20,4,'hello');
		//code
		$ret.=tag('div','class=console',build::Code('
$ret.=textarea(\'inp2\',\'\',20,4,\'hello\');'));
		$ret.=br();
		
		#select
		$ret.=tag('h2','','select()');
		//test
		$options=array(1=>'one','two','three','four','five');
		$ret.=select('inp4',$options,'two','v');
		//code
		$ret.=tag('div','class=console',build::Code('
$options=array(1=>\'one\',\'two\',\'three\',\'four\',\'five\');
$ret.=select(\'inp4\',$options,\'two\',\'v\');'));
		$ret.=br();
		
		#checkbox
		$ret.=tag('h2','','checkbox()');
		//test
		$opts=array('1a'=>lang('yes'),'2a'=>lang('no'));
		$ret.=checkbox('options',$opts,'1',' ');
		//code
		$ret.=tag('div','class=console',build::Code('
$opts=array(\'1\'=>lang(\'yes\'),\'2\'=>lang(\'no\'));
$ret.=checkbox(\'options\',$opts,\'1\');'));
		$ret.=br();
		
		#radio
		$ret.=tag('h2','','radio()');
		//test
		$opts=array('1b'=>lang('yes'),'2b'=>lang('no'));
		$ret.=radio('position',$opts,'1');
		//code
		$ret.=tag('div','class=console',build::Code('
$opts=array(\'1\'=>lang(\'yes\'),\'2\'=>lang(\'no\'));
$ret.=radio(\'position\',$opts,\'1\');'));
		$ret.=br();
		
		#datalist
		$ret.=tag('h2','','datalist()');
		//test
		$opts=array('1'=>lang('yes'),'2'=>lang('no'));
		$ret.=datalist('id',$opts,'',8,'label');
		//code
		$ret.=tag('div','class=console',build::Code('
$opts=array(\'1\'=>lang(\'yes\'),\'2\'=>lang(\'no\'));
$ret.=datalist(\'id\',$opts,\'\',8,\'label\');'));
		$ret.=br();
	
	return $ret;}
}
?>