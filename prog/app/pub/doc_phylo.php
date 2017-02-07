<?php

class doc_phylo{
	static $private='0';

	static function headers(){
		Head::add('csscode','.console{
		background:#eee; border:1px solid #aaa; 
		}');
	}
	
	#content
	static function content($p){
		$ret=tag('h3','','Phylo::read($datas,$structure)');
		
		$ret.=div(nl2br('Phylo is used to place datas in a tree of divs. 
The value of array will search the $data having this value as key :
[\'value\'] -> $r[\'value\']
key not numeric return div with class as key
[\'class\'=>\'value\'] -> div($r[\'value\'],\'class\')
key not numeric and value is array return div with id as key
\'id\'=>[\'v1\',\'v2\'] -> div($v1.$v2,\'\',\'id\')
Second level of Data Array returns concatenation
		'),'pane').br();
		
		$code='
$struct=[\'header\',\'content\'=>[\'titles\'=>[\'author\',\'date\',\'title\'],\'content\'=>\'text\',\'related\'],\'footer\'];
$datas[\'header\']=\'Hello\';
$datas[\'author\']=\'Me\';
$datas[\'date\']=\'Today\';
$datas[\'title\']=\'Hey !\';
$datas[\'text\'][]=\'Hello \';
$datas[\'text\'][]=\'World\';
$datas[\'related\']=\'everything\';
$datas[\'footer\']=\'Thank You\';
$ret=Phylo::read($datas,$struct);
';
		$ret.=div(Build::Code($code),'console');
		$ret.=br();
		
		$ret.=tag('h3','','Returns:');
		$code='Hello
<div id="content">
	<div id="titles">MeTodayHey !</div>
	<div class="content">Hello World</div>
	everything
</div>
Thank You';
		$ret.=div(Build::Code($code),'console');

		$ret.=tag('h3','','Result:');
		$struct=['header','content'=>['titles'=>['author','date','title'],'content'=>'text','related'],'footer'];
		$datas['header']='Hello';
		$datas['author']='Me';
		$datas['date']='Today';
		$datas['title']='Hey !';
		$datas['text'][]='Hello ';
		$datas['text'][]='World';
		$datas['related']='everything';
		$datas['footer']='Thank You';
		$ret.=Phylo::read($datas,$struct);
	return $ret;}
}
?>
