//ph1.fr GNU/GPL
var wait=0,popnb=0,curid=0,cpop=0,cpop_difx=0,cpop_dify=0,popz=1,fixpop=1,amt=0,cutat=2000;

function AJAX(aUrl,aMethod,aTarget,aOption,aPost){
if(aUrl!=undefined)this.mUrl=aUrl;
if(aTarget!=undefined)this.targetId=aTarget;
if(aOption!=undefined)this.ajaxOption=aOption;
if(aMethod!=undefined)this.method=aMethod; else this.method=0;
if(this.mRequest!=undefined){this.mRequest.abort(); delete this.mRequest;}
this.mRequest=this.createReqestObject();
var m_This=this;
this.mRequest.onreadystatechange=function(){m_This.handleResponse();}
this.mRequest.open('POST',this.mUrl,true);
this.mRequest.send(aPost?aPost:null);}

AJAX.prototype.mUrl=undefined;
AJAX.prototype.targetId=undefined;
AJAX.prototype.mRequest=undefined;

AJAX.prototype.createReqestObject=function(){var req;
try{req=new XMLHttpRequest();}//all
catch(error){try{req=new ActiveXObject("Microsoft.XMLHTTP");}//IE
	catch(error){try{req=new mUrliveXObject("Msxml2.XMLHTTP");}//IE
		catch(error){req=false;}}}
return req;}

//ajaxOption: string=bubbles options; load, y=repos
AJAX.prototype.handleResponse=function(){
if(this.mRequest.readyState==4){wait=0;
	var method=this.method; var target=this.targetId; var option=this.ajaxOption;
	//if(option=='load')wait=0;
	if(this.mRequest.status=="200"){
		var result=this.mRequest.responseText;
		if(method!='returnVar' && target)var content=getbyid(target);
		if(option=='x' || option=='xy')Close('popup');
		if(target=='socket')content='';
		else if(method=='div')content.innerHTML=result;
		else if(method=='popup')popUp(result);
		else if(method=='pagup')pageUp(result);
		else if(method=='imgup')pageUp(result,1);
		else if(method=='bubble')bubble(result,target,option);
		else if(method=='menu')menu(result,target,option);
		else if(method=='input')content.value=result;
		else if(method=='after')addiv(target,result,method);
		else if(method=='before')addiv(target,result,method);
		else if(method=='begin')addiv(target,result,method);
		else if(method=='atend')addiv(target,result,method);
		else if(method=='injectJs')addjs(result);
		else if(method=='returnVar')window[target]=result;
		else if(method=='loadjs')setTimeout(target(result),100);
		else if(method=='reload'){//loged_ok
			if(result==option)setTimeout('window.location=document.URL',100);
			else content.innerHTML=result;}
		else if(method=='mem')amt_ending();
		if(option=='xy' || option=='y')Repos();
		if(option=='xx')setTimeout("Close('popup')",4000);
		//if(this.ajaxOption=='select')selectindiv(target);
	}
	else if(this.onError!=undefined){
		this.onError({status:this.mRequest.status,
		statusText:this.mRequest.statusText});
	}
	delete this.mRequest;
}
else if(this.ajaxOption=='load' && wait==0){wait=1;
	//var percent=this.loaded/this.total*100;
	if(this.method=='div')waitmsg(getbyid(this.targetId));}}

//composants
function waitmsg(div){div.innerHTML='Loading...';}
function getbyid(id){return document.getElementById(id);}
function addEvent(obj,event,func){if(obj!=undefined)obj.addEventListener(event,func,true);}
function innersizes(){return {w:parseInt(window.innerWidth)-18,h:parseInt(window.innerHeight)};}

function jra(val){var r=new Object; var ra=val.split(',');
for(i=0;i<ra.length;i++){var rb=ra[i].split('='); r[rb[0]]=rb[1];}
return r;}
function jrb(ra){var rb=[];
for(var key in ra)rb.push(key+':'+ra[key]);
return rb.join(',');}

