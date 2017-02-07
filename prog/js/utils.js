//ph1.fr GNU/GPL

//select
var theSelection=false;
var clientPC=navigator.userAgent.toLowerCase();
var clientVer=parseInt(navigator.appVersion);
var is_ie=((clientPC.indexOf('msie')!=-1) && (clientPC.indexOf('opera')==-1));
var is_win=((clientPC.indexOf('win')!=-1) || (clientPC.indexOf('16bit')!=-1));

function selectxt(input,selectionStart,selectionEnd){
	if(input.setSelectionRange){input.focus();
		input.setSelectionRange(selectionStart,selectionEnd);}
	else if(input.createTextRange){
		var range=input.createTextRange();
		range.collapse(true);
		range.moveEnd('character',selectionEnd);
		range.moveStart('character',selectionStart);
		range.select();}}

function embed_slct(debut,fin,id,act){var id=id?id:'txtarea';
	var txtarea=getbyid(id); txtarea.focus(); 
	donotinsert=false; theSelection=false; 
	if((clientVer>=4) && is_ie && is_win){
		theSelection=document.selection.createRange().text;
		if(theSelection){
		while(theSelection.substring(theSelection.length-1,theSelection.length)==' '){
				theSelection=theSelection.substring(0,theSelection.length-1);}
			document.selection.createRange().text=debut+theSelection+fin;
			txtarea.focus(); theSelection=''; return theSelection;}}
	else if(txtarea.selectionEnd && (txtarea.selectionEnd-txtarea.selectionStart>0)){
		theSelection=mozWrap(debut,fin,id,act);
		return theSelection;}}

function insert(text,tar){var txtarea=getbyid(tar);
	if(txtarea.createTextRange && txtarea.caretPos){
		var caretPos=txtarea.caretPos; var ct=caretPos.text;
		caretPos.text=ct.charAt(ct.length-1)==' '?ct+text+' ':ct+text;}
	else{mozWrap('',text,tar); return;}}

//from http://www.massless.org/mozedit/
function mozWrap(opn,clo,id){var id=id?id:'txtarea';
	var s1=''; var s2=''; var s3=''; 
	var txtarea=getbyid(id); var vl=1;//
	if(typeof txtarea.value==='undefined')var vl=0;
	var selLength=txtarea.textLength;
	var selStart=txtarea.selectionStart;
	var selEnd=txtarea.selectionEnd;
	var selTop=txtarea.scrollTop;
	if(selEnd==1 || selEnd==2)selEnd=selLength;
	if(vl)var truend=(txtarea.value).substring(selEnd-1,selEnd);
	if(selEnd-selStart>0 && truend==' '){selEnd=selEnd-1;}
	if(selEnd-selStart>0 && truend=="\n"){selEnd=selEnd-1;}
	if(vl)var s1=(txtarea.value).substring(0,selStart);
		//else var s1=(txtarea.innerHTML).substring(0,selStart);
	if(vl)var s2=(txtarea.value).substring(selStart,selEnd);
	if(vl)var s3=(txtarea.value).substring(selEnd,selLength);
	if(vl)txtarea.value=s1+opn+s2+clo+s3;
	else txtarea.innerHTML=s1+opn+s2+clo+s3;
	selFin=selEnd+clo.length+opn.length;
	//window.selectxt(txtarea,selStart,selFin);//selStart
	txtarea.scrollTop=selTop;
	txtarea.focus();
	return s2;}

//localstorage
function memStorage(val){
	var vn=val.split('_'); var ob=getbyid(vn[0]);//tar_var_copy
	if(vn[2]=='sav')localStorage[vn[1]]=ob.value;
	if(vn[2]=='res' && localStorage[vn[1]]!=undefined)ob.value=localStorage[vn[1]];}

//autorefresh
function getfilmtime(f){
	$(document).ready(function(){
		$.ajax({url:'ajax.php',async:false, 
			data:'call=File,fdate&params={"fileRoot":"'+f+'"}',
			success:function(result){res=result;}
		});
	});
return res;}

