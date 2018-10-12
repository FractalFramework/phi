<?php
class install{
//while installation, set private=0;, and idem to upsql.php
static $private=0;//!!!!!

static function menubt($dr,$f){
	$app=before($f,'.');
	if(method_exists($app,'install')){
		$q=new $app; $q::install('');
		return $f.br();}}

static function build($p){
	$r=walk('install','menubt','prod');
	return implode('',$r);}

static function json($p){
	$table=val($p,'inp1','');
	$r=sql('*',$table,'rr','');
	return json_encode($r);}

static function content($p){
	$p['rid']=randid('md');
	$bt=hlpbt('install').' ';
	$bt.=aj($p['rid'].'|install,build',langp('install'),'btn');
	$bt.=aj($p['rid'].'|upsql',langp('databases'),'btn');
	$bt.=lk('/update',langp('updates'),'btn');
	return $bt.div('','',$p['rid']);}
}
?>