//ajaxCall
function ajaxCall(call,params,inputs){na=0;
if(params){var prm=jra(params);} else var prm=new Object();
if(typeof xc!='undefined')clearTimeout(xc);//stop pending actions
var p=call.split('|');
var com=p[0].split(',');
var app=p[1].split(',');
//callbackMethod
var cbMethod=com[0];//div,popup,bubble,etc...
var cbId=com[1]!=undefined?com[1]:'';//id of callback
var cbOption=com[2]!=undefined?com[2]:'';//loading,fading,...
var cbJs=com[3]!=undefined?com[3]:'';//id of code to retroinjection in headers
//component
var appName=app[0];
var appMethod=app[1]!=undefined?'&appMethod='+app[1]:'';
//inputs
if(inputs)var inp=inputs.split(','); else $inp='';
if(inp!=undefined && inp!=null){
	for(var i=0;i<inp.length;i++){
		if(inp[i]){
			var el=getbyid(inp[i]);//content value
			if(el==null){var el=document.getElementsByName(inp[i]);//checklists
				for(var io=0;io<el.length;io++)if(el[io].checked)content=el[io].value;}
			else var content=ajaxCaptures(el);
			if(content!=undefined)prm[inp[i]]=jurl(content);
			if(content.length>cutat){na=multithread(content); prm[inp[i]]='memtmp';}//amt
		}}}
//load operation
if(getbyid('page'))prm['pagewidth']=getbyid('page').offsetWidth-40;
//var 
var str=jrb(prm);
//str=encodeURI(str);
//var host='http://'+window.location.hostname;host+
var url='/call.php?appName='+appName+appMethod+'&params='+str+'&'+cbMethod+'==';
//send
if(na){mem=[url,cbMethod,cbId,cbOption]; return;}
else var ajax=new AJAX(url,cbMethod,cbId,cbOption);
//post actions
if(cbOption=='reload')setTimeout('window.location=document.URL',100);
if(cbOption=='resetform')for(i=0;i<inp.length;i++){getbyid(inp[i]).value='';getbyid(inp[i]).innerHTML='';}
if(cbOption=='resetdiv')getbyid(inp[0]).innerHTML='';
if(cbOption=='js')addjs(getbyid(com[4]).value);
//retro-injection in headers
if(cbJs){
	if(cbJs=='injectJs' || cbJs=='1'){
		var url='/call.php?appName='+appName+'&appMethod=injectJs';
		var ajax=new AJAX(url,'injectJs','','2');}
	else if(cbJs=='resetscroll')addjs('exs=[];');
	else if(cbJs=='scrollBottom')setTimeout('scrollBottom("'+com[4]+'")',200);
}}

function ajb(p,e){var com=p[0];
	var app=p[1]; var prm=p[2]; var inp=p[3]; var cbk=p[0].split(','); var z=cbk[0];
	if(!cbk[1] && z!='popup' && z!='pagup' && z!='imgup' && z!='bubble' && z!='menu' && z!='input' && z!='before' && z!='after' && z!='begin' && z!='atend' && z!='reload' && z!='ses'){cbk[0]='div'; cbk[1]=z;}
	var com=cbk.join(',');
	return ajaxCall(com+'|'+app,prm,inp);}

function ajbt(e){var p2='';
	if(e.dataset.toggle){var ok=toggle(e,e.dataset.toggle); if(ok)return false;}
	if(e.dataset.prmtm){
		if(e.dataset.prmtm=='current')var p2=getbyid('prmtm').value;
		else getbyid('prmtm').value=e.dataset.prmtm;}
	var d=e.dataset.j; var p=d.split('|'); if(p2)p[2]=p[2]+','+p2; ajb(p,e);
	var d=e.dataset.jb; if(d){var p=d.split('|'); ajb(p,e);}}

function toggle(btn,did){
	if(btn.rel=='active'){closediv(did); btn.rel=''; active(btn,0); return 1;} 
	else{btn.rel='active'; btn.dataset.bid=btn.id; active(btn,1); var p=btn.parentNode.childNodes;
	for(i=0;i<p.length;i++)if(p[i].id!=btn.id){p[i].rel=''; active(p[i],0);}
return 0;}}

