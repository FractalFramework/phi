<?php
class gen{
private static $r=[];

static function setvar($d){$n=strpos($d,'=');
if($n!==false){$a=substr($d,0,$n); $b=substr($d,$n+1); self::$r[$a]=$b;}}

static function setvars($d){
$r=explode(',',$d); foreach($r as $v)self::setvar($v);}

static function readconn($d){$p=strrpos($d,':');//p*o:connector
if($p!==false)$r=array(substr($d,0,$p),substr($d,$p+1)); else $r=array($d,'');
$p=explode('*',$r[0]); return [$p[0],isset($p[1])?$p[1]:'',$r[1]];}

#read
static function reader($d,$b=''){//[p*o:c]
list($p,$o,$c)=self::readconn($d);
$r=['area','base','bgsound','embed','frame','input','link','meta','option']; //,'nextid'
if(in_array($c,$r)){$n=1; if(!$o)$o=$p;} else $n=0;
$atb=atr($o); if(!$atb)$atb['class']=$o; //echo $p.'*'.$o.':'.$c.br();
$s=strrpos($p,'.');
if($s){$xt=substr($p,$s+1);// && !$c
	if($xt=='jpg' or $xt=='png' or $xt=='gif')$c='img';
	elseif($xt=='mp3')$c='audio'; elseif($xt=='mp4')$c='video';
	elseif($xt=='pdf')$c='pdf';}
//elseif(substr($p,0,4)=='http')$c='url';
$r=['h','k','e','n'];//'q','s',
$r=['h'=>'big','k'=>'s','e'=>'sup','n'=>'sub'];//'q'=>'blockquote','s'=>'small',
if(isset($r[$c]))return $c=$r[$c];
switch($c){
	case('br'):return br(); break;
	case('-'):return hr(); break;
	case('a'):return lk($p,$o,'btxt'); break;
	case('p'):return ptag($p); break;
	case('url'):return conn::url($p,$o,'btxt'); break;
	case('img'):return conn::img($p,$o); break;
	case('list'):return conn::mklist($p); break;
	case('numlist'):return conn::mklist($p,1); break;
	case('table'):return conn::mktable($p,$o); break;
	case('web'):return tlex::playweb($p); break;
	case('ico'):return ico($p,$o); break;
	case('pic'):return pic($p,$o); break;
	case('picto'):return picto($p,$o); break;
	case('lang'):return lang($p,$o); break;
	case('help'):return hlpxt($p,$o); break;
	case('code'):return tag('pre','',tag('code','',$p.($o?'*'.$o:''))); break;
	case('artit'):return lk('/art/'.$p,art::tit(['id'=>$p]),'btlk'); break;
	case('artxt'):return art::call(['id'=>$p]); break;
	case('art'):return art::preview(['id'=>$p]); break;
	case('forms'):return forms::conn(['fcom'=>$p]); break;
	case('stabilo'):return span($p,'stabilo'); break;
	case('sticky'):return stabilo::pad($p,$o); break;
	case('apj'):$js='ajaxCall("div,cn'.$c.',,1|'.$p.','.$o.'","headers=1");';
		return div(csscode($js),'','cn'.$c); break;
	case('app'):return app($p,['param'=>$o,'headers'=>1]); break;
	case('popup'):return popup($p,$o,'btxt'); break;
	case('pagup'):return pagup($p,$o,'btxt'); break;
	case('imgup'):return imgup($p,$o); break;
	case('db'):return db::call(['f'=>'usr/'.$p]); break;
	case('aj'):return aj($p,$o,'btxt'); break;
	case('on'):return '['.$p.($o?'*'.$o:'').']'; break;
	case('no'):return; break;
	case('var'):return nl2br(self::$r[$p]); break;
	case('gen'):return self::parse(self::$r[$p],$b); break;
	case('setvar'):return self::setvars($p);break;}
//if(is_img($d))return img($d,'','',$o);
//if($p=='http' or $p=='https')return self::url($d,'','btxt');
if($c && method_exists($c,'call')){$q=new $c;
	if($o==1)return app($c,['appMethod'=>'call','conn'=>'no','id'=>$p,'headers'=>0]);
	if($o)$t=$o; elseif(method_exists($c,'tit'))$t=$q::tit(['id'=>$p]); else $t=$c;
	$bt=span(hlpic($c,28),'apptyp').' '.span($t,'apptit');
	return div(popup($c.',call|headers=1,id='.$p,$bt),'app');}
return tag($c,$atb,$p,$n);}

static function parse($d,$p=''){
$st='['; $nd=']'; $deb=''; $mid=''; $end='';
$in=strpos($d,$st);
if($in!==false){
	$deb=substr($d,0,$in);
	$out=strpos(substr($d,$in+1),$nd);
	if($out!==false){
		$nb=substr_count(substr($d,$in+1,$out),$st);
		if($nb>=1){
			for($i=1;$i<=$nb;$i++){$out_tmp=$in+1+$out+1;
				$out+=strpos(substr($d,$out_tmp),$nd)+1;
				$nb=substr_count(substr($d,$in+1,$out),$st);}
			$mid=substr($d,$in+1,$out);
			$mid=self::parse($mid,$p);}
		else $mid=substr($d,$in+1,$out);
		$mid=self::reader($mid,$p);
		$end=substr($d,$in+1+$out+1);
		$end=self::parse($end,$p);}
	else $end=substr($d,$in+1);}
else $end=$d;
return $deb.$mid.$end;}

static function read($d,$r,$o=''){self::$r=$r;
if($r)foreach($r as $k=>$v)$d=str_replace('('.$k.')',$v,$d);
$ret=self::parse($d,$o); //pr(self::$r);
//$ret=nl2br($ret);
return $ret;}

static function read_r($r,$d){
if($r)foreach($r as $k=>$v)$ret[]=self::parse($v,$tmp);
if(isset($ret))return implode('',$ret);}

static function com($p){
$d=val($p,'msg',val($p,'params'));
$r=val($p,'vars');
$o=val($p,'opt');
return self::read($d,$r,$o);}

}
?>