function refresh(f){
	var ftim=getfilmtime(f);
	if(ftim>tim)window.location=document.URL;
	autorefresh(f);}
function autorefresh(f){timer=setTimeout(function(){refresh(f)},3000);}

//timer
function opac(op,id){getbyid(id).style.opacity=(op/100);}
function bkg(op,id){getbyid(id).style.backgroundColor='rgba(0,0,0,'+(op/100)+')';}
function Timer(func,id,start,end,t){var timer=10;
	if(typeof id==='undefined' || id=='')return; 
	if(start>end){for(i=start;i>=end;i-=10){timer++; curi=i;
		x=setTimeout(func+"("+i+",'"+id+"')",timer*t);}}
	else if(start<end){for(i=start;i<=end;i+=10){timer++;
		x=setTimeout(func+"("+i+",'"+id+"')",timer*t);}}}

function slowclose(id){
	if(typeof x!='undefined')clearTimeout(x); if(typeof xb!='undefined')clearTimeout(xb);
	Timer('opac',id,100,0,10); xb=setTimeout('Close('+id+')',1000);}

//buttons
function ajdel(call,prm,inp){
	var ok=confirm('really?');
	if(ok)ajaxCall(call,prm);}

//verif
function isEmail(myVar){
	var regEmail=new RegExp("^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$","i");
	return regEmail.test(myVar);}

function verifchars(e){var va=e.value;
	var arr=[',','?',';','.',':','/','!','§',' ','"',"'",'(',')','=','+','$','*','%','<','>',' ','|','~','&','^','¨','é','è','à','ç','ù','£','@','{','}','[',']','`','^','µ','¨','^','²','#','\\'];//'-','_',
	for(i=0;i<arr.length;i++)va=va.replace(arr[i],'');
	if(Number(va.substr(0,1)))va=va.substr(1); //var va=va.toLowerCase();
	e.value=va;}

//fixdiv
function fixdiv(ob){
	var scrl=pageYOffset; var dim=innersizes();
	var div=getbyid(ob); var pdiv=getPositionAbsolute(div);
	if(typeof xtop==='undefined')xtop=pdiv.y;
	if(typeof diff==='undefined')diff=pdiv.h-dim.h; if(diff<0)diff=0;
	if(scrl<=xtop+diff){
		div.style.top='';
		div.style.position='relative';}
	else if(diff>0){
		div.style.top=(0-diff)+'px';
		div.style.position='fixed';}
	else {
		div.style.top='0';
		div.style.position='fixed';}}

function fixdiv_resize(ob){var dim=innersizes();
	var div=getbyid(ob); var pdiv=getPositionAbsolute(div);
	diff=pdiv.h-dim.h; if(diff<0)diff=0;}

function togglediv(id,o){var div=getbyid(id);
if(o){div.style.display='block'; clbubob={esc:id,cl:id,bt:''};
//addEvent(document.body,'mousedown',function(){togglediv(id,0)});
//addEvent(getbyid('pblshcnt'),'mousedown',function(){togglediv(id,1)});//bug
}
else div.style.display='none';}

//scroll
function mouse(ev){if(ev.pageX || ev.pageY){return {x:ev.pageX,y:ev.pageY};}
	return{x:ev.clientX+document.body.scrollLeft-document.body.clientLeft,
		y:ev.clientY+document.body.scrollTop-document.body.clientTop};}

function scrolltopos(id){//var ob=getbyid(id);
	var ob=document.querySelector('#'+id);
	var sz=innersizes(); var h=sz.h/2; var w=sz.w/2;
	var ox=ob.getAttribute('x'); var oy=ob.getAttribute('y');
	var nx=ox-w>0?00:ox-w; var ny=oy-h>0?00:oy-h; 
	window.scroll(oy-h,ox-w);}//scrollslide(ny,nx);

function scrollslide(oy,ox){
	var wy=window.scrollY; var wx=window.scrollX;
	if(wy>oy)wy-=1; else wy+=1;
	if(wx>ox)wx-=1; else wx+=1;
	window.scrollTo(wy,wx);}
	//x=setTimeout(function(){scrollslide(oy,ox)},100);
	//if(wy-oy<10 && wx-ox<10)clearTimeout(x);

