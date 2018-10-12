<?php
#tlex.fr GNU/GPL

#autoload
function loadapp($d){$dr=ses('dev'); $r=sesfunc('scandir_b',$dr,0);
	if($r)foreach($r as $k=>$v){$f=$dr.'/'.$v.'/'.$d.'.php'; if(file_exists($f))require_once $f;}
	$f=$dr.'/'.$d.'.php'; if(file_exists($f))require_once $f;}
spl_autoload_register('loadapp');

#dev
function p($r){print_r($r);}
function pr($r,$o=''){$ret='<pre>'.print_r($r,true).'</pre>'; 
if($o)return $ret; else echo $ret;}
function br(){return '<br />';}
function hr(){return '<hr />';}
function sp(){return "&nbsp;";}
function n(){return "\n";}

#attributs
function atb($d,$v){return ' '.$d.'="'.$v.'"';}
function atc($d){return ' class="'.$d.'"';}
function atd($d){return ' id="'.$d.'"';}
function ats($d){return ' style="'.$d.'"';}
function atn($d){return ' name="'.$d.'"';}
function atv($d){return ' value="'.$d.'"';}
function atz($d){return ' size="'.$d.'"';}
function att($d){return ' title="'.$d.'"';}
function atj($d,$r){return $d.'(\''.(is_array($r)?implode('\',\'',$r):$r).'\');';}
function atr($d){$ret=''; $r=explode(',',$d);//explode_array_keys($d,',','=')
	if($r)foreach($r as $k=>$v){$rb=explode('=',$v); if(isset($rb[1]))$ret[$rb[0]]=$rb[1];}
	return $ret;}

//tags
function tag($tag,$r,$t='',$o=''){$ret=''; if(is_string($r))$r=atr($r);
	if(is_array($r))foreach($r as $k=>$v)if($v)$ret.=atb($k,$v);
	return '<'.$tag.$ret.(!$o?'>'.$t.'</'.$tag.'>':'/>');}
function div($t,$c='',$id='',$s='',$rb=''){
	$r=['id'=>$id,'class'=>$c,'style'=>$s]; if($rb)$r+=$rb;
	return tag('div',$r,$t);}
function span($t,$c='',$id='',$s='',$rb=''){
	$r=['id'=>$id,'class'=>$c,'style'=>$s]; if($rb)$r+=$rb;
	return tag('span',$r,$t);}
function ul($t,$c='',$id='',$s=''){
	return tag('ul',['id'=>$id,'class'=>$c,'style'=>$s],$t);}
function li($t,$c='',$id='',$s=''){
	return tag('li',['id'=>$id,'class'=>$c,'style'=>$s],$t);}

function lk($u,$t='',$c='',$o='',$id=''){if(!$t)$t=domain($u);
	$r=['href'=>$u,'id'=>$id,'class'=>$c]; if($o)$r['target']='_blank';
	return tag('a',$r,$t);}
function lkb($u,$t='',$c=''){if(!$t)$t=domain($u);
	return tag('a',['href'=>$u,'class'=>$c,'target'=>'_blank'],$t);}
function btj($t,$j='',$c='',$id=''){
	return tag('a',['id'=>$id,'class'=>$c,'onclick'=>$j.' return false;'],$t);}
function img($src,$w='',$h='',$c='',$s=''){
	if(is_numeric($w))$w.='px'; if(is_numeric($h))$h.='px';
	return tag('img',['src'=>$src,'width'=>$w,'height'=>$h,'style'=>$s,'class'=>$c],'','1');}

//forms
function input($id,$v,$s='',$h='',$num='',$n=''){$r=['id'=>$id];
	if($h==1)$r['placeholder']=$v; else $r['value']=$v; if($h && $h!=1)$r['placeholder']=$h; 
	if($s)$r['size']=$s; if($n)$r['maxlength']=$n;
	if($num){$j=atj('numonly',$id); $r['onclick']=$j; $r['onkeyup']=$j;}
	return tag('input',$r,'',1);}
function goodinput($id,$v){
	if(strlen($v)<20)return input($id,$v); else return textarea($id,$v,40,4);}
function inputcall($j,$id,$v,$s=''){
	$r=['id'=>$id,'placeholder'=>$v,'size'=>$s,'onkeyup'=>'callj(event,this)','data-j'=>$j];
	return tag('input',$r,'',1);}
