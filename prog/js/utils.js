//tlex.fr GNU/GPL

//select
var slct=false;
var clientPC=navigator.userAgent.toLowerCase();
var clientVer=parseInt(navigator.appVersion);
var is_ie=((clientPC.indexOf('msie')!=-1) && (clientPC.indexOf('opera')==-1));
var is_win=((clientPC.indexOf('win')!=-1) || (clientPC.indexOf('16bit')!=-1));

function storeCaret(d){//insert at Caret position
if(d.createTextRange)d.caretPos=document.selection.createRange().duplicate();}

function setSelectionRange(input,start,end){
if(input.setSelectionRange){input.focus();
	input.setSelectionRange(start,end);}
else if(input.createTextRange){
	var range=input.createTextRange();
	range.collapse(true);
	range.moveEnd('character',end);
	range.moveStart('character',start);
	range.select();}}

//mb
function encode_utf8(s){return unescape(encodeURIComponent(s));}
function substr_utf8_bytes(str,startInBytes,lengthInBytes){
var resultStr='';
var startInChars=0;
for(bytePos=0; bytePos<startInBytes; startInChars++){ch=str.charCodeAt(startInChars);
	bytePos +=(ch<128)? 1 : encode_utf8(str[startInChars]).length;}
end=startInChars + lengthInBytes - 1;
for(n=startInChars; startInChars <=end; n++){ch=str.charCodeAt(n);
	end -=(ch<128)? 1 : encode_utf8(str[n]).length;
	resultStr +=str[n];}
return resultStr;}

//stabilo
function getrange(id){
var ob=getbyid(id);
elStart=0; elEnd=0;
var doc=ob.ownerDocument || ob.document;
var win=doc.defaultView || doc.parentWindow;
var sel;
if(typeof win.getSelection!="undefined"){
	sel=win.getSelection(); //sel=encode_utf8(sel);
	if(sel.rangeCount>0){
		var range=win.getSelection().getRangeAt(0);
		var preCaretRange=range.cloneRange();
		preCaretRange.selectNodeContents(ob);
		preCaretRange.setEnd(range.endContainer,range.endOffset);
		elEnd=preCaretRange.toString().length;}}
else if((sel=doc.selection) && sel.type!="Control"){
	var textRange=sel.createRange();
	var preCaretTextRange=doc.body.createTextRange();
	preCaretTextRange.moveToElementText(ob);
	preCaretTextRange.setEndPoint("EndToEnd",textRange);
	elEnd=preCaretTextRange.text.length;}
slct=sel.toString();
if(slct.substring(slct.length-1,slct.length)==' '){slct=slct.substring(0,slct.length-1); elEnd-=1;}
var elStart=elEnd-slct.length;
return{start:elStart,end:elEnd,txt:slct};}

function useslct(e,id,aid){var d=getrange(id);
	var prm='id:'+aid+',start:'+d.start+',end:'+d.end+',txt:'+jurl(d.txt);
	var url='/call.php?appName=stabilo&appMethod=add_note&popup==&params='+prm;
	if(d.txt)var ajax=new AJAX(url,'popup','','');}

//editors
function embed_div(opn,clo,id){//use getrange
	var ob=getbyid(id); var len=slct.length; //alert(len); alert(elStart+'-'+elEnd);
	var s1=(ob.innerHTML).substring(0,elStart);
	var s2=(ob.innerHTML).substring(elStart,elEnd);
	var s3=(ob.innerHTML).substring(elEnd,len);
	ob.innerHTML=s1+opn+s2+clo+s3;}

function embed_slct(debut,fin,id,act){//only value
	//var e=getrangepos(id); alert(e);
	var ob=getbyid(id); ob.focus(); //alert(ob.selectionStart);
	donotinsert=false; slct=false; 
	if((clientVer>=4) && is_ie && is_win){
		slct=document.selection.createRange().text;
		if(slct){while(slct.substring(slct.length-1,slct.length)==' '){
				slct=slct.substring(0,slct.length-1);}
			document.selection.createRange().text=debut+slct+fin;
			ob.focus(); slct=''; return slct;}}
	else if(ob.selectionEnd && (ob.selectionEnd-ob.selectionStart>0)){
		slct=mozWrap(debut,fin,id,act);
		return slct;}}

function insert(text,tar){var ob=getbyid(tar);
	if(ob.createTextRange && ob.caretPos){
		var caretPos=ob.caretPos; var ct=caretPos.text;
		caretPos.text=ct.charAt(ct.length-1)==' '?ct+text+' ':ct+text;}
	else{mozWrap('',text,tar); return;}}

