<?php

class admin_ascii{
static $private='6';
static $db='ascii';

static function headers(){
add_head('csscode','');}

//install
static function install(){
sqlcreate(self::$db,['ref'=>'int','nam'=>'var','icon'=>'var'],1);}

//save
static function update($p){$rid=$p['rid'];
sqlup(self::$db,'nam',$p[$rid],$p['id']);
$r=sesfunc('ascii_com','',1);
return self::com($p);}

static function del($p){
$nid=sqldel(self::$db,$p['id']);
return self::com($p);}

static function save($p){//$lang=val($p,'lang');,$lang
$nid=sqlsav(self::$db,array($p['ref'],$p['icon']));
$r=sesfunc('ascii_com','',1);
return self::com($p);}

static function edit($p){$rid=randid('icons');//id
$r=sql('ref,nam,icon',self::$db,'ra','where id='.$p['id']);
$ret=label($rid,$r['ref'].' '.$r['icon']);
$ret.=goodinput($rid,$r['nam']);
$ret.=aj('admm,,x|admin_ascii,update|id='.$p['id'].',rid='.$rid.'|'.$rid,lang('save'),'btsav');
$ret.=aj('admm,,x|admin_ascii,del|id='.$p['id'],lang('del'),'btdel');
return $ret;}

static function open($p){$ref=val($p,'ref');
$p['id']=sql('id',self::$db,'v',['ref'=>$ref]);
if(!$p['id'])$p['id']=sqlsav(self::$db,[$ref,'']);
if($p['id'])return self::edit($p);}

static function add($p){//ref,icon
$ref=val($p,'ref'); $icon=val($p,'icon');
$ret=input('ref',$ref?$ref:'',16,'ref').input('icon',$icon?$icon:'',16,'icon');
$ret.=aj('admm,,x|admin_ascii,save||ref,icon',lang('save'),'btsav');
return $ret;}

//table
static function select(){$ret='';
if(auth(6)){
	$ret.=aj('popup|admin_ascii,add',langp('add'),'btn');
	$ret.=aj('popup|core,mkbcp|b=icons',langp('backup'),'btsav');
	if(sqlex('icons_bak'))
	$ret.=aj('popup|core,rsbcp|b=lang',langp('restore'),'btdel');}
	$ret.=aj('popup|admin_ascii,all',pic('pictos'),'btn');
	$ret.=aj('admm|admin_ascii',pic('reload'),'btn').br();
return div($ret);}

static function build(){
return sql('id,ref,nam,icon',self::$db,'','order by ref');}

static function all(){
$r=self::build();
if($r)foreach($r as $k=>$v)$rb[$k]=tag('a',['title'=>$v[1].' '.$v[2]],$v[3]);
$ret=implode(' ',$rb);
return div($ret);}

static function com(){
$bt=self::select();
$r=self::build();
if($r)foreach($r as $k=>$v){
	$dec=aj('popup|admin_ascii,edit|id='.$v[0],$v[1],'btn');
	$ref=$v[2]; $icon=$v[3];
	$rb[$k]=[$dec,$ref,$icon];}
array_unshift($rb,['dec','ref','icon']);
return $bt.mktable($rb,1);}

static function known(){$ret=["934"=>"phi1","945"=>"alpha","946"=>"beta","947"=>"gamma","948"=>"delta","949"=>"epsilon","950"=>"zeta","951"=>"eta","952"=>"theta","953"=>"iota","954"=>"kappa","955"=>"lambda","956"=>"mu","957"=>"nu","958"=>"xi","959"=>"omicron","960"=>"pi","961"=>"rho","962"=>"sigma","963"=>"sigma1","964"=>"tau","965"=>"upsilon","966"=>"phi","967"=>"chi","968"=>"psi","969"=>"omega","1046"=>"oomo","8258"=>"s3","8273"=>"s2","8277"=>"s1","8886"=>"copy","8887"=>"paste","8981"=>"search","8983"=>"hash","8984"=>"command","8986"=>"clock","9092"=>"share","9096"=>"shared","9187"=>"irrationnal","9201"=>"time","9203"=>"wait","9204"=>"left","9205"=>"right","9206"=>"up","9207"=>"down","9208"=>"pause","9210"=>"record","9211"=>"logout","9212"=>"login","9477"=>"etc","9654"=>"play","9712"=>"redim","9728"=>"sun","9742"=>"phone","9744"=>"emptycase","9745"=>"valid","9746"=>"unvalid","9774"=>"peace","9775"=>"yinyang","9776"=>"menu","9824"=>"peak","9827"=>"clover","9829"=>"heart","9830"=>"diam","9834"=>"sound","9835"=>"music","9854"=>"apps","9872"=>"flag","9874"=>"tools","9875"=>"anchor","9888"=>"alert","9898"=>"round","9901"=>"union","9902"=>"distinct","9903"=>"separate","9919"=>"key","9921"=>"msql","9929"=>"banner","9930"=>"html","9940"=>"forbidden","9946"=>"fullscreen","9964"=>"finder","9986"=>"cut","9989"=>"true","9995"=>"hand","9996"=>"success","9997"=>"editor","9998"=>"edit","10006"=>"close","10010"=>"add","10024"=>"stars","10025"=>"star","10033"=>"asterix","10060"=>"del","10062"=>"false","10068"=>"help","10069"=>"info","10070"=>"config","10084"=>"love","10094"=>"chevron1","10095"=>"chevron2","10096"=>"code","10100"=>"accolad1","10101"=>"accolad2","10133"=>"plus","10134"=>"less","10135"=>"divide","10550"=>"back","10561"=>"refresh","10701"=>"triangle","10718"=>"symetry","10723"=>"diez","10734"=>"delconn","10736"=>"conndot","10737"=>"connslct","10738"=>"conn","10753"=>"xor","10754"=>"nor","10799"=>"sclose","11020"=>"width","11021"=>"height","11092"=>"network","11100"=>"loading","11114"=>"import","11116"=>"export","11118"=>"reload","11120"=>"upcloud","11121"=>"upload","11123"=>"download","11128"=>"update","11156"=>"fsopen","11164"=>"kleft","11165"=>"ktop","11166"=>"kright","11167"=>"kdown","11216"=>"fsclose","11279"=>"cluster","12292"=>"harmony","12410"=>"img","68181"=>"all","120141"=>"i","120407"=>"b","120413"=>"h","120426"=>"u","127378"=>"cool","127379"=>"free","127381"=>"new","127383"=>"ok","127758"=>"globe","127760"=>"global","127769"=>"sleep","127771"=>"moon","127790"=>"taco","127795"=>"tree","127799"=>"rose","127801"=>"leaf","127803"=>"flower2","127804"=>"flower","127813"=>"fruit","127814"=>"vegetable","127829"=>"pizza","127873"=>"gift","127908"=>"mic","127911"=>"headphones","127912"=>"paint","127916"=>"movie","127937"=>"race","127942"=>"trophee","127944"=>"sport","127968"=>"home","127981"=>"industry","128070"=>"finger","128077"=>"thumb","128100"=>"user","128101"=>"users","128125"=>"alien","128139"=>"kiss","128150"=>"inlove","128161"=>"idea","128172"=>"comment","128176"=>"money","128190"=>"save2","128191"=>"disk","128193"=>"folder","128194"=>"folder2","128195"=>"script","128198"=>"agenda","128200"=>"charts","128202"=>"stats","128230"=>"pack","128241"=>"gsm","128247"=>"photo","128250"=>"tv","128256"=>"permut","128257"=>"rollback","128260"=>"reload2","128269"=>"search2","128271"=>"lock","128279"=>"link","128362"=>"speakers","128372"=>"local","128386"=>"mail","128392"=>"localize","128393"=>"editxt","128394"=>"edit2","128398"=>"editor2","128423"=>"structure","128427"=>"save","128435"=>"admin","128438"=>"print","128441"=>"txt","128453"=>"sticky","128459"=>"file","128460"=>"select","128462"=>"doc","128463"=>"article","128464"=>"articles","128465"=>"trash","128468"=>"desktop","128471"=>"popup","128474"=>"size","128476"=>"meet","128489"=>"chat","128490"=>"forum","128506"=>"world","128516"=>"lol","128577"=>"sad","128578"=>"smile","128631"=>"quote","128679"=>"worksite","128711"=>"no","128760"=>"ufo","128941"=>"close2","129052"=>"previous","129054"=>"next","9209"=>"square"];
return $ret;}

static function save_ascii(){
$n=128; $max=1488;
$n=7424; $max=11833;
$n=119040; $max=119272;
$n=119552; $max=119638;
$n=119808; $max=120831;
$n=127024; $max=127123;
$n=127136; $max=127221;
$n=127232; $max=127386;
$n=127744; $max=129510;
$ra=self::known();
for($i=$n;$i<=$max;$i++){
	if(isset($ra[$i]))echo $v=$ra[$i]; else $v='';
	//sqlsav(self::$db,[$i,$v,'&#'.$i.';']);//sqlsavup
}
}

//content
static function content($p){$ret='';
//self::install();
//self::save_ascii();
$ret=self::com();
return div($ret,'board','admm');}

}

?>