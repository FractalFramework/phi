<?php

class Build{
	
	#popup
	static function popup($content,$prm){
		$pwidth=val($prm,'pagewidth'); $width=val($prm,'popwidth');
		$style='min-width:320px;';
		$style.=' max-width:'.($pwidth<640?$pwidth:($width?$width:$pwidth-100)).'px;';
		$cl=picto('close',20); $min=picto('less',20); $rez=picto('ktop',20);
		//$cl=pic('close',16); $min=pic('window-minimize',16); $rez=pic('window-restore',16);
		$ret=tag('a',['class'=>'imbtn','onclick'=>'Close(\'popup\');'],$cl);
		$ret.=tag('a',['class'=>'imbtn','onclick'=>'Reduce(\'popup\');'],$min);
		$ret.=tag('a',['class'=>'imbtn','onclick'=>'Repos();'],$rez);		
		$app=val($prm,'appName'); $mth=val($prm,'appMethod');
		$lk=href('/app/'.$app,pic('link'),'black').' ';
		$title=$lk.$app;//.($mth?'::'.$mth:'');
		$title=val($prm,'title',$title);
		if($app && method_exists($app,'admin') && !$mth)
			$title.=Menu::call(['app'=>$app,'method'=>'admin']);
		$ret.=tag('span',['class'=>'imbtn'],$title);
		$header=tag('div',['id'=>'popa','class'=>'popa','onmouseup'=>'stop_drag(event); noslct(1);','onmousedown'=>'noslct(0);'],$ret);
		$content=tag('div',['id'=>'popu','class'=>'popu'],$content);
		return tag('div',['class'=>'popup','style'=>$style],$header.$content);
	}
	
	static function pagup($content){
		$close='';//span(btj(pic('close'),'Close(\'popup\');','btn'),'left');
		$content=tag('div',array('id'=>'popu','class'=>'pagu'),div($close.$content,'pgu'));
		return tag('div',array('class'=>'pagup'),$content);
	}
	
	static function imgup($content){
		$ret=tag('div',array('id'=>'popu','class'=>'imgu'),div($content,'imu'));
		//$ret=tag('a',array('onclick'=>'Close(\'popup\');'),$ret);
		return tag('div',array('class'=>'pagup'),$ret);
	}
	
	#bubble
	static function bubble($content){
		$content=tag('div',array('id'=>'popu','class'=>'bubu'),$content);
		return tag('div',array('class'=>'bubble','style'=>'max-width:320px'),$content);
	}
	static function menu($content){
		$content=tag('div',array('id'=>'popu','class'=>'bubu'),$content);
		return tag('div',array('class'=>'bubble','style'=>''),$content);
	}

	#deco
	static function offon($state){
		return pic($state?'toggle-on':'toggle-off');
	}
	
	#scroll
	static function scroll($elements,$content,$limit,$width='',$height=''){
	    $max=is_numeric($elements)?$elements:count($elements);
		$style='overflow-y:scroll; max-height:'.($height?$height:400).'px;'.($width?' min-width:'.$width.'px;':'');
	    if($max>$limit)return tag('div',array('id'=>'scroll','style'=>$style),$content); 
	    else return $content;
	}
	
	static function code($v,$o=''){
		$v=str_replace(array('<?php','?>'),'',$v);
		$v='<?php '.trim($v).' ?>';
		//$v=str_replace("\t",'',$v);
		$v=highlight_string($v,true);
		if($o)$v=str_replace(array('FF8000','007700','0000BB','DD0000','0000BB'),array('FF8000','00ee00','afafff','eeeeee','ffbf00'),$v);
		$v=str_replace(array('&lt;?php&nbsp;','&lt;?php','?&gt;'),'',$v);
		if(substr($v,0,6)=='<br />')$v=substr($v,6);
		if(substr($v,0,4)=='<br>')$v=substr($v,4);
		return trim($v);
	}
	
	//table
	static function table($array,$csa='',$csb='',$keys=''){$i=0; $tr='';
	    if(is_array($array))
		foreach($array as $k=>$v){$td=''; $i++;
	        $cs=$i==1?$csa:$csb;
	        $alterenateCss=$i%2?'r2':'r1';
			if($keys)$td.=tag('td',array('class'=>$cs),$k);
	        if(is_array($v))foreach($v as $ka=>$va)
	        	$td.=tag('td',array('class'=>$cs),$va);
			else $td.=tag('td',array('class'=>$cs),$v);
	        if($td)$tr.=tag('tr',array('id'=>$k,'valign'=>'top','class'=>$alterenateCss),$td);
	    }
	    return tag('table','',$tr);
	}
	
	//html table
	static function divTable($array,$csa='',$csb=''){$ret=''; $i=0;
	    if(is_array($array))foreach($array as $k=>$v){$td=''; $i++;
	        $cs=$i==1?$csa:$csb;
	        $alterenateCss=$i%2?'r2':'r1';
	        if(is_array($v))foreach($v as $ka=>$va)
	        	$td.=tag('span',array('class'=>'cell2 '.$cs),$va);
	        $ret.=tag('span',array('id'=>$k,'class'=>'row '.$alterenateCss),$td);}
	    return $ret;
	}
		
}

?>