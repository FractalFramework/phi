<?php 

class Vue{

static function template(){
	return '
	[
		[_var1*class=btn:div]
		[_var2*div:tag]
		[_var3*_url:a]
	*:div]
	';}

static function test(){
	$datas=['var1'=>'hello1','var2'=>'hello2','var3'=>'hello3','url'=>'http://ph1.fr'];
	$template=Vue::template();
	return Vue::read($datas,$template);}

static function reader($d,$b){
	list($p,$o,$c)=readconn($d); $atb=atbr($o);
	$b='div,span,h1,h2,h3,h4,small,big,';
	if(strpos($b,$c.',')!==false)return tag($c,$atb,$p);//txt*class=btn,:tag
	switch($c){
		case('br'):return br(); break;
		case('a'):return href($o,$p); break;//url*href=http://ph1.fr:a
		case('tag'):return tag($o,'',$p); break;//txt*b:tag
		//case('icon'):return icon($p,$o); break;
		case('pic'):return pico($p,$o); break;
		//case('picto'):return picto($p,$o); break;
		//case('form'):return Form::com($p); break;//
		case('img'): return img($p,$o); break;
		//case('thumb'): return make_thumb_d($p,$o); break;
		case('apj'):$js='ajaxCall("div,cn'.$c.',,1|'.$p.','.$o.'","headers=1");';
			return div(Head::jsCode($js),'','cn'.$c); break;
		case('app'):return App::open($p,['param'=>$o,'headers'=>1]); break;
		case('appbt'):return aj('popup|'.$c.','.$o.'|param='.$p,langp('open').' '.$c.':'.$p,'btn'); break;
		case('no'):return '['.$p.']'; break;}
	return '['.$d.']';}
	
static function read($p,$tmp){
	$ret=Conn::read($tmp,'Vue','reader');
	foreach($p as $k=>$v)$ret=str_replace('_'.$k.'',$v,$ret);
	return $ret;}

static function read_r($r,$tmp){$ret='';
	foreach($r as $k=>$v)$ret.=self::read($v,$tmp);
	return $ret;}
	
}

?>