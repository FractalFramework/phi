<?php
class freeapp{	
static $private=6;
static function call($p){$inp=val($p,'inp');
$r=sesfunc('scandir_b',ses('dev').'/app',0);
foreach($r as $k=>$v)if(file_exists(ses('dev').'/app/'.$v.'/'.$inp.'.php'))return lang('already used');
return lang('free');}
static function content($p){$p['inp']=val($p,'p');
$ret=input('inp',$p['inp'],'','1');
$ret.=aj('fa|freeapp,call|msg=text|inp',lang('verif'),'btsav');
return div($ret.span(self::call($p),'popbt','fa'),'pane');}
}