/*function toggle_close(did){var id=getbyid(did).dataset.bid;
	var btn=getbyid(id); closediv(did); btn.rel=''; active(btn,0);}*/

function scrollBottom(d){
	var div=getbyid(d);
	div.scrollTo(0,div.scrollHeight);}

function randid(d){var n=Math.random()+''; return d+(n.substr(2,7));}

function active(btn,o){var css=btn.className;
	if(o)btn.className=css+' active';
	else btn.className=css.split(' ')[0];}

//capture inputs
function ajaxCaptures(el){
var typ=el.type;
if(!typ)var type='div'; else var type=typ.split('-')[0];
if(type=='text' || type=='password' || type=='hidden')var content=el.value;
else if(type=='placeholder')var content=el.placeholder;
else if(type=='textarea'){var content=el.value; if(!content)var content=(el.innerHTML);}
else if(type=='checkbox')var content=el.checked?1:0;
else if(type=='radio')var content=el.options[el.selectedIndex].value;
else if(type=='select')var content=el.options[el.selectedIndex].value;
else if(type=='range')var content=el.value;
else if(type=='div')var content=el.innerHTML;//encodeURIComponent
else var content=el.value;
//content=escape(content);
return jurl(content);}

//Amt
function multithread(content,ix){
na=Math.ceil(content.length/cutat); if(!na)na=1; rs=0;
for(i=0;i<na;i++){var res=jurl(content.substr((i*cutat),cutat));
	var ajax=new AJAX('/amt.php?nb='+i+'&mem='+res,'mem','','');}
return na;}

function amt_ending(){rs=rs+1;
if(na==rs)var ajax=new AJAX(mem[0],mem[1],mem[2],mem[3]);}

//timer
function ajaxTimer(call,prm,inp){//alert(prm); dont't works!!
if(typeof xc!='undefined')clearTimeout(xc);
xc=setTimeout(function(){ajaxCall(call,prm,inp);},200);}

//login
function verifusr(e){//input,user
	//ajaxCall("loadjs,usrexist()|login,verifusr","user="+e.value);
	ajaxCall("returnVar,usrxs|login,verifusr","user="+e.value);
	setTimeout(function(){usrexist(e);},100);}
function usrexist(e){var bt=getbyid('usrexs');
	if(usrxs){bt.style.display='inline-block'; e.style.bordeColor='red';}
	else{bt.style.display='none'; e.style.bordeColor='silver';}}

//popup
function Repos(){
if(!curid)return;
var popup=getbyid(curid);
var id='popu'+(curid.substring(3));
var popu=getbyid(id);
poph(popu); var pos=ppos(popu,0);
popup.style.left=pos.x; popup.style.top=pos.y;}

function Reduce(){
var pp=getbyid(curid);
var id='popu'+(curid.substring(3));
var div=getbyid(id); var op=div.style.display;
if(op=='block' || !op)div.style.display='none'; 
else div.style.display='block';}

function poph(popu,pageup){
popu.style.maxHeight=''; var adjust=40; var sz=innersizes();
var ha=sz.h; var hb=popu.offsetHeight+adjust;
if(hb>ha){popu.style.maxHeight=(pageup==1?'100vh':(ha-adjust))+'px';
	popu.style.width=(pageup==1?'100vw':'');
	popu.style.overflowY='auto'; popu.style.overflowX='hidden';}
else{popu.style.overflowY='auto'; popu.style.overflowX='hidden'; popu.style.width='';}}

function ppos(popu,decal){var px=0; var sz=innersizes(); 
var sw=sz.w; var w=popu.offsetWidth; var l=(sw-w)/2+px; var py=-20; 
var sh=sz.h; var h=popu.offsetHeight; var t=(sh-h)/2+py; 
if(l+decal+w+0>sw)decal=0; var px=(l>0?l:0)+decal;
if(t+decal+h+0>sh)decal=0; var py=(t>0?t:0)+decal;
return {x:px+'px', y:py+'px'};}