//from http://www.massless.org/mozedit/
function mozWrap(opn,clo,id){
	var s1=''; var s2=''; var s3=''; 
	var ob=getbyid(id); var vl=1;//
	if(typeof ob.value==='undefined')var vl=0;
	var selLength=ob.textLength;
	var selStart=ob.selectionStart;
	var selEnd=ob.selectionEnd;
	var selTop=ob.scrollTop;
	if(selEnd==1 || selEnd==2)selEnd=selLength;
	if(vl)var truend=(ob.value).substring(selEnd-1,selEnd);
	if(selEnd-selStart>0 && truend==' '){selEnd=selEnd-1;}
	if(selEnd-selStart>0 && truend=="\n"){selEnd=selEnd-1;}
	if(vl)var s1=(ob.value).substring(0,selStart);
		//else var s1=(ob.innerHTML).substring(0,selStart);
	if(vl)var s2=(ob.value).substring(selStart,selEnd);
	if(vl)var s3=(ob.value).substring(selEnd,selLength);
	if(vl)ob.value=s1+opn+s2+clo+s3;
	else ob.innerHTML=s1+opn+s2+clo+s3;
	selFin=selEnd+clo.length+opn.length;
	window.setSelectionRange(ob,selStart,selFin);//selStart
	ob.scrollTop=selTop;
	ob.focus();
	return s2;}

//localstorage
function memStorage(val){
	var vn=val.split('_'); var ob=getbyid(vn[0]);
	if(vn[2]=='sav')localStorage[vn[1]]=vn[3]==1?ob.innerHTML:ob.value;
	if(vn[2]=='res'){if(vn[3]==1)ob.innerHTML=localStorage[vn[1]];
		else ob.value=localStorage[vn[1]];}}