function hidden($id,$v){return tag('input',['type'=>'hidden','id'=>$id,'value'=>$v],'',1);}
function password($id,$v,$sz='',$h=''){
	$r=['type'=>'password','id'=>$id,'value'=>$v,'size'=>$sz];
	if($h)$r['placeholder']=$h!=1?$h:$v;
	return tag('input',$r,'',1);}
function textarea($id,$v,$cols=70,$rows=7,$h='',$c='',$n=''){
	$r=['id'=>$id,'cols'=>$cols,'rows'=>$rows]; $op='';
	if($h)$r['placeholder']=$h!=1?$h:$v; if($c)$r['class']=$c;
	if($n){$js='strcount1(\''.$id.'\','.$n.')'; $r['onclick']=$js; $r['onkeyup']=$js;
		$op=' '.span($n-mb_strlen($v),'small','strcnt'.$id.'');}
	return tag('textarea',$r,$v).$op;}
function divarea($v,$c,$id,$s=''){$bt=build::wysiwyg($id);
	return $bt.tag('div',['id'=>$id,'class'=>$c,'style'=>$s,'contenteditable'=>'true'],$v);}
function cellarea($tag,$id,$v,$j){
	return tag($tag,['id'=>'d'.$id,'contenteditable'=>'true','onblur'=>atj('savecell',[$id,$j])],$v);}