function popUp(res,method){popnb+=1; var nb=popnb; move=1;
var content=getbyid('popup');
var decal=(content.childNodes.length)*10;
var popup=document.createElement('div');
popup.id='pop'+nb; popup.style.position='fixed';
addEvent(popup,'mousedown',function(){zindex('pop'+nb)});
popup.innerHTML=res; 
content.appendChild(popup); zindex('pop'+nb);
var popa=getbyid('popa');
addEvent(popa,'mousedown',function(event){start_drag(event,nb)});
var popu=getbyid('popu');
poph(popu);//before ppos
var pos=ppos(popu,decal);
popup.style.left=pos.x; popup.style.top=pos.y;
popa.id='popa'+nb; popu.id='popu'+nb;}

var move=0;
function pageUp(res,img){
if(popnb)Close('pop'+popnb);//used for second pagup
popnb+=1; var nb=popnb;
var content=getbyid('popup'); if(!res)return;
var popup=document.createElement('div');
popup.id='pop'+nb; popup.style.position='fixed';
addEvent(popup,'mousedown',function(){zindex('pop'+nb)});
popup.innerHTML=res;
content.appendChild(popup); zindex('pop'+nb); opac(1,'pop'+nb);
var popu=getbyid('popu');
if(!img){//closer
	var imu=popu.children[0]; var i=0;
	while(imu.innerHTML==''){i++; imu=popu.children[i];}
	imu.id='pgu'+nb; zindex('pgu'+nb);
	clbubob={esc:'pgu'+nb,cl:'pop'+nb,bt:0};}
if(img)addEvent(popu,'mousedown',function(){Close('popup')});
poph(popu,1);//before ppos
popup.style.left=0; popup.style.top=0;
popup.style.right=0; popup.style.bottom.top=0;
Timer('opac','pop'+nb,0,100,10);
popu.id='popu'+nb;}

//bubble position
function bpos(id,nb,mode,popup){//bubble
var bt=getbyid(id); var pos=getPositionRelative(bt);//btn of reference
var bl=getbyid(nb); var pob=getPositionAbsolute(bl);//bubble
var px=pos.x+pos.w+6; var py=pos.y-((pob.h-pos.h)/2);
if(pob.h>300){var popu=getbyid('popu'); popu.style.maxHeight=300+'px'; py=pos.y;}
if(mode){px=pos.x; py=pos.y+bt.offsetHeight+6;}//as menu
if(py<20)py=20;
var sz=innersizes();
if(mode)if(pos.x+pob.w>sz.w)px=pos.x+pos.w-pob.w;//flip
if(curid)var parentpopu=getbyid('popu'+(curid.substring(3))); 
if(parentpopu)var scr=parentpopu.scrollTop; if(scr)py-=scr;
pob=getPositionAbsolute(bl);
return {x:px+'px',y:py+'px'};}

//bubble closers
clbubob={};
function clickoutside(bub,e){if(e)var m=mouse(e); var yoffset=0;
	var p=getPositionRelative(bub); var fix=infixed(bub);//if scroll
	if(fix){var p=getPositionAbsolute(bub); var yoffset=self.pageYOffset;} 
	var top=p.y+yoffset;
	if(m.x<p.x||m.x>(p.x+p.w)||m.y<top||m.y>(top+p.h))return 1;}

//clic on body
function closebub(e){var ob=getbyid(clbubob.esc);
	if(ob && clickoutside(ob,e)){
		if(clbubob.cl)hidediv(clbubob.cl);//Close
		/*if(clbubob.cl){//error open bubble:
			if(typeof x!='undefined')clearTimeout(x); if(typeof xb!='undefined')clearTimeout(xb);
			Timer('opac',clbubob.cl,100,0,10); xb=setTimeout('Close(clbubob.cl)',1000);}*/
		if(clbubob.bt)var obt=getbyid(clbubob.bt);
		if(obt){obt.rel=''; active(obt,0)}
		clbubob={};}}