//timer
function opac(op,id){getbyid(id).style.opacity=(op/100);}
function bkg(op,id){getbyid(id).style.backgroundColor='rgba(0,0,0,'+(op/100)+')';}
function resiz(op,id){getbyid(id).style.height=(op/100)+'px';}
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
	if(Number(va.substr(0,1)))va=va.substr(1); //va=va.toLowerCase();
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
	else{
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
function mouse(ev){if(ev.pageX || ev.pageY){return{x:ev.pageX,y:ev.pageY};}
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
	var tx=getbyid(id).value; var tn=getbyid('strcnt'+id); var tb=getbyid('edtbt'+id);
	if(tx.length>limit){tn.innerHTML=tx.length;
		tb.style.display='none'; tn.style.display='';}
	else if(tb){tb.style.display=''; tn.style.display='none';}}

function isNumeric(n){return !isNaN(parseFloat(n)) && isFinite(n);}

function numonly(id){var inp=getbyid(id);
	if(inp.value && !isNumeric(inp.value))inp.className='error';
	else inp.className='';}

function resizearea(id){var ob=getbyid(id);
	var h=ob.offsetHeight; var t=ob.value; var na=0;
	var r=t.split("\n"); var n=r.length; var n=n>10?10:n;
	for(i=0;i<n;i++){na+=1; var nb=r[i].length; var nc=nb/97;
		if(nc>1)na+=Math.floor(nc);}
	ob.style.height=10+(na*18)+'px';}
	
function closeditor(){
if(exb.indexOf(pid)==-1)exb.push(pid); var n=exb.length;
if(n>0)for(var i=0;i<n;i++)if(exb[i] && exb[i]!=pid){
	var bt=getbyid(exb[i].substr(3)); if(bt){bt.rel=''; bt.className='';}
	Close(exb[i]); exb[i]=0;}}

function closebt(f,rid){
	Close('bt'+rid); //pr(rid);
	var d=getbyid(rid); var ty=d.type;
	if(d)var t=ty=='text'?d.value:d.innerHTML; //pr(t);
	var tb=t.replace('['+f+':img]',''); //pr(tb);
	if(ty=='text')d.value=t; else d.innerHTML=t;}

//upload
function upload(rid,usr){
	var form=getbyid('upl'+rid);
	var fileSelect=getbyid('upfile'+rid);
	var files=fileSelect.files;
	var div=getbyid(rid);
	var fd=new FormData();
	for(var i=0;i<4;i++){//files.length
		var time=Date.now();//Math.floor(Date.now()/1000);
		var file=files[i]; //pr(files);
		if(!file)continue;
		var xtr=file.name.split('.'); var xt=xtr[xtr.length-1];
		if(file.type.match('image.*'))var ty='img';
		else if(file.type.match('audio.*'))var ty='audio';//audio/mpeg /mid
		else if(file.type=='video/mp4')var ty='video';
		else continue;
		if(ty=='img')var filename=''+time+'.'+xt;
		else var filename=ty+'/'+time+'.'+xt;
		fd.append('upfile'+rid,file,filename);
		if(div.type=='text')div.value=filename;
		else insert('['+filename+':'+ty+']',rid);
		var prm='rid:'+rid+',ty:'+ty;//getinp:1
		var url='/call.php?appName=upload&appMethod=save&params='+prm;
		var ajax=new AJAX(url,'atend',rid+'up','z',fd);}}

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
//addEvent(document,'scroll',function(event){loadscroll('app,meth','div'))});

//gps (for tlex editor)
rid=0;
function gps_ko(error){switch(error.code){
	case error.PERMISSION_DENIED: pr('gps: refus'); break;      
	case error.POSITION_UNAVAILABLE: pr('gps: impossible'); break;
	case error.TIMEOUT: p('gps: ne répond pas'); break;}}

//url
function updateurl(ret,url){
	document.getElementById('content').innerHTML=ret.html;
	document.title=ret.pageTitle;
	window.history.pushState({'html':ret.html,'pageTitle':ret.pageTitle},'',url);}

//keyPressEnter
function checkEnter(e,frm,id){
	if(e && e.which)char=e.which; else char=e.keyCode;
	if(char==13){document.forms[frm].submit(); return false;}
	else return true;}

function checkj(e,o){
	if(e && e.which)char=e.which; else char=e.keyCode;
	if(char==13){var d=o.dataset.j; if(d){var p=d.split('|'); ajb(p,o);} return false;}
	else return true;}

function callj(e,o){
	if(e && e.which)var char=e.which; else var char=e.keyCode;
	if(char==13){var d=o.dataset.j; if(d){var p=d.split('|'); ajb(p,o);} return false;}
	else return true;}

//getSelection
/*function focuspos(id){var ob=getbyid('txt'+id);
if(ob.setSelectionRange)return ob.value.substring(ob.selectionStart,ob.selectionEnd);
else if(document.selection){ob.focus(); return document.selection.createRange().text;}}*/

//art
function format(p,id){document.execCommand(p,false,'http');}
function execom(d,o){document.execCommand(d=='h'?'formatBlock':d,false,o?'<'+o+'>':null);}
function fontsz(n){var txt=document.getSelection(); alert(txt);}
function savtim(id){if(sok){xa=setTimeout("savtim("+id+")",10000);//bug multi amt
	//var ob=getbyid('txt'+id); var pos=getSelection;
	if(sok)ajaxCall("socket|art,savetxt","id="+id,"txt"+id);}}
function restore_art(id){editbt(id,2); getbyid("txt"+id).innerHTML=localStorage["m3"];}
function backsav(e,id){//13=enter,46=dot pr(char);
	if(e && e.which){char=e.which;} else{char=e.keyCode;}
	if(char==13)ajaxCall("socket|art,savetxt","id="+id,"txt"+id);}

function editxt(div,id,o){var ob=getbyid(div+id);
	if(ob.className!="editon"){
		if(div=="txt" && o!=2)ajaxCall("div,txt"+id+"|art,playconn","id="+id,"");
		ob.contentEditable="true";
		ob.designMode="on"; void 0; //ob.focus();
		ob.className="editon";}}

function savtxt(div,id){var ob=getbyid(div+id);
	ob.contentEditable="false"; ob.designMode="off"; ob.className="editoff";
	if(div=="tit")ajaxCall("div,tit"+id+"|art,savetxt","id="+id,"tit"+id);
	if(div=="txt")ajaxCall("div,txt"+id+"|art,savetxt","id="+id,"txt"+id);}

function editbt(id,o){var bt=getbyid("bt"+id);
	if(bt.rel==1 && !o){bt.rel=0; sok=0; //close
		ajaxCall("div,bt"+id+"|art,editbt","id="+id+",o=0","");
		savtxt("txt",id); //pr(bt.rel);
		getbyid("edt"+id).style.display="none";}
	else{bt.rel=1; sok=1; editxt("txt",id,o);  //pr(bt.rel);//if(!o)savtim(id);//open
		ajaxCall("div,bt"+id+"|art,editbt","id="+id+",o=1","");
		if(!o)ajaxCall("socket,"+id+",store|art,savetxt","id="+id,"txt"+id);//backup
		getbyid("edt"+id).style.display="inline-block";}}

//editable
function savecell(id,j){var t=getbyid('d'+id).innerHTML;
	var prm=j.split('|');
	ajaxCall(id+'|'+prm[0],prm[1]+',id='+id,'d'+id);}

//appx
function multhidden(n,id){var r=[];
	for(i=1;i<=n;i++){var v=getbyid(id+i).value; if(v)r.push(v);}
	getbyid(id).value=r.join('|');}

//chat
chatliv=4000;
function chatlive(){
	if(getbyid('chtbck')){
		var room=getbyid('chtroom').value;
		ajaxCall('div,chtbck|chat,read','vu=1,id='+room);}
	setTimeout("chatlive()",chatliv);}
if(chatliv)chatlive();

//json
function json(f){
	var req=new XMLHttpRequest(); req.open("GET",f,true); 
	req.onreadystatechange=jsonread; req.send(null);}
function jsonread(){//var d=doc.menu.value;
	if(req.readyState==4)var doc=eval('('+req.responseText+')');}

//map
rid=0;
function gps_paste(position){
	var	gpsav=position.coords.latitude+"/"+position.coords.longitude; val(gpsav,'coords'); 
	var d=new Date; val(d.toDateString(),'address');}

function geo2(id){rid=id;
	if(navigator.geolocation)navigator.geolocation.getCurrentPosition(gps_paste,gps_ko,{enableHighAccuracy:true,timeout:10000,maximumAge:600000});}

//profile
function geo(){
	if(navigator.geolocation)navigator.geolocation.getCurrentPosition(gps_ok,gps_ko,{enableHighAccuracy:true,timeout:10000,maximumAge:600000});
	else p("need html5");}
function gps_ok(position){
	var gpsav=position.coords.latitude+"/"+position.coords.longitude;
	ajaxCall("div,gpsloc|profile,gpsav","gps="+gpsav);}

//unused
function decodeBase64(s){
var e={},i,b=0,c,x,l=0,a,r='',w=String.fromCharCode,L=s.length;
var A="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
for(i=0;i<64;i++)e[A.charAt(i)]=i;
for(x=0;x<L;x++){
	c=e[s.charAt(x)];b=(b<<6)+c;l+=6;
	while(l>=8)((a=(b>>>(l-=8))&0xff)||(x<(L-2)))&&(r+=w(a));}
return r;}

//autorefresh
tim=0;
function getftime(f){ajaxCall("returnVar,ftres|file,fdate","fileRoot="+f); return res;}
function arload(f){var ftim=getftime(f); if(ftim>tim)window.location=document.URL; autorefresh(f);}
function autorefresh(f,x){if(x)clearTimeout(timr); else timr=setTimeout(function(){arload(f)},2000);}

//utils
function closediv(id){var d=getbyid(id); if(id && d)d.innerHTML='';}
/*function closediv0(id){var d=getbyid(id); var h=d.innerHeight; pr(id+'-'+h);//
	d.style.height=h+'px'; d.innerHTML=''; Timer('resiz',id,h,0,10);}*/

function repaircode(id){
d=getbyid(id).innerHTML;
d=strreplace(' = ','=',d);
d=strreplace(' (','(',d);
d=strreplace(') ',')',d);
d=strreplace(' {','{',d);
d=strreplace('  ',' ',d);
d=strreplace(', ',',',d);
d=strreplace(' . ','.',d);
//getbyid(id).innerHTML=d;
}

function invertclr(clr){var vclr=parseInt(clr,16);//var nclr=16777215-vclr;
if(vclr>8388607)return 'black'; else return 'white';}
function applyclr(e){e.style.backgroundColor='#'+e.value; e.style.color=invertclr(e.value);
	document.body.style.backgroundColor='#'+e.value;}
function affectclr(c,id){var e=getbyid(id); val(c,id);
	e.style.backgroundColor='#'+e.value; e.style.color=invertclr(e.value);
	document.body.style.backgroundColor='#'+e.value;}

function hidediv(id){getbyid(id).style.display='none';}
function inn(v,id){getbyid(id).innerHTML=v;}
function val(v,id){getbyid(id).value=v;}
function innfromval(from,id){getbyid(id).innerHTML=getbyid(from).value;}
function valfromval(from,id){getbyid(id).value=getbyid(from).value;}