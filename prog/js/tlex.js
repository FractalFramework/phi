//tlex
var activelive=0; var nbnew=0; var reloadtime=10000;
function lastelex(){var mnu=getbyid("tlxbck"); var firstchild=mnu.childNodes[0];
	if(firstchild)if(mnu){var first=firstchild.id; return id=first.substr(3);}}
function btaction(d,id,n=0){var bt=getbyid(id); if(typeof bt==null)return;
	if((d>=1 && n==0) || d>n){var nb=d; var sty="#0088e6;";}//bt.parentNode.className=""; 
	else{var nb=n?n:''; var sty="#333333;";}
	bt.innerHTML=nb; bt.style='background-color:'+sty;}
function recbt(nbnew){if(nbnew)var rn=nbnew.split('-');
	btaction(rn[0],'tlxrec');//new posts
	btaction(rn[1],'tlxntf');//notifications
	//btaction(rn[2],'tlxsub');//subscriptions
	btaction(rn[3],'tlxabs',rn[2]?getbyid("tlxabsnb").value:'');//follow
	btaction(rn[4],'tlxmsg');}//messages
function refresh(){var id=lastelex(); var prmtm=getbyid("prmtm").value;//String()
	if(id && prmtm)ajaxCall("returnVar,nbnew|tlex,refresh","since="+id+","+prmtm);
	if(nbnew)setTimeout("recbt(nbnew)",200);}
function tlexlive(ok){if(ok)activelive=0;
	if(activelive){refresh(); xa=setTimeout("tlexlive(0)",reloadtime);}}
addEvent(document,"scroll",function(event){loadscroll("tlex,read","tlxbck")});

//search
function search2(id){
	var d=getbyid(id).value; if(d){getbyid('prmtm').value='srh='+d;
	ajaxCall("div,tlxbck|tlex,search_txt","",id);}}
function Search(old,id){
	var ob=getbyid(id); if(ob!=null)var src=ob.value;
	if(!src||src.length<2)return;
	if(src!=old){if(!old)return SearchT(id); else return;}
	if(src)search2(id);}
function SearchT(id){var ob=getbyid(id); 
	if(ob!=null)var old=ob.value; else var old='';
	setTimeout(function(){Search(old,id)},1000);}