function buboff(e){var ob=getbyid('pop'+id); if(ob){Close('popub'+id);}}
function closebubauto(id){var ob=getbyid(id); if(ob){Close('pop'+id); ob.rel='';}}
function attachclbub(popu,id){var r=popu.getElementsByTagName('a');
for(i=0;i<r.length;i++){
	//if(r[i].href)window.location=document.r[i].href;
	addEvent(r[i],'click',function(){closebubauto(id)});}}

exb=[];
function bubCloseOthers2(pid){
if(exb.indexOf(pid)==-1)exb.push(pid); var n=exb.length;
if(n>0)for(var i=0;i<n;i++)if(exb[i] && exb[i]!=pid){
	var bt=getbyid(exb[i].substr(3)); if(bt){bt.rel=''; bt.className='';}
	Close(exb[i]); exb[i]=0;}}

//bubble
function bubble(res,id,mode){
var btn=getbyid(id); var pid='pop'+id;
clbubob={esc:pid,cl:pid,bt:id};//params for close
if(btn.rel=='active'){Close(pid); btn.rel=''; active(btn,0); return;}
else{btn.rel='active'; active(btn,1);}
closebubauto(id);
bubCloseOthers2(pid);
bubClose();//close menus
var clbub=getbyid('closebub');
var popup=document.createElement('div');
popup.id=pid; popup.style.position='absolute';
addEvent(popup,'mousedown',function(){zindex(pid)});
popup.innerHTML=res;
btn.parentNode.appendChild(popup); zindex(pid)
var popu=getbyid('popu'); attachclbub(popu,id);//dont works with href and menu
poph(popu);//before bpos
var pos=bpos(id,pid,mode,popup);
popup.style.left=pos.x; popup.style.top=pos.y;
popu.id='popu'+id;}//after bpos

//inside div
function menu(res,id,mode){popnb+=1; var nb=popnb; var btn=getbyid(id);
bubBodyCloser(1);
bubCloseOthers2('');//close bub
if(btn){bubCloseOthers(btn.parentNode); btn.className='active';}
var content=btn.parentNode;
var popup=document.createElement('div');
popup.id='pop'+id; popup.style.position='absolute';
addEvent(popup,'mousedown',function(){zindex('pop'+id)});
//addEvent(document.body,'mousedown',function(){bubClose()});
popup.innerHTML=res; 
content.appendChild(popup); zindex('pop'+id);
var popu=getbyid('popu'); poph(popu);//before ppos
var posAbs=getPositionAbsolute(btn);
var posRel=getPositionRelative(btn);
var posbub=getPositionRelative(popup);
var bub=getPositionRelative(popu); var sz=innersizes();
if(mode=='1'){//vertical
	var px=posRel.x; var py=posRel.y+posRel.h; var pz='';
	if(posAbs.x+posbub.w>sz.w)px=px+posRel.w-posbub.w+10;//flip
	//if(px+posbub.w>sz.w){px=''; pz=-10;}
}
else{//horizontal, second iteration
	var px=posRel.x+posRel.w; var py=posRel.y; var pz='';
	if(posAbs.x+posRel.w+bub.w+10>sz.w)px=posRel.x-bub.w;
}
if(px)popup.style.left=px+'px';
if(pz)popup.style.right=pz+'px';
popup.style.top=py+'px';
popu.id='popu'+nb;}

function bubBodyCloser(op,id){var clbub=getbyid('closebub'); clbub.style.zIndex=1;
if(op){clbub.style.width='100%'; clbub.style.height='100%';}
else{clbub.style.width='0'; clbub.style.height='0';}}

function bubCloseOthers(e){//bubBodyCloser(0);
var btr=e.getElementsByTagName('div');
if(btr.length>0)for(var i=0;i<btr.length;i++)Close(btr[i].id);
var btr=e.getElementsByTagName('a');
if(btr.length>0)for(var i=0;i<btr.length;i++)btr[i].className='';}

function bubClose(){bubBodyCloser(0);//
var bub=document.getElementsByClassName('bub');
for(var i=0;i<bub.length;i++)bubCloseOthers(bub[i]);}

