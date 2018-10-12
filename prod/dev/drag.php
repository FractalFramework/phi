<?php
//https://developer.mozilla.org/en-US/docs/Web/API/HTML_Drag_and_Drop_API
class drag{
static $private='1';

static function injectJs(){
	return '
function dragstart_handler(ev) {
 console.log("dragStart");
 // Add the target element\'s id to the data transfer object
  ev.datatransfer.setData("text/plain", ev.target.id);
  ev.datatransfer.setData("text/html", "<p>Example paragraph</p>");
  ev.datatransfer.setData("text/uri-list", "http://developer.mozilla.org");
  var img = new Image(); 
  img.src = \'http://tlex.fr/usr/img/poster1.png\'; 
  ev.datatransfer.setDragImage(img, 10, 10);
  ev.datatransfer.dropEffect = "copy";
 ev.dropEffect = "move";
}

function dragover_handler(ev) {
 ev.preventDefault();
 // Set the dropEffect to move
 ev.datatransfer.dropEffect = "move"
 ev.dropEffect = "move";
 //var data = ev.datatransfer.getData("text");
 //getElementById(data).className="dragover";
}

function drop_handler(ev) {
 ev.preventDefault();
 // Get the id of the target and add the moved element to the target\'s DOM
 var data = ev.datatransfer.getData("text");
 //ev.target.appendChild(document.getElementById(data));
 //getElementById(data).className="dragover";
 alert(data);
}';}
static function headers(){
	add_head('csscode','
.dragme {
    width: 64px;
    height: 64px;
    border: 1px solid #666;
    background: #acf;
    margin: 0.25em;
    padding: 0.25em;
    cursor: pointer;
}
.dropper {
    padding: 0.25em;
    width: 15ex;
    height: 15ex;
    border: 1px solid #666;
    background: #eee;
    margin: 0 0 0 15ex;
}
.dragover {
    background: #8f8;
}');
	add_head('jslink','/js/drag.js');
	//add_head('jscode',self::injectJs());
}

static function dragline($t,$c,$id){
	return tag('div',['id'=>$id,'name'=>$id,'class'=>$c,'draggable'=>'true','ondragstart'=>'dragstart_handler(event)','ondragover'=>'dragover_handler(event)','ondrop'=>'drop_handler(event)'],$t);}//,'ondragend'=>'end_handler(event)'

static function build($p){
	$r=['d1'=>'hello1','d2'=>'hello2','d3'=>'hello3'];
	$p1=val($p,'p1'); $p2=val($p,'p2'); $d1=''; $d2='';
	if($p1 && $p2){$d1=$r[$p1]; $d2=$r[$p2];}
	foreach($r as $k=>$v){
		if($k==$p1 && $d2)$rb[$k]=$d2;
		elseif($k==$p2 && $d1)$rb[$k]=$d1;
		else $rb[$k]=$v;
	}
	return $rb;}

//builder
static function play($p){
	$ret='';
	//$bt=tag('div',['id'=>'p1','class'=>'dragme','draggable'=>'true','ondragstart'=>'dragstart_handler(event)'],'#1');
	//$bt.=tag('div',['id'=>'target','class'=>'dropper','ondrop'=>'drop_handler(event)','ondragover'=>'dragover_handler(event)'],'#2');
	$r=self::build($p); //p($r);
	foreach($r as $k=>$v)$ret.=self::dragline($v,'dragme',$k);
	return $ret;}

static function call($p){
	return div(self::play($p),'','divlist');}

//interface
static function content($p){
	$p['rid']=randid('md');
	$ret=self::call($p);
	return div($ret,'',$p['rid']);}
}
?>
