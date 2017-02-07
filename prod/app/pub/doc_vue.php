<?php

class doc_vue{
	static $private='0';

	static function headers(){
		Head::add('csscode','.console{
		background:#eee; border:1px solid #aaa; 
		}');
	}
	
	#content
	static function content($p){
		$ret=tag('h3','','Vue::read($datas,$template)');
		
		$ret.=div(nl2br('Vue is the motor of templates.
The template is written as Connectors, which we place our variables as "_content".
Connectors let write any tags like : [_var1*class=btn:div], and can be imbricated.
The $datas are in an array.
		'),'pane').br();
		
		$code='
$p1=val($p,\'p1\',\'hello1\'); $p2=val($p,\'p2\',\'hello2\'); $p3=val($p,\'p3\',\'hello3\');
$datas=[\'var1\'=>$p1,\'var2\'=>$p2,\'var3\'=>$p3,\'url\'=>\'http://tlex.fr\'];
$template=\'[[_var1*class=btn:div][_var2*div:tag][_var3*_url:a]*:div]\';
$ret=Vue::read($datas,$template);
';
		$ret.=div(Build::Code($code),'console');
		$ret.=br();
		
		$ret.=tag('h3','','Returns:');
		$code='<div>
	<div class="btn">hello1</div>
	<div>hello2</div>
	<a href="http://ph1.fr">hello3</a>
</div>';
		$ret.=div(Build::Code($code),'console');

		$ret.=tag('h3','','Result:');
		$p1=val($p,'p1','hello1'); $p2=val($p,'p2','hello2'); $p3=val($p,'p3','hello3');
		$datas=['var1'=>$p1,'var2'=>$p2,'var3'=>$p3,'url'=>'http://ph1.fr'];
		$template='[[_var1*class=btn:div][_var2*div:tag][_var3*_url:a]*:div]';
		$ret.=Vue::read($datas,$template);
	return $ret;}
}
?>
