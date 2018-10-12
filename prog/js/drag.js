function reorderdiv(id,tg){
	//console.log(id+'-'+tg);
	var div=getbyid(id).parentNode;
	var r=div.getElementsByTagName('div');
	//var d1=getbyid(id).innerhtml;
	//var d2=getbyid(tg).innerhtml;
	//console.log(d1);
	for(i=0;i<r.length;i++){
		if(r[i].id==id)var ida=r[i];
		if(r[i].id==tg)var idb=r[i];
	}
	for(i=0;i<r.length;i++){
		//if(r[i].id==id)div.replaceChild(r[i],idb);
		if(r[i].id==tg)div.replaceChild(r[i],ida);
	}
	//console.log("drop");
}

function end_handler(ev) {
	if(ev.datatransfer.dropEffect == 'move')
		ev.target.parentNode.removeChild(ev.target);
}

function drop_handler(ev) {
	ev.preventDefault();
	// Get the id of the target and add the moved element to the target\'s DOM
	var data=ev.datatransfer.getData("text");
	var div=ev.target.parentNode;
	div.appendChild(document.getElementById(data));
	//getElementById(data).className="dragover";
	//reorderdiv(data,ev.target.id);
	//ajaxCall('div,divlist|drag,play','p1='+data+',p2='+ev.target.id,'');
}

function dragover_handler(ev) {
	ev.preventDefault();
	// Set the dropEffect to move
	ev.datatransfer.dropEffect="move"
	//ev.dropEffect="move";
	var data=ev.datatransfer.getData("text");
	//console.log(data);
	//getElementById(data).className="dragover";
}

function dragstart_handler(ev){
	//console.log("dragStart");
	// Add the target element\'s id to the data transfer object
	ev.datatransfer.setData("text/plain", ev.target.id);
	//ev.datatransfer.setData("text/html", "<p>Example paragraph</p>");
	//ev.datatransfer.setData("text/uri-list", "http://developer.mozilla.org");
	/*var img=new Image(); 
	img.src='http://tlex.fr/img/mini/1494860473916.jpg'; 
	ev.datatransfer.setDragImage(img,10,10);*/
	ev.datatransfer.dropEffect="copy";
	//ev.dropEffect="move";
}