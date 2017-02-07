<?php
//installation : set private=0;
class install{
	static $private=6;
	
	static function menubt($dr,$f){
		$app=before($f,'.');
		if(method_exists($app,'install')){
			//echo $app.' - ';
			$app::install();
			return $f.br();
		}
	}
	
	static function build($p){
		$r=Dir::walk('install','menubt','prod');
		return implode('',$r);
	}
	
	static function json($p){
		$table=val($p,'inp1','');
		$r=Sql::read('*',$table,'rr','');
		return Api::build($r);
	}
	
	static function content($p){
		$p['rid']=randid('md');
		$bt=hlpbt('install').' ';
		$bt.=aj($p['rid'].'|install,build',lang('install'),'btn');
		$bt.=href('/app/apisql',lang('databases'),'btn');
		return $bt.div('','',$p['rid']);
	}
}
?>