//verifs
function strcount(id,limit){
	var ob=getbyid(id).value; var to=getbyid('strcnt'+id);
	if(ob.length>=limit){
		getbyid(id).value=ob.substr(0,limit);
		var ob=getbyid(id).value;}
	to.innerHTML=limit-ob.length;}

function strcount1(id,limit){
	var ob=getbyid(id).value; var to=getbyid('strcnt'+id);
	if(ob.length>limit)getbyid('edtbt').className='btsav btsavno';
	else getbyid('edtbt').className='btsav';
	to.innerHTML=limit-ob.length;}

function resizearea(id){var ob=getbyid(id);
	var h=ob.offsetHeight; var t=ob.value; var r=t.split("\n"); var n=r.length;
	ob.style.height=(n*16)+'px';}
	
function closeditor(){
if(exb.indexOf(pid)==-1)exb.push(pid); var n=exb.length;
if(n>0)for(var i=0;i<n;i++)if(exb[i] && exb[i]!=pid){
	var bt=getbyid(exb[i].substr(3)); if(bt){bt.rel=''; bt.className='';}
	Close(exb[i]); exb[i]=0;}}

//upload for telex
function upload(rid){
	var form=getbyid("upl"); var prm='';
	var fileSelect=getbyid("upfile");
	var files=fileSelect.files;
	for(var i=0;i<1;i++){//files.length
		var formData=new FormData();
		var time=Math.floor(Date.now()/1000);
		var file=files[i];
		var xtr=file.name.split('.'); var xt=xtr[xtr.length-1];
		var filename='upl'+time+'.'+xt;
		if(!file.type.match("image.*"))continue;
		formData.append("upfile",file,filename);
		insert('['+filename+':img]',rid);
		//var prm="getinp:1"; else var prm="";
		var url="/call.php?appName=Upload&appMethod=save&params="+prm;
		var ajax=new AJAX(url,"atend","tlxapps","load",formData);
	}
}

//continuous scrolling
var exs=[]; var prmtm='';
function loadscroll(component,div){
	var content=getbyid(div);
	if(typeof content!=='object')return;
	var prmtm=String(getbyid('prmtm').value);
	if(prmtm)prmtm+=','; else return;
	var scrl=pageYOffset+innerHeight;
	var mnu=content.childNodes;
	var last=mnu[mnu.length-1]; if(!last)return;
	var id=last.id;
	var pos=getPositionAbsolute(last);
	var idx=exs.indexOf(id);
	if(idx==-1 && scrl>pos.y){exs.push(id);
		var call='after,'+id+',2|'+component;
		ajaxCall(call,prmtm+'from='+id.substr(3));}}
//addEvent(document,'scroll',function(event){loadscroll("app,meth","div"))});

//gps (for telex editor)
rid=0;
function gps_ko(error){switch(error.code){
	case error.PERMISSION_DENIED: console.log("refus utilisateur"); break;      
	case error.POSITION_UNAVAILABLE: console.log("localisation impossible"); break;
	case error.TIMEOUT: console.log("pas de réponse"); break;}}

//url
function updateurl(ret,url){
	document.getElementById('content').innerHTML=ret.html;
	document.title=ret.pageTitle;
	window.history.pushState({'html':ret.html,'pageTitle':ret.pageTitle},'',url);}

//keyPressEnter
function checkEnter(e,frm,id){
	if(e && e.which){characterCode=e.which;} else{e=e.keyCode; characterCode=e.keyCode;}
	if(characterCode==13){document.forms[frm].submit(); return false;}
	else return true;}

//utils
function closediv(id){getbyid(id).innerHTML='';}
function hidediv(id){getbyid(id).style.display='none';}
function inn(v,id){getbyid(id).innerHTML=v;}
function val(v,id){getbyid(id).value=v;}
function innfromval(from,id){getbyid(id).innerHTML=getbyid(from).value;}
function valfromval(from,id){getbyid(id).value=getbyid(from).value;}