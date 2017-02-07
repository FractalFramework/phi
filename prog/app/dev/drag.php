<?php

class drag{
	static $private='1';
	static $db='telex';
	
	static function injectJs(){
		return "(function(){
	var dndHandler={
		//Propriété pointant vers l'élément en cours de déplacement
		draggedElement: null, 
		applyDragEvents: function(element){
			element.draggable=true;
			//Cette variable est nécessaire pour que l'événement « dragstart » ci-dessous accède facilement au namespace « dndHandler »
			var dndHandler=this; 
			element.addEventListener('dragstart', function(e){
				//On sauvegarde l'élément en cours de déplacement
				dndHandler.draggedElement=e.target; 
				//Nécessaire pour Firefox
				e.dataTransfer.setData('text/plain', ''); 
			});
		},
		applyDropEvents: function(dropper){
			dropper.addEventListener('dragover', function(e){
				//On autorise le drop d'éléments
				e.preventDefault(); 
				//Et on applique le style adéquat à notre zone de drop quand un élément la survole
				this.className='dropper drop_hover'; 
			});
			dropper.addEventListener('dragleave', function(){
				//On revient au style de base lorsque l'élément quitte la zone de drop
				this.className='dropper'; 
			});
			//Cette variable est nécessaire pour que l'événement « drop » ci-dessous accède facilement au namespace « dndHandler »
			var dndHandler=this; 
			dropper.addEventListener('drop', function(e){
				var target=e.target,
					//Récupération de l'élément concerné
					draggedElement=dndHandler.draggedElement, 
					//On créé immédiatement le clone de cet élément
					clonedElement=draggedElement.cloneNode(true); 
				//Cette boucle permet de remonter jusqu'à la zone de drop parente
				while (target.className.indexOf('dropper') == -1){
					target=target.parentNode;
				}
				//Application du style par défaut
				target.className='dropper';
				//Ajout de l'élément cloné à la zone de drop actuelle
				clonedElement=target.appendChild(clonedElement);
				//Nouvelle application des événements qui ont été perdus lors du cloneNode()
				dndHandler.applyDragEvents(clonedElement);
				//Suppression de l'élément d'origine
				draggedElement.parentNode.removeChild(draggedElement);
			});
		}
	};
	var elements=document.querySelectorAll('.draggable'),
		elementsLen=elements.length;
	for (var i=0; i < elementsLen; i++){
		//Application des paramètres nécessaires aux éléments déplaçables
		dndHandler.applyDragEvents(elements[i]);
	}
	var droppers=document.querySelectorAll('.dropper'),
		droppersLen=droppers.length;
	for (var i=0; i < droppersLen; i++){
		//Application des événements nécessaires aux zones de drop
		dndHandler.applyDropEvents(droppers[i]); 
	}	
})();";}
	static function headers(){
		Head::add('csscode','
.dropper {
margin:50px 10px 10px 50px;
width:400px;
height:250px;
background-color:#555;
border:1px solid #111;

-moz-border-radius:10px;
border-radius:10px;

-moz-transition:all 200ms linear;
-webkit-transition:all 200ms linear;
-o-transition:all 200ms linear;
transition:all 200ms linear;
}

.drop_hover {
-moz-box-shadow:0 0 30px rgba(0, 0, 0, 0.8) inset;
box-shadow:0 0 30px rgba(0, 0, 0, 0.8) inset;
}

.draggable {
display:inline-block;
margin:20px 10px 10px 20px;
padding-top:20px;
width:80px;
height:60px;
color:#3D110F;
background-color:#822520;
border:4px solid #3D110F;
text-align:center;
font-size:2em;
cursor:move;

-moz-transition:all 200ms linear;
-webkit-transition:all 200ms linear;
-o-transition:all 200ms linear;
transition:all 200ms linear;

-moz-user-select:none;
-khtml-user-select:none;
-webkit-user-select:none;
user-select:none;
}');
		Head::add('jslink','js/drag.js');
		//Head::add('jscode',self::injectJs());
	}
	static function admin(){
		$r[]=array('','j','popup|drag,content','plus',lang('open'));
		return $r;}
	static function install(){
		Sql::create('drag',array('mid'=>'int','mname'=>'var'),0);}//1=update
	static function titles($p){
		$d=val($p,'appMethod');
		$r['content']='welcome';
		$r['build']='drag';
		if(isset($r[$d]))return lang($r[$d]);}
	
	//builder
	static function build($p){
		
		$bt=div('#1','draggable');
		$bt.=div('#2','draggable');
		$ret=div($bt,'dropper');
		
		$bt=div('#3','draggable');
		$bt.=div('#4','draggable');
		$ret.=div($bt,'dropper');
	
		return $ret;}
	
	//interface
	static function content($p){
		$p['rid']=randid('md');
		return self::build($p);
		return div($ret,'',$p['rid']);}
}
?>
