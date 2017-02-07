<?php

/*
Array value return value data with key=value
['value'] -> $r['value']
key not numeric return div with class as key
['class'=>'value'] -> div($r['value'],'class')
key not numeric and value is array return div with id as key
'id'=>['v1','v2'] -> div($v1.$v2,'','id')
Second level of Data Array returns concatenation
*/

class _phylo{

static function test($p){
	$struct=['header','content'=>['titles'=>['author','date','title'],'content'=>'text','related'],'footer'];
	$datas['header']='Hello';
	$datas['author']='Me';
	$datas['date']='Today';
	$datas['title']='Hey !';
	$datas['text'][]='Hello ';
	$datas['text'][]='World';
	$datas['related']='everything';
	$datas['footer']='Thank You';
	return Phylo::read($datas,$struct);
}
	
#content
static function content($p){
	$ret=self::test($p);
	return $ret;}
}
?>
