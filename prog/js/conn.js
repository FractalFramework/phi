//ConnEctors

function substr_count(d,v){n=0;
	for(i=0;i<d.length;i++){if(d.substr(i,1)==v)n++;}
	return n;}

function isNumeric(n){return !isNaN(parseFloat(n)) && isFinite(n);}

function atjp(d){
	if(d!='this' && d!='self' && !isNumeric(d))d="\'"+d+"\'";
	return d;}

function attj(d,p){
	if(typeof(p)==='Array'){var rb=[];
		for(var k in p)rb.push(atjp(r[k]));
	var p=rb.join(',');}
	else p=atjp(p);
	return ' '+d+'('+p+');';}
	
function attr(d,p){return ' '+d+'="'+p+'"';}
function prma(d){var r=d.split('='); return attr(r[0],r[1]);}

function prm(d){var ret=''; var z=''; //alert(d.class);//if(p.hasOwnProperty(k))
	if(typeof d === 'object'){for(var k in d)ret+=attr(k,d[k]);}// z='obj';
	else if(d.indexOf('{')!=-1){var r=JSON.parse(d); //z='json';
		if(r)for(var k in r)ret+=attr(k,r[k]); return ret;}
	else if(d.indexOf(',')!=-1){var r=d.split(','); for(var k in r)ret+=prma(r[k]);}// z='multi';
	else if(d.indexOf('=')!=-1){var ret=prma(d);}// z='prma';
	else ret=d; //alert(z+'-'+ret);
	return ret;}

function tag(d,b,p=''){if(p)p=prm(p);// alert(p);
	if(b!='image' && b!='input')var ret='<'+b+p+'>'+d+'</'+b+'>';
	else ret='<'+b+p+' />'; //alert(ret);
	return ret;}

function conn(d,r){var b='',p='',t='';
	var na=d.lastIndexOf(':'); var nb=d.lastIndexOf('|'); var nc=d.lastIndexOf('.');
	if(nb!=-1)t=d.substring(0,nb); else if(na!=-1)t=d.substring(0,na); //alert(t+'$'+nb);
	if(na!=-1)b=d.substr(na+1);
	if(nb!=-1)p=d.substring(nb+1,na); //alert(t+' -- '+p+' -- '+b);
	if(nc!=-1)x=d.substr(nc);
	if(b==='app')d=d(p);
	if(b==='var')d=r[t];
	else if(b)d=tag(t,b,p);
	else if(c in ['jpg','png','gif'])d=tag('','image',{'source':t});
	else if(t.substr(0,4)=='http')d=tag(t,'a',{'href':t});
	else d='['+d+']';
	return d;}

function connectors(msg,cn,act){
//msg=decodeURIComponent(d);
var deb='',mid='',end='',msb='',out='',nb_in='',out_tmp='';
var ini=msg.indexOf("[");
if(ini!=-1){deb=msg.substr(0,ini); 
	msb=msg.substr(ini+1); out=msb.indexOf("]");
	if(out!=-1){msb=msg.substr(ini+1,out);
		nb_in=substr_count(msb,"[");
		if(nb_in>=1){
			for(var ia=1;ia<=nb_in;ia++){
				out_tmp=ini+1+out+1;
				msb=msg.substr(out_tmp);
				out=out+msb.indexOf("]")+1;
				msb=msg.substr(ini+1,out);
				nb_in=substr_count(msb,"[");}
			mid=msg.substr(ini+1,out);
			mid=connectors(mid,cn,act);}
		else mid=msb;
		if(act=='delconn')mid=delconn(mid,cn);
		else mid=conn(mid,cn);
		end=msg.substr(ini+1+out+1);
		end=connectors(end,cn,act);}
	else end=msg.substr(ini+1);}
else end=msg;
return deb+mid+end;}

/*
var d='[[hello|class=btsav:span] world|class=btn:b]';
var a='[hello|class=btsav:span]';
var b='['+a+' hola:u]';
var d='['+b+':section]';
//var r={['hello':[b,class=btn]]};
var ret=connectors(a);
*/

function delconn(d,cn){
	var na=d.lastIndexOf(':'); var nb=d.lastIndexOf('§'); var xt=d.substr(na+1);
	if((d.substr(nb)).indexOf(']')!=-1)nb=-1;
	if(xt==cn || !cn){
		if(d.substr(0,4)=='http' && !cn){
			if(nb!=-1)return d.substr(nb+1)+' ['+d.substr(0,nb)+']'; else return d;}
		else if(na!=-1){d=d.substr(0,na);if(nb!=-1)d=d.substr(0,nb);}
		else if(nb!=-1)d=d.substr(0,nb);}
	else d='['+d+']';
	return d;}