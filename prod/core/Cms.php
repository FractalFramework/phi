<?php

class Cms{
	
	static function edition($id){$ret='';
		$r=array('bold'=>pic('bold'),'italic'=>pic('italic'),'underline'=>pic('underline'),'insertUnorderedList'=>pic('list'),'Indent'=>pic('indent'),'Outdent'=>pic('outdent'));
		foreach($r as $k=>$v)$ret.=tag('button','onclick=document.execCommand(\''.$k.'\',false,null);',$v).'';
	return $ret;}

	static function com($p){
		$rid=randid('txt'); $j='';
		$ret=self::edition($rid);
		$prm=array('contenteditable'=>'true','id'=>$rid,'onkeydown'=>$j,'onclick'=>$j);
		$ret.=tag('div',$prm,val($p,'txt'));
		return $ret;
	}

}
?>