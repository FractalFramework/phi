(function(){
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
})();