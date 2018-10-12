<?php

class pad{

static function injectJs(){
	return 'if(localStorage["m2"]!="undefined")
		getbyid("txarea").innerHTML=localStorage["m2"];';}

static function headers(){
	add_head('csscode','.wrapper{width:600px; margin:0 auto;}');
	add_head('jscode','');
	add_head('jscode',self::injectJs());}

static function popup(){
	$bt=aj('pagup|pad',ico('window-maximize'));
	$ret['title']='NotePad';
	$ret['width']=640;
	return $ret;}

static function admin(){
	$r[]=['','pop','core,help|ref=pad_app','','help'];
	$r[]=['editors','pop','txt','','txt'];
	$r[]=['editors','pop','pad','','pad'];
	$r[]=['editors','pop','convert','','convert'];
	return $r;}

#content
static function content($p){$id='txarea';
	$ret=build::wysiwyg($id);
	$ret.=btj(langph('save'),atj('memStorage','txarea_m2_sav_1'),'btsav','');
	$ret.=btj(langph('restore'),'getbyid(\'txarea\').innerHTML=localStorage[\'m2\'];','btn','');
	$txt=val($p,'txt');
	$s='width:calc(100% - 10px); min-height:440px;';
	//$txt=conn::read(['msg'=>$txt,'ptag'=>1]);
	//$ret.=divarea(val($p,'txt'),$id,'txarea','');
	$ret.=tag('div',['id'=>$id,'class'=>'editarea','style'=>$s,'contenteditable'=>'true'],$txt);
	
	//$ret.=csscode(self::injectJs());
	return $ret;}
}

?>