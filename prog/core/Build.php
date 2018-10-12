<?php
class build{
static function popup($d,$p){
$pw=val($p,'pagewidth'); $w=val($p,'popwidth',post('pw'));
$style='min-width:320px;';
$style.=' max-width:'.($pw<640?$pw:($w?$w:$pw)).'px;';
$cl=picto('close',20); $min=picto('less',20); $rez=picto('ktop',20);
//$cl=ico('close',16); $min=ico('window-minimize',16); $rez=ico('window-restore',16);
$ret=tag('a',['class'=>'imbtn','onclick'=>'Close(\'popup\');'],$cl);
$ret.=tag('a',['class'=>'imbtn','onclick'=>'Reduce(\'popup\');'],$min);
$ret.=tag('a',['class'=>'imbtn','onclick'=>'Repos();'],$rez);		
$app=val($p,'appName'); $mth=val($p,'appMethod');
$title=lk('/'.$app,ico('link'),'',1).' ';//titles
if(method_exists($app,'titles'))$title.=$app::titles($p); else $title.=$app.' ';
if($app && method_exists($app,'admin') && !$mth)//
	$title.=menu::call(['app'=>$app,'method'=>'admin']);
$ret.=tag('span',['class'=>'imbtn'],$title);
$header=tag('div',['id'=>'popa','class'=>'popa','onmouseup'=>'stop_drag(event); noslct(1);','onmousedown'=>'noslct(0);'],$ret);
$d=tag('div',['id'=>'popu','class'=>'popu'],$d);
return tag('div',['class'=>'popup','style'=>$style],$header.$d);}

static function pagup($d,$p){if(!$d)return;
if($w=val($p,'popwidth'))$d=div($d,'','','max-width:'.$w.'px');
//$close=span(btj(ico('close'),'Close(\'popup\');','btn'),'left');
$d=tag('div',['id'=>'popu','class'=>'pagu'],div($d,'pgu'));
return tag('div',['class'=>'pagup'],$d);}

static function imgup($d){
$ret=tag('div',['id'=>'popu','class'=>'imgu'],div($d,'imu'));
//$ret=tag('a',['onclick'=>'Close(\'popup\');'],$ret);
return tag('div',['class'=>'pagup'],$ret);}

static function bubble($d){
$d=tag('div',['id'=>'popu','class'=>'bubu'],$d);
return tag('div',['class'=>'bubble'],$d);}//,'style'=>'max-width:320px'

static function menu($d){
$d=tag('div',['id'=>'popu','class'=>'bubu'],$d);
return tag('div',['class'=>'bubble','style'=>''],$d);}

static function scroll($r,$n,$h=''){$max=count($r); $ret=implode('',$r);
$s='overflow-y:scroll; max-height:'.($h?$h.'px':400).';';
if($max>$n)return tag('div',['id'=>'scroll','style'=>$s],$ret); 
else return $ret;}

static function code($v,$o=''){
$v=str_replace(['<?php','?>'],'',$v);
$v='<?php '.trim($v).' ?>';
$v=highlight_string($v,true);
if($o)$v=str_replace(['FF8000','007700','0000BB','DD0000','0000BB'],['FF8000','00ee00','afafff','eeeeee','ffbf00'],$v);
$v=str_replace(['&lt;?php&nbsp;','&lt;?php','?&gt;'],'',$v);
if(substr($v,0,6)=='<br />')$v=substr($v,6);
if(substr($v,0,4)=='<br>')$v=substr($v,4);
return trim($v);}

static function editable($r,$j,$jb=''){$tr='';
if(is_array($r))foreach($r as $k=>$v){$td='';
	$tag=$k==='_'?'th':'td';
	$td.=tag($tag,'',popup($j.',k='.$k,pic('edit').$k));
	if(is_array($v))foreach($v as $ka=>$va){//$va=$k==='_'?($ka).'. '.$va:$va;
		if($jb)$td.=cellarea($tag,$k.'-'.$ka,$va,$jb);
		else $td.=tag($tag,['id'=>$k.'-'.$ka],$va);}
	else{if($jb)$td.=cellarea($tag,$k.'-1',$v,$jb);
		else $td.=tag($tag,'',$v);}
	if($td)$tr.=tag('tr',['id'=>'k'.$k],$td);}
$ret=tag('table','',$tr);
return tag('tbody','',$ret);}

static function recursive($r){$ret='';
foreach($r as $k=>$v)if(is_array($v))$ret.=li($k.ul(self::recursive($v)));
else $ret.=li($k.':'.$v);
return ul($ret);}

static function toggle($p){$v=$p['v']; $rid=randid('itg');
$yes=val($p,'yes','yes'); $no=val($p,'no','no');
if($v==1){$ic='on'; $t=$yes;}else{$ic='off'; $t=$no;}
$j=$rid.'|build,toggle|id='.$p['id'].',v='.($v==1?0:1); $j.=',yes='.$yes.',no='.$no;
return span(aj($j,ico('toggle-'.$ic,22).lang($v==1?$yes:$no)).hidden($p['id'],$v),'',$rid);}

static function leftime($end){$time=$end-ses('time');
if($time>86400)$ret=($n=floor($time/86400)).' '.langs('day',$n);
elseif($time>3600)$ret=($n=floor($time/3600)).' '.langs('hour',$n);
elseif($time>60)$ret=($n=floor($time/60)).' '.langs('minute',$n);
else $ret=$time.' '.langs('second',$time);
return span($ret,'small');}

static function wysiwyg($id){$ret='';
$r=['bold'=>'bold','italic'=>'italic','underline'=>'underline','insertUnorderedList'=>'list-ul','insertOrderedList'=>'list-ol','Indent'=>'indent','Outdent'=>'outdent','createLink'=>'link'];//'h'=>'font','JustifyLeft'=>'align-left','JustifyCenter'=>'align-center','inserthorizontalrule'=>'minus',
foreach($r as $k=>$v)
	//$ret.=tag('button',['onclick'=>atj('execom',[$k,$k=='h'?'h2':''])],ico($v,14));
	$ret.=tag('button',['onclick'=>atj('format',[$k,$id])],ico($v,14));
return div($ret,'connbt');}

static function connwsg($id){
$ret=btj('[]',atj('embed_slct',['[',']',$id]),'btn');
$r=['h','b','i','u','q','k','url','web'];
foreach($r as $k=>$v)$ret.=btj(lang($v,1),atj('embed_slct',['[',':'.$v.']',$id]),'btn');
$ret.=hlpbt('connectors');
return div($ret);}

static function genwsg($id){
$ret=btj('[]',atj('embed_slct',['[',']',$id]),'btn');
$r=['h','b','i','u','q','k','url','web'];
foreach($r as $k=>$v)$ret.=btj($v,atj('embed_slct',['[',':'.$v.']',$id]),'btn');
$ret.=hlpbt('genetics_app');
$ret.=self::appswsg($id);
return div($ret);}

static function appids($a){
return sql('id',$a::$db,'rv',['uid'=>ses('uid')]);}

static function select_appid($p){$ret='';
$a=val($p,'app'); $id=val($p,'id');
if(!class_exists($a))return ''; 
$app=new $a; $r=self::appids($a); echo $a;
if($r)foreach($r as $k=>$v){$bt=$app->tit(['id'=>$v]);
	$ret.=btj('['.$bt.']',atj('insert',['['.$v.':'.$a.']',$id]),'btn').'';}
return $ret;}

static function selectj($id,$r,$j,$o=''){$ret='';
	if($r)foreach($r as $k=>$v){
		if($o)$k=is_numeric($k)?$v:$k; if($o==2)$v=lang($v);
		$ret.=tag('option',['value'=>$k],$v);}
	//return tag('select','id=\''.$id.'\' onchange=ajaxCall(\''.$j.'\',\'app=\'+this.value)',$ret);
	return '<select id="slct'.$id.'" onchange="ajaxCall(\'div,'.$j.'\',\'app=\'+this.value+\',id='.$id.'\')">'.$ret.'</select>';}

static function appswsg($id){
$r=applist::folder('apps'); //pr($r);
array_unshift($r,lang('select'));
$j='slctapp|build,select_appid';
$ret=self::selectj($id,$r,$j,1);
$ret.=div('','','slctapp');
//$ret.=hlpbt('genetics_app');
return $ret;}

static function thumb($f,$dim){$dr='img/';
$fb='medium/'.$f; $med=is_file($dr.$fb);
if($dim=='mini' or $dim=='micro')$im='mini/'.$f;
elseif($dim=='medium')$im=$med?$fb:'full/'.$f; else $im='full/'.$f;
if(is_file($dr.$im) && filesize($dr.$im))return $dr.$im;}

static function mini($d){
$fa='img/mini/'.$d; $fb='img/full/'.$d;
if(is_file($fb))return imgup($fb,img('/'.$fa));}

}
?>