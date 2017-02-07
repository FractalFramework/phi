<?php

class txt{

	static function injectJs(){
		return '
		if(localStorage["m1"]!=undefined)
			document.getElementById("txarea").value=localStorage["m1"];';
	}

	static function headers(){
	}
	
	static function popup(){
		$bt=Ajax::j('pagup|txt',pic('window-maximize'));
		$ret['title']='NotePad';
		$ret['wifth']=640;
		return $ret;
	}
	
	#content
	static function content($prm){$w=val($prm,'pagewidth');
		$ret=hlpbt('txt_app').' ';
		//$ret.=tag('a','id=ckb,class=btdel,onclick=memStorage(\'txarea_m1_res\');',lang('restore')).' ';
		$ret.=tag('a','id=ckc,class=btsav,onclick=memStorage(\'txarea_m1_sav\');',lang('save'));
		$inp=tag('textarea',array('id'=>'txarea','style'=>'min-width:310px; min-height:140px;'),'');
		$ret.=div($inp);
		$ret.=Head::jsCode(self::injectJs());
		return $ret;
	}
}

?>