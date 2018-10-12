<?php

class sky0{
static $private='0';
static $a='sk';

static function admin(){
	$r[]=['','j','popup|sky,content','plus',lang('open')];
	$r[]=['','pop','core,help|ref=_model_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|f=_model','code','Code'];
	return $r;}

static function injectJs(){return;}
static function headers(){
	add_head('csscode','
	.skyframe{display:inline-block; width:400px; height:300px; border:1px solid black;}
	.sky{background-repeat:no-repeat; background-attachment:fixed;}
	.sky0{background-image:
		linear-gradient(to bottom,rgba(119,119,119,0),rgba(119,0,119,1)),
		linear-gradient(to left,rgba(119,119,119,0),rgba(119,55,255,1)),
		linear-gradient(to right,rgba(119,119,119,0),rgba(255,119,119,1));}
	.sky1{background-image:linear-gradient(to top,rgba(165,52,152,1),rgba(54,21,125,1));}
	.sky2{background-image:linear-gradient(to top,rgba(115,203,205,1),rgba(0,124,199,1));}
	.sky3{background-image:linear-gradient(to top,rgba(199,134,104,1),rgba(151,123,164,1));}
	.sky4{background-image:
		linear-gradient(to bottom,rgba(108,0,159,0),rgba(255,49,0,0.8)),
		linear-gradient(to left,rgba(0,0,0,0.6),rgba(59,23,121,1)),
		linear-gradient(to right,rgba(74,29,130,1),rgba(0,0,0,0.2));}
	.sky5{background-image:
		linear-gradient(to bottom,rgba(223,88,20,1),rgba(248,161,90,0.8)),
		linear-gradient(to left,rgba(0,0,0,0.6),rgba(247,117,33,1)),
		linear-gradient(to right,rgba(252,197,141,1),rgba(0,0,0,0.4));}
	.sky6{background-image:linear-gradient(to bottom,rgba(88,223,20,1),rgba(100,248,90,0.8)), linear-gradient(to left,rgba(0,0,0,0.8),rgba(017,47,3,1)), linear-gradient(to right,rgba(97,52,141,1),rgba(0,0,0,0.4));}
	');
	add_head('jscode',self::injectJs());}

static function titles($p){
	$d=val($p,'appMethod');
	$r['content']='welcome';
	$r['build']='model';
	if(isset($r[$d]))return lang($r[$d]);}

#build
static function build($r){$usr=ses('user');
	$clr=sesr('clr',$usr); $clr0=clrneg($clr,1); $clra=hexrgb($clr,0.7);
	//$rgb1=hexrgb_r($clr); $rgb2=hexrgb_r($clr0);
	$ra=['clr'=>$clr,'inv'=>$clr0,'alp'=>$clra];
	$rc=[0,255,119];
	$rd=['to bottom','to right','to top','to left'];
	foreach($r as $k=>$v){
		$rb=[$rd[$k]];
		foreach($v as $ka=>$va){
			if(!is_numeric($va))$rb[]='#'.$ra[$va];
			else{$re=str_split($va);
				$rb[]='rgba('.implode(',',[$rc[$re[0]],$rc[$re[1]],$rc[$re[2]],$re[3]/10]).')';}}
		$ret[]='linear-gradient('.implode(',',$rb).')';}
	//linear-gradient(to bottom,'.hexrgb($clr,0.7).',rgba(119,119,119,0)),
	//linear-gradient(to left,rgba(119,119,119,0),rgba(119,119,255,0.5)),
	//linear-gradient(to right,rgba(119,119,119,0),rgba(255,119,119,0.5))
	return div('','sky','','background-image:'.implode(',',$ret).';');}

#read
static function call($p){$ret='';
	//$ca='clr'; $cb='neg'; $cc='alp';
	$r=[['2025','2220'],['2220','2215'],['2220','1225']];
	$ret.=self::build($r);
	$r=[['clr','2220'],['2220','2215'],['2220','1225']];
	//$ret.=self::build($r);
	$r=[['2025','2220'],['2000','2215'],['2220','1225'],['0115','0000']];
	$ret.=self::build($r);
	$r=[['0001','2220'],['0101','2220'],['0011','2220'],['0001','2220']];
	//$ret.=self::build($r);
	//$rb=['sunset','evening','day','automn','red','orange','green','sea','purple','night'];
	$rb=sql('tit,css','sky','kv','where uid="'.ses('uid').'" or pub>2'); //p($rb);
	foreach($rb as $k=>$v)$ret.=div($k,'skyframe','','background-image:'.$v.';');
	return $ret;}

static function com(){
	return self::content($p);}

#content
static function content($p){
	//$bt=aj('cbklr|sky0,call|headers=1,sky=1',lang('send'),'btn');
return div(self::call($p),'pane','cbklr');}
}
?>