function label($for,$v,$id=''){return tag('label',['for'=>$for,'id'=>$id],$v);}
function small($t){return tag('small','',$t);}
function iframe($f,$w='',$h=''){
return tag('iframe',['width'=>$w,'height'=>$h,'frameborder'=>'0','scrolling'=>'no','marginheight'=>'0','marginwidth'=>'0','src'=>$f],'',1);}
function goodir($f){if(substr($f,0,4)=='http')return $f; else return 'disk/'.$f;}
function video($f,$w='',$h=''){if(!$w)$w='100%'; if(!$h)$h='280px'; return '<video controls width="'.$w.'" height="'.$h.'"><source src="'.goodir($f).'" type="video/mp4"></video>';}
function audio($f,$id=''){return '<audio controls>
<source id="mp3'.$id.'" src="'.goodir($f).'" type="audio/mpeg"></audio>';}
function bar($id,$v=50,$step=10,$min=0,$max=100,$js='inn',$c=''){return '<input type="range" id="'.$id.'" min="'.$min.'" max="'.$max.'" step="'.$step.'" value="'.$v.'" onmousemove="'.$js.'(this.value,\'lbl'.$id.'\');"/>'.label($id,$v,'lbl'.$id);}
function progress($n){return '<progress value="'.$n.'" max="100"></progress>';}

function select($id,$r,$slct='',$o=''){$ret='';
	if($r)foreach($r as $k=>$v){
		if($o==1)$k=is_numeric($k)?$v:$k; if($o==2)$v=lang($v);
		if($k==$slct)$chk='selected'; else $chk='';
		$ret.=tag('option',['value'=>$k,'selected'=>$chk],$v);}
	return tag('select',['id'=>$id],$ret);}
function selectj($id,$r,$j,$o=''){$ret='';
	if($r)foreach($r as $k=>$v){
		if($o==1)$k=is_numeric($k)?$v:$k; if($o==2)$v=lang($v);
		$ret.=tag('option',['value'=>$k,'onchange'=>$j],$v);}
	return tag('select',['id'=>$id],$ret);}
function radio($d,$r,$ck,$o='',$lg=''){$ret='';
	foreach($r as $k=>$v){
		if($o)$k=is_numeric($k)?$v:$k; $chk=$k==$ck?'checked':''; if($lg)$v=lang($v);
		$atb=['type'=>'radio','name'=>$d,'id'=>$d.'-'.$k,'value'=>$k,'checked'=>$chk];
		$ret.=tag('input',$atb,'',1).label($d.'-'.$k,$v).' ';}
	return $ret;}
function checkbox($d,$r,$ck='',$o=' '){$ret='';
	foreach($r as $k=>$v){
		if($o==1)$k=is_numeric($k)?$v:$k; $chk=$k==$ck?'checked':''; if($o==2)$v=lang($v);
		$atb=['type'=>'checkbox','name'=>$d,'id'=>$d.'-'.$k,'value'=>$v,'checked'=>$chk];
		$ret.=tag('input',$atb,'',1).label($d.'-'.$k,$v).' ';}
	return $ret;}
function datalist($id,$r,$v,$s=16,$t=''){$ret=''; $opt='';
	if($t)$ret=label($id,$t);
	$ret.=tag('input',['id'=>$id,'list'=>'dt'.$id,'size'=>$s,'value'=>$v],'',1);
	foreach($r as $v)$opt.=tag('option','value='.$v,'',1);
	$ret.=tag('datalist',['id'=>'dt'.$id],$opt);
	return $ret;}
function input_label($id,$v,$t){return div(label($id,$t).input($id,$v));}
function input_row($id,$bt,$t){return div(div(label($id,lang($t)),'cell2').div($bt,'cell'),'row');}

//aj
function aj($call,$t,$c='',$r=''){//wait for data-jb/-prmtm/-toggle
	$onc=isset($r['onclick'])?' '.$r['onclick']:''; $r['data-j']=$call;
	$r['onclick']='ajbt(this);'.$onc; //$r['onTouchStart']='ajbt(this);'.$onc;
	if($c)$r['class']=$c;//onmousedown not mobile 
	//$r['onmousedown']='nsf();'; $r['onselectstart']='nsf();'; 
	if(auth(6) && !isset($r['title']))$r['title']=$call;
	return tag('a',$r,$t);}
function ajs($call,$p='',$inp=''){$p=$p?_jr(explode(',',$p)):'';
	return atj('ajaxCall',[$call,$p,$inp]);}
function popup($call,$t,$c=''){return aj('popup,,,1|'.$call,$t,$c);}
function pagup($call,$t,$c=''){return aj('pagup,,,1|'.$call,$t,$c);}
function imgup($f,$t,$c=''){return aj('imgup|core,img|f='.$f,$t,$c);}
function bubble($call,$t,$c='',$o=''){$id=randid('bb');//,['id'=>$id]
	return span(aj('bubble,'.$id.','.$o.'|'.$call,$t,$c),'',$id);}
function toggle($call,$t,$c='',$o=''){//need container for close others
	$id=randid('tg'); $div=before(before($call,'|',1),',',1);
	return aj($call,$t,$c.($o?' active':''),['id'=>$id,'data-toggle'=>$div,'rel'=>$o]);}
function autoggle($id,$call,$t,$c=''){if(!$id)$id=randid('tg');
	return toggle($id.'|'.$call,$t,$c).div('','tgbox',$id);}
function ajtime($call,$prm,$t,$c=''){$id=randid('bbt');
	$r['onmouseover']='ajaxTimer(\'bubble,'.$id.',1|'.$call.'\',\''.$prm.'\'); zindex(\''.$id.'\');';
	$r['onmouseout']='clearTimeout(xc);'; $r['onmouseup']='clearTimeout(xc);';
	return span(aj('bubble,'.$id.',1|'.$call,$t,$c,$r),'',$id);}
	
//icons
function ico($d,$s='',$c='',$t='',$ti='',$tb='',$id=''){
	if(is_numeric($s))$s='font-size:'.$s.'px'; if($s)$r['style']=$s; 
	$r['class']='pic fa fa-'.$d.($c?' '.$c:''); if($id)$r['id']=$id; 
	if($t)$t=lang($t); elseif($ti)$r['title']=lang($ti); elseif($tb)$t=' '.$tb;
	return tag('span',$r,'').$t;}
function icxt($d,$t,$c='',$s=''){return span(ico($d,$s).$t,$c);}
function icit($d,$t,$c='',$s=''){return ico($d,$s,$c,'',$t);}
//function icid($d,$t,$id){return ico($d,'','',$t,'','',$id);}
function ics($d,$o=''){return icon_get($d,$o);}
function pic($d,$s='',$c=''){return ico(ics($d),$s,$c);}
//lang
function lang($d,$o='',$no=''){return lang_get($d,$o,$no);}//ucfirst
function langc($d,$c=''){return span(lang_get($d),$c);}
function langp($d,$s=''){return ico(ics($d),'','ico').lang($d);}
function langpi($d){return ico(ics($d),'','','',$d);}
function langph($d){return ico(ics($d),'','ico').span(lang($d),'react');}
function langs($d,$n,$o=''){return lang($d.($n>1?'s':''),$o);}
//philum
function picto($d,$s='',$c=''){if($c)$c=' '.$c; if(is_numeric($s))$s='font-size:'.$s.'px;';
	return span('','philum ic-'.$d.$c,'',$s);}
//helps
function help($d,$c='',$b=''){return helpget(['ref'=>$d,'css'=>$c,'conn'=>$b]);}
function hlpbt($d,$t='',$c='btn'){return bubble('core,help|ref='.$d,$t?$t:ico('question-circle-o'),$c);}
function hlpxt($d){return helpget(['ref'=>$d,'brut'=>1]);}
function hlpic($d,$s){return pic($d,$s).helpget(['ref'=>$d,'brut'=>1]);}

//strings
function delbr($d,$o=''){return str_replace(['<br />','<br/>','<br>'],$o,$d);}
function deln($d,$o=''){return str_replace("\n",$o,$d);}
function delsp($d){return str_replace("&nbsp;",' ',$d);}
function stripAccents($d){
	$a='¿¡¬√ƒ≈∆«»… ÀÃÕŒœ–—“”‘’÷ÿŸ⁄€‹›ﬁﬂ‡·‚„‰ÂÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ¯˘˙˚˝˝˛ˇ';
	$b='aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby';
	return strtr($d,$a,$b);}
function normalize($d){
	$d=str_replace([' ',"'",'"','?','/','ß',',',';',':','!','%','&','$','#','_','+','=','!',"\n","\r","\0","[\]",'~','(',')','[',']','{','}','´','ª',"&nbsp;",'-','.'],'',($d));
	return stripAccents($d);}
function ucfirst_b($d){$a=substr($d,0,1); //$a=stripAccents(utf8_decode($a));
return strtoupper($a).substr($d,1);}

function del_p($d){$d=str_replace(['<p>','</p>'],"\n",$d);
	return str_replace('<br>',"\n",$d);}
function clean_firstspace($d){$r=explode("\n",$d);
	foreach($r as $v)$rb[]=trim($v); return implode("\n",$rb);}
function clean_n($ret){
	$ret=str_replace("\r","\n",$ret);
	$ret=preg_replace('/( ){2,}/',' ',$ret);
	$ret=preg_replace('/(\n){2,}/',"\n\n",$ret);
	return $ret;}

function ptag($d){$r=explode("\n\n",$d); $ret=''; $ex='<h1<h2<h3<h4<br<hr<bl<pr<di<sp<if';
	foreach($r as $k=>$v){$v=trim($v); if($v){$cn=substr($v,0,3);
		if(strpos($ex,$cn)===false)$ret.='<p>'.$v.'</p>'; else $ret.=$v;}}
	$ret=preg_replace('/(\n){2,}/',"\n",$ret); $ret=nl2br($ret); return $ret;}

//php
function combine($a,$b){$n=count($a); $r=[];
	for($i=0;$i<$n;$i++)$r[$a[$i]]=isset($b[$i])?$b[$i]:''; return $r;}
function merge($r,$rb){if(is_array($r) && $rb)return array_merge($r,$rb);
	elseif($rb)return $rb; else return $r;}
function after($v,$s,$o=''){//last by default
	if(strpos($v,$s)===false)return $v; $v=$o?strchr($v,$s):strrchr($v,$s); 
	return substr($v,1);}
function before($v,$s,$o=''){//last by default
	$n=$o?strpos($v,$s):strrpos($v,$s); if($n===false)return $v;
	return substr($v,0,$n);}
function segment($v,$s,$e){$pa=strpos($v,$s); $ret='';
	if($pa!==false){$pa+=strlen($s); $pb=strpos($v,$e,$pa);
		if($pb!==false)$ret=substr($v,$pa,$pb-$pa); else $ret=substr($v,$pa);}
	return $ret;}
function portion($d,$a,$b,$na='',$nb=''){
	$pa=$na?strrpos($d,$a):strpos($d,$a); $pb=$nb?strrpos($d,$b):strpos($d,$b);
	return substr($d,$pa+1,($pb-$pa-1));}
function slice($d,$a,$n=''){$p=$n?strrpos($d,$a):strpos($d,$a);
	return [substr($d,0,$p),substr($d,$p+1)];}
function implode_array($r,$line,$col){
	foreach($r as $k=>$v)$ret[]=implode($col,$v);
	return implode($line,$ret);}
function explode_array($d,$line,$col){$r=explode($line,$d);
	if($r)foreach($r as $k=>$v)$ret[]=explode($col,$v);
	return $ret;}
function explode_array_keys($d,$line,$col){$r=explode($line,$d);
	if($r)foreach($r as $k=>$v){list($ka,$va)=explode($col,$v); $ret[$ka]=$va;}
	return $ret;}
function in_array_k($r,$d){foreach($r as $k=>$v)if($v==$d)return $k;}
function max_k($r){$mx=0; foreach($r as $k=>$v)if($v>$mx){$mx=$v; $mk=$k;} return $mk;}
function scandir_b($d){$r=scandir($d);
if($r[0]=='.')unset($r[0]); if($r[1]=='..')unset($r[1]); return $r;}

//parse
function substrpos($v,$a,$b){return substr($v,$a+1,$b-$a-1);}
function lastagpos($v,$ab,$ba){$d=substrpos($v,$ab,$ba);
$nb_aa=substr_count($d,'{'); $nb_bb=substr_count($d,'}'); $nb=$nb_aa-$nb_bb;
if($nb>0){for($i=0;$i<$nb;$i++)$ba=strpos($v,'}',$ba+1); $ba=lastagpos($v,$ab,$ba);}
return $ba;}

function accolades($d){
$pa=strpos($d,'{'); $d=substr($d,$pa+1);
$pb=strpos($d,'}'); $db=substr($d,0,$pb+1);
$na=substr_count($db,'{'); $nb=substr_count($db,'}'); $n=$nb-$na.' ';
if($na>$nb)$pb=lastagpos($d,$pa,$pb);
return substr($d,0,$pb);}

function innerfunc($d,$func){
$na=strpos($d,'function '.$func); $d=substr($d,$na);
$na=strpos($d,'('); $nb=strpos($d,')');
//$vars=substr($d,$na+1,$nb-$na-1);
$d=substr($d,$nb+1);
return accolades($d);}

//mecanics
function strprm($v,$s,$n){$r=explode($s,$v); return $r[$n];}
function randid($p=''){return $p.substr(microtime(),2,6);}
function http($d){return substr($d,0,4)!='http'?'http://'.$d:$d;}
function nohttp($d){return str_replace(['https','http','://','www.'],'',$d);}
function domain($d){$d=nohttp($d); return before($d,'/',1);}
function reload($u=''){echo tag('script','','window.location='.($u?$u:'document.URL'));}
function is_img($d){$n=strrpos($d,'.'); $xt=substr($d,$n);
if($xt=='.jpg' or $xt=='.png' or $xt=='.gif' or $xt=='.jpeg')return true;}
function ext($d){$a=strrpos($d,'.'); if($a)$d=strtolower(substr($d,$a));
$b=strrpos($d,'/'); if($b)$d=substr($d,0,$b); if(strlen($d<6))return $d;}
function b36($n,$o=''){return base_convert($n,$o?36:10,$o?10:36);}
function row($r){$ret=''; foreach($r as $v)$ret.=div($v,'cell'); return div($ret,'row');}

//controls
function auth($n){if(ses('uid') && ses('auth')>=$n)return true;}
function val($r,$d,$b=''){if(!isset($r[$d]))return $b; elseif($r[$d]===false)return $b;
	return $r[$d]=='memtmp'?memtmp($d):$r[$d];}
function vals($p,$r){foreach($r as $k=>$v)$ret[$v]=val($p,$v); return $ret;}
function valk($p,$r){foreach($r as $k=>$v)$ret[]=val($p,$v); return $ret;}
function prm($p){foreach($p as $k=>$v)$ret[]=$k.'='.$v; if($ret)return implode(',',$ret);}
function mkprm($p){foreach($p as $k=>$v)$ret[]=$k.'='.$v; if($ret)return implode('&',$ret);}
function get($d){if(isset($_GET[$d]))return urldecode($_GET[$d]);}
function post($d,$v=false){if($v!==false)$_POST[$d]=$v; if(isset($_POST[$d]))return $_POST[$d];}
function cookie($d,$v=''){if($v)setcookie($d,$v,time()+86400);
	elseif(isset($_COOKIE[$d]))$v=$_COOKIE[$d]; return $v;}
function ses($d,$v=''){if($v)$_SESSION[$d]=$v; return isset($_SESSION[$d])?$_SESSION[$d]:'';}
function sez($d,$v=''){return $_SESSION[$d]=$v;}
function sesif($d,$v=''){return isset($_SESSION[$d])?$_SESSION[$d]:ses($d,$v);}
function sesr($d,$k,$v=''){
	if(!isset($_SESSION[$d]) || !array_key_exists($d,$_SESSION))$_SESSION[$d]=[];
	if(!isset($_SESSION[$d][$k]) || !array_key_exists($k,$_SESSION[$d]))$_SESSION[$d][$k]=$v;
	elseif($v)$_SESSION[$d][$k]=$v;
	return $_SESSION[$d][$k];}
function sesrif($d,$k,$v=''){$ret=sesr($d,$k); if(!$ret)$ret=sesr($d,$k,$v); return $ret;}
function sesrz($d,$k,$v=''){if($v)return $_SESSION[$d][$k]=$v; else unset($_SESSION[$d][$k]);}
function sesfunc($d,$v='',$z=''){$ret=ses($d);
if((!$ret or $z) && function_exists($d))$ret=ses($d,$d($v)); return $ret;}
function sesclass($d,$met,$p='',$z=''){$v=$d.$met.$p;
	if(!ses($v) or $z)sez($v,$d::$met($p)); return ses($v);}
function sestg($d){return ses($d)?sez($d,0):ses($d,1);}

//conn
function readconn($d){$p=strrpos($d,':');//p*o:connector
if($p!==false)$r=[substr($d,0,$p),substr($d,$p+1)]; else $r=[$d,''];
$p=explode('*',$r[0]); return [$p[0],isset($p[1])?$p[1]:'',$r[1]];}

function atbr($d){$ret=''; $r=explode(',',$d);//make k="v" from k=v,
	if($r)foreach($r as $v)if(strpos($v,'=')){
		list($ka,$va)=explode('=',$v); $ret.=atb($ka,$va);}
	return $ret;}
function tagb($tag,$p,$t=''){//for vue
	if(trim($t))return '<'.$tag.atbr($p).'>'.$t.'</'.$tag.'>';}

//function insertbt($t,$v,$id){return btj($t,'insert(\'['.$v.']\',\''.$id.'\')','btok');}
function insertbt($t,$v,$id){return tlex::publishbt($t,$v,$id);}

//amt
function memtmp($d){
	if(isset($_SESSION['mem'][$d])){ksort($_SESSION['mem'][$d]);
	$ret=implode('',$_SESSION['mem'][$d]); unset($_SESSION['mem'][$d]);
	return (jurl($ret,1));}}//utf8_decode

//ajaxencode
function jurl($d,$o=''){
	$a=["\n","\t",'\'','|',"'",'"','*','#','+','=','&','?','.',':',',','/','%u',' ','<','>'];
	$b=['(-n)','(-t)','(-asl)','(-bar)','(-q)','(-dq)','(-star)','(-dz)','(-add)','(-eq)','(-and)','(-qm)','(-dot)','(-ddot)','(-coma)','(-sl)','(-pu)','(-sp)','(-b1)','(-b2)'];
	return str_replace($o?$b:$a,$o?$a:$b,$d);}
function _jr($r){$ret='';//tostring
	if($r)foreach($r as $k=>$v)if($v)
		if(strpos($v,'=')){list($k,$v)=explode('=',$v); $ret[]=$k.'='.jurl($v);}
	if($ret)return implode(',',$ret);}
function _jrb($d,$o=''){$r=explode(',',$d); $ret='';//mkarray
	if($r)foreach($r as $v){$rb=explode($o?$o:':',$v); 
		if(isset($rb[1]))$ret[$rb[0]]=(jurl($rb[1],1));}
	return $ret;}
function unicode($d){
	if(strpos($d,'%u')===false)return $d; $n=strlen($d); $ret='';
	for($i=0;$i<$n;$i++){$c=substr($d,$i,1);
	if($c=='%'){$i++; $cb=substr($d,$i,1);
		if($cb=='u'){$i++; $cc=substr($d,$i,4); $i+=3; $ret.='&#'.hexdec($cc).';';}
		else $ret.=$c.$cb;}
	else $ret.=substr($d,$i,1);}
return $ret;}
function unicode2($d){return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/',function($match){return mb_convert_encoding(pack('H*',$match[1]),'UTF-8','UCS-2BE');},$d);}
function ascii($v){if(is_numeric($v))$v='#'.$v; return '&'.$v.';';}
function protect($d,$o=''){$a='|'; $b='(bar)';
	return str_replace($o?$b:$a,$o?$a:$b,$d);}

//batch
function batch($r,$j,$vrf=''){$ret='';
if($r)foreach($r as $k=>$v)$ret.=aj(str_replace(['$k','$v'],[$k,$v],$j),$v,$v==$vrf?'active':'');
return div($ret,'lisb');}

//json
function json_r($r){
foreach($r as $k=>$v){
	if(is_array($v))$ret[]=json_r($v);
	elseif(is_numeric($k))$ret[]='"'.rawurlencode($v).'"';
	else $ret[]='"'.$k.'":"'.rawurlencode($v).'"';}
if(is_numeric($k))return '['.implode(',',$ret).']';
else return '{'.implode(',',$ret).'}';}

function utf_r($r,$o=''){
foreach($r as $k=>$v){
	if(is_array($v))$ret[$k]=utf_r($v,$o);
	else $ret[$k]=$o?($v):($v);}//utf8_decode//_b
return $ret;}

function json_dec($d){if($d)$r=json_decode($d,true); return utf_r($r,ses('enc'));}
function json_enc($r){$r=utf_r($r);
return json_encode($r,JSON_FORCE_OBJECT| JSON_HEX_TAG| JSON_HEX_APOS| JSON_HEX_QUOT| JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);}


//clr
function diez($d,$o=''){return $o?substr($d,1):'#'.$d;}
function rgb($d){return [hexdec(substr($d,0,2)),hexdec(substr($d,2,2)),hexdec(substr($d,4,2))];}
function rgb2($r,$o=0){return [$r[0]+$o,$r[1]+$o,$r[2]+$o];}
function rgba($d,$a=1){$r=rgb($d); return 'rgba('.$r[0].','.$r[1].','.$r[2].','.$a.')';}
function rgb2clr($r){$ret=''; for($i=0;$i<3;$i++){$d=dechex($r[$i]>0?$r[$i]:0);
	if(strlen($d)==1)$d='0'.$d; $ret.=$d=='0'||!$d?'00':$d;}
	return $ret;}
function clrb($p,$o=0){$r=rgb($p); $r=rgb2($r,$o); return rgb2clr($r);}
function clrneg($p,$o){if($o)return hexdec($p)<10000000?'ffffff':'000000';
	for($i=0;$i<3;$i++){$d=dechex(255-hexdec(substr($p,$i*2,2)));
	if(strlen($d)==1)$d='0'.$d; $ret.=$d=='0'||!$d?'00':$d;}
	return $ret;}

//utils
function host(){return 'http://'.$_SERVER['HTTP_HOST'];}
//function encode($d){return ses('enc')==1?utf8_encode($d):$d;}
function decode($d){return ses('enc')==1?utf8_decode($d):$d;}
function utf8_decode_b($d){return mb_convert_encoding($d,'HTML-ENTITIES','UTF-8');}
function ip(){return gethostbyaddr($_SERVER['REMOTE_ADDR']);}
function eco($d,$o=''){$d=utf8_decode_b($d); $ret=is_array($d)?pr($d,1):$d;
	if($o)return textarea('',$ret,40,20); else echo $ret;}
function chrono($d=''){$ret=''; static $start;
	if($start)$ret=round(array_sum(explode(' ',microtime()))-$start,5);
	$start=array_sum(explode(' ',microtime()));
	return tag('small','',$d.'+'.$ret);}

?>