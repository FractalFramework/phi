<?php

class doc_vue{
	static $private='0';

	static function headers(){
		add_head('csscode','.console{
		background:#eee; border:1px solid #aaa; 
		}');
	}
	
	#content
	static function content($p){
		$ret=tag('h3','','vue::read($datas,$template)');
		
		$ret.=div(nl2br('vue is the motor of templates.
The template is written as connectors, which we place our variables as "_content".
connectors let write any tags like : [_var1*class=btn:div], and can be imbricated.
The $datas are in an array.
		'),'pane').br();
		
		$code='
$p1=val($p,\'p1\',\'hello1\'); $p2=val($p,\'p2\',\'hello2\'); $p3=val($p,\'p3\',\'hello3\');
$datas=[\'var1\'=>$p1,\'var2\'=>$p2,\'var3\'=>$p3,\'url\'=>\'http://tlex.fr\'];
$template=\'[[(var1)*class=btn:div][(var2)*div:tag][(var3)*(url):a]*:div]\';
$ret=vue::read($datas,$template);
';
		$ret.=div(build::Code($code),'console');
		$ret.=br();
		
		$ret.=tag('h3','','Returns:');
		$code='<div>
	<div class="btn">hello1</div>
	<div>hello2</div>
	<a href="http://tlex.fr">hello3</a>
</div>';
		$ret.=div(build::Code($code),'console');

		$ret.=tag('h3','','Result:');
		$p1=val($p,'p1','hello1'); $p2=val($p,'p2','hello2'); $p3=val($p,'p3','hello3');
		$datas=['var1'=>$p1,'var2'=>$p2,'var3'=>$p3,'url'=>'http://tlex.fr'];
		$template='[[(var1)*class=btn:div][(var2)*div:tag][(var3)*(url):a]*:div]';
		$ret.=vue::read($datas,$template);
	return $ret;}
}
?>
