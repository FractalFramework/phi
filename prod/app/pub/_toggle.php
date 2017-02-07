<?php

/**
 * Javascrip menu Accordion 
 * - toggle: open/close menu
 * - controller: close other menus
 * - needed class 'menu'
*/

class _toggle{
	static $private='2';
	
	//used to retro-inject js in current page where calling this app in a popup
	static function injectJs(){
		return '
	    function toggleList(id,randid,obj){
	        var mnu=document.getElementById("toggle"+randid).getElementsByClassName(obj);
	        for(i=0;i<mnu.length;i++){
	            if(mnu[i].id=="togg_"+id){
	                var ul=mnu[i].nextElementSibling;
	                if(mnu[i].className=="menu active"){
	                    mnu[i].className=obj;
	                    ul.style.display="none";
	                }
	                else{
	                    mnu[i].className="menu active";
	                    ul.style.display="block";
	                }
	            }
	            else {
	                mnu[i].className=obj;
	                ul=mnu[i].nextElementSibling;
	                ul.style.display="none";
	            }
	        }
	    }';}
	
	static function headers(){
		Head::add('csscode','
	    body {text-rendering:optimizeLegibility;}
	    a {cursor:pointer; color:black; text-decoration:none;
	    	transition: all 0.3s ease; -webkit-transition: all 0.3s ease;
	    }
	    list-type-style:none;
	    li {padding:16px;}
	    li a {display:block; padding:10px;}
	    li a:hover, li a:active {background:#ddd;}
	    ul ul li a:hover, li a:active {background:#ddd;}
	    li.active a {background:#ccc; text-shadow:1px 1px 2px gray;}
	    
	    /*crossbrowsers*/
	    ul {
	    list-style-type: none;
	    padding: 0px;
	    margin: 0px;
	    }
	    ul ul {
	        border:1px solid #ccc;
	    }
	    ul li {
	        /*background-image: url(icons/png/caret-right-2x.png);*/
	        background-repeat: no-repeat;
	        background-position: 0px center;
	        padding-left:0px;
	    }
	    ul ul li {
	    padding-left:0px;
	    }
	    ul ul li a {
	    padding-left:20px;
	    }
	');
		Head::add('jscode',self::injectJs());
	}
	
	//content
	static function toogleJsDatas(){
		return array('Menu 1'=>array('submenu 1'=>1),'Menu 2'=>array('submenu 1'=>1,'submenu 2'=>1,'submenu 3'=>1,'submenu 4'=>1),'Menu 3'=>array('submenu 1'=>1,'submenu 2'=>1,'submenu 3'=>1,'submenu 4'=>1,'submenu 5'=>1,'submenu 6'=>1,'submenu 7'=>1,'submenu 8'=>1,'submenu 9'=>1,'submenu 10'=>1));
	}

	static function toogleButtonFirstLevel($txt,$id,$randid){
		$ahref=tag('a',array('onclick'=>'toggleList(\''.$id.'\',\''.$randid.'\',\'menu\'); fixdiv(\'column\'); return false;'),$txt);
		return tag('li',array('class'=>'menu','id'=>'togg_'.$id),$ahref);
	}

	static function toogleButtonSecondLevel($txt,$n){
		$li=tag('a',array('onclick'=>'return false'),$txt);
		return tag('li',array('class'=>''),$li);
	}

	static function content($prm){
		$i=0; $ret='';
		$r=self::toogleJsDatas();
		$randid=randid();//uniqid();
		foreach($r as $k=>$v){
			$i++;
			$ret.=self::toogleButtonFirstLevel($k,$i,$randid);
			if(is_array($v)){
				$rt='';
				foreach($v as $ka=>$va)
					$rt.=self::toogleButtonSecondLevel($ka,$i);
				$rt=Build::scroll($v,$rt,6,'','240');
				$ret.=tag('ul',array('style'=>'display:none;'),$rt);
			}
		}
		$ret=tag('ul',array('id'=>'toggle'.$randid),$ret);
		$ret=tag('div',array('class'=>'Alegreya'),$ret);
		return $ret;
	}
}
?>