function bubCloseTimer(){//var id=e.id;
if(typeof xb!='undefined')clearTimeout(xb);
xb=setTimeout(function(){bubClose()},4000);}

//composants
function strreplace(rep,by,val){return val.split(rep).join(by);}
function jurl(val,n){
var arr=["\n","\t",'\'','|',"'",'"','*','#','+','=','&','?','.',':',',','/','%u',' ','<','>'];
var arb=['(n)','(t)','(asl)','(bar)','(q)','(dq)','(star)','(dz)','(add)','(eq)','(and)','(qm)','(dot)','(ddot)','(coma)','(sl)','(pu)','(sp)','(b1)','(b2)'];
if(n){var ra=arb; var rb=arr;}else{var ra=arr; var rb=arb;}
var rgx=new RegExp(/([^A-Za-z0-9\-])/);
if(rgx.test(val))for(var i=0;i<arr.length;i++)val=strreplace(ra[i],rb[i],val);
return val;}

function nsf(){return false;} function nst(){return true;} 
function noslct(a){
if(window.sidebar){if(a)document.onmousedown=nst; else document.onmousedown=nsf;}}
document.onselectstart = new Function("return false");

function zindex(id){popz++; curid=id; var bub=getbyid(id);
if(bub!=null)bub.style.zIndex=popz;}

function addiv(tar,res,st){var ob=getbyid(tar); if(ob==null)return;
var div=document.createElement('span'); div.innerHTML=res; var parent=ob.parentNode; 
if(st=='before')parent.insertBefore(div,ob);
else if(st=='after'){var childs=div.childNodes, n=childs.length;
	for(i=0;i<n;i++)if(typeof childs[i]=='object')parent.appendChild(childs[i]);}
else if(st=='begin'){var obd=ob.childNodes; ob.insertBefore(div,obd[0]);}
else if(st=='atend')ob.appendChild(div);}

function addjs(d){var head=document.getElementsByTagName('head')[0];
var js=document.createElement('script'); js.innerHTML=d;
head.appendChild(js);}

function popslide(ev){
if(move && cpop!=0){var mousepos=mouse(ev);
	cpop.style.left=(mousepos.x-cpop_difx)+'px';
	cpop.style.top=(mousepos.y-cpop_dify)+'px';}}

function start_drag(ev,z){
popup=getbyid('pop'+z); cpop=popup;
old_mousep=mouse(ev);
old_mousex=getPositionAbsolute(popup);
cpop_difx=old_mousep.x-old_mousex.x;
cpop_dify=old_mousep.y-old_mousex.y;}

function stop_drag(ev){cpop=0;}

function mouse(ev){if(ev.pageX || ev.pageY){return {x:ev.pageX,y:ev.pageY};}
return{x:ev.clientX+document.body.scrollLeft-document.body.clientLeft,
	y:ev.clientY+document.body.scrollTop-document.body.clientTop};}

function getPositionAbsolute(e){if(e==null)return {x:0,y:0,w:0,h:0};
var left=0; var top=0; var w=e.offsetWidth; var h=e.offsetHeight;
while(e.offsetParent){left+=e.offsetLeft; top+=e.offsetTop; e=e.offsetParent;}
left+=e.offsetLeft; top+=e.offsetTop; return {x:left,y:top,w:w,h:h};}
function getPositionRelative(e){if(e==null)return {x:0,y:0,w:0,h:0};
return {x:e.offsetLeft,y:e.offsetTop,w:e.offsetWidth,h:e.offsetHeight};}

function infixed(e){if(e==null)return 'no';
while(e.parentNode){if(e.style.position=='fixed')return e; e=e.parentNode;}
return 0;}

function Close(val){var pp=getbyid('popup');
if(val=='popup' && pp){if(curid)pp.removeChild(getbyid(curid)); else pp.innerHTML=''; curid=0; move=0;}
else if(val=='pop' && pp)pp.innerHTML='';
else if(val){var div=getbyid(val); if(div)div.parentNode.removeChild(div);}}
