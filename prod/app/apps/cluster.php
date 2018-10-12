<?php
class cluster{
static $private='0';
static $a='cluster';
static $db='cluster';
static $cb='cls';
static $cols=['typ','tit','status'];
static $typs=['var','var','int'];
static $conn=0;
//static $db2='cluster_prop';
//static $db3='cluster_attr';
//static $db4='cluster_unit';
//static $db5='cluster_parents';
//static $db5='cluster_parent_avalisation';
//static $db6='cluster_parent_usages';
static $usage=['product','work','resource'];
static $status=['free','used','destroyed'];
static $credits=['red','blue','green'];
static $open=0;

function __construct(){
	$r=['a','db','cb','cols','conn'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));
	//props: actions(r)/job(b)/maintenance(g)
	sqlcreate('cluster_prop',['bid'=>'int','atid'=>'int','unid'=>'int','prop'=>'var'],1);
	sqlcreate('cluster_attr',['attr'=>'var','typ'=>'int'],1);//attr of prop
	sqlcreate('cluster_unit',['atid'=>'int','unit'=>'var'],1);//scale of prop
	//pid: parent id, aval: comity id avalization
	sqlcreate('cluster_parents',['bid'=>'int','pid'=>'int','utility'=>'var','aval'=>'int'],1);
	//type:buy/use, comity id
	//sqlcreate('cluster_parents',['cid'=>'int','comid'=>'int','type'=>'var','eval'=>'int'],1);
	}

static function admin($p){$p['o']=1; $p['ob']=1;
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	add_head('csscode','
	.coin{background:#ffffff; border:1px solid #fff; display:inline-block; padding:4px 6px;}
	.coin:hover{box-shadow:0,0,6px,rgba(0,0,0,0.4);}
	.red,.blue,.green{text-align:center;}
	.red{background:#ff4444; color:white;} .red:hover{background:#ff4444;}
	.blue{background:#4444ff; color:white;} .blue:hover{background:#4444ff;}
	.green{background:#44ff44; color:black;} .green:hover{background:#44ff44;}
	.instit{background:#f4f4f4; color:black;}
	.money{background:#f4f4f4; margin:6px;}');
	add_head('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	//$p['db2']='cluster_prop';
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function modif($p){
	return appx::modif($p);}

#form
static function mdf_prop($p){$id=val($p,'idp');
	if(val($p,'del'))sqldel('cluster_prop',$id);
	else sqlup('cluster_prop','prop',val($p,'prop'),$id);
	return self::properties($p);}

static function edt_prop($p){$id=val($p,'id'); $typ=$p['typ'];
	$r=sql('distinct(attr)','cluster_attr','rv','where typ="'.$typ.'"');
	$ret=datalist('addattr',$r,'',20,lang('attribut'));
	//$inp.=bar('eval'.$k,$v[2],25,'','','barlabel');
	$ret.=aj(self::$cb.'tab|cluster,sav_attr|id='.$id.',typ='.$typ.',|addattr',langp('add property'),'btsav');
	return $ret;}

//prop
static function sav_prop($p){$id=val($p,'id'); $unid=0; //p($p);
	$atid=val($p,'atid'); $prop=val($p,'addprop'); $unit=val($p,'addunit');
	if($unit){
		$ex=sql('id','cluster_unit','v','where unit="'.$unit.'" and atid="'.$atid.'"');
		if($ex)$unid=$ex;
		else $unid=sqlsav('cluster_unit',['atid'=>$atid,'unit'=>$unit]);}
	$r=['bid'=>$id,'atid'=>$atid,'unid'=>$unid,'prop'=>$prop]; //p($r);
	$ex=sql('id','cluster_prop','v','where atid="'.$atid.'" and bid="'.$id.'"');
	if($prop && !$ex)sqlsav('cluster_prop',$r);
	return self::properties($p);}

static function unit($p){$id=val($p,'id'); $atid=val($p,'atid');
	$r=sql('unit','cluster_unit','rv','where atid="'.$atid.'"'); //p($r);
	$ret=datalist('addunit',$r,val($p,'unit'),20,lang('referential'));
	return $ret;}

static function add_prop($p){$id=val($p,'id'); $attr=val($p,'addattr');
	$rb=sql('distinct(prop)','cluster_prop','rv','');
	$ret=datalist('addprop',$rb,'',20,$attr);
	$ret.=self::unit($p);
	$ret.=aj(self::$cb.'tab|cluster,sav_prop|id='.$id.',atid='.val($p,'atid').',|addprop,addunit',langp('save'),'btsav');
	return div($ret);}

//attr
static function copyfrom($p){//p($p);
	$id=val($p,'id'); $bid=val($p,'bid'); $typ=val($p,'typ',0); $ret='';
	if(!$bid){$r=sql('id,tit',self::$db,'kv','where typ="'.$typ.'"'); //p($r);
		if($r)foreach($r as $k=>$v)
			$ret.=aj(self::$cb.'tab|cluster,copyfrom|id='.$id.',bid='.$k,$v);}
	else{$r=$r=sql('atid,unid,prop','cluster_prop','rr','where bid="'.$bid.'"',1); //p($r);
		if($r)foreach($r as $k=>$v)
			$rb[]=[$id,$v['atid'],$v['unid'],$v['prop']]; //pr($rb);
		sqlsav2('cluster_prop',$rb);}
	return div($ret,'list');}

static function sav_attr($p){$id=val($p,'id'); $attr=val($p,'addattr');
	$r=['attr'=>$attr,'typ'=>val($p,'typ')];
	if(!$attr)return self::add_attr($p);
	else{
		$ex=sql('id','cluster_attr','v','where attr="'.$attr.'"');
		$ex_prop=sql('id','cluster_prop','v','where atid="'.$ex.'" and bid="'.$id.'"');
		if($ex_prop)return self::add_attr($p);
		if($ex)$p['atid']=$ex;
		else $p['atid']=sqlsav('cluster_attr',$r);
		return self::add_prop($p);}}

static function add_attr($p){$id=val($p,'id'); $typ=$p['typ'];
	$r=sql('distinct(attr)','cluster_attr','rv','where typ="'.$typ.'"');
	$ret=datalist('addattr',$r,'',20,lang('attribut'));
	//$inp.=bar('eval'.$k,$v[2],25,'','','barlabel');
	$ret.=aj(self::$cb.'edt|cluster,sav_attr|id='.$id.',typ='.$typ.'|addattr',langp('add property'),'btsav');
	$ret.=aj(self::$cb.'|cluster,edit|id='.$id,langp('cancel'),'btn');
	return $ret;}

//parents
static function del_parent($p){//p($p);
	if($id=val($p,'del'))sqldel('cluster_parents',$id);
	return self::parents($p);}

static function sav_parent($p){$pid=val($p,'addparent');
	$ex=sql('id','cluster_parents','v','where bid="'.$p['id'].'" and pid="'.$pid.'"');
	$r=['bid'=>$p['id'],'pid'=>$pid,'utility'=>val($p,'utility'),'aval'=>val($p,'approbation')];
	if(!$ex)$p['pid']=sqlsav('cluster_parents',$r);
	return self::parents($p);}

static function open_parent($p){$ret='';
	$r=sql('id,tit',self::$db,'kv','where uid="'.ses('uid').'"');
	if($r)foreach($r as $k=>$v)$ret.=btj($v,atj('val',[$k,'addparent']));
	return div($ret,'list');}

static function add_parent($p){$id=$p['id'];
	$ret=input_label('addparent','',lang('dependancy'));
	$ret.=aj('popup|cluster,open_parent',langp('open'),'btn');
	$ret.=input_label('utility','',lang('utility'));
	$ret.=input_label('approbation','',lang('approbation'));
	$ret.=aj(self::$cb.'tab|cluster,sav_parent|id='.$id.'|addparent,utility,approbation',langp('save'),'btsav');
	$ret.=aj(self::$cb.'|cluster,edit|id='.$id,langp('cancel'),'btn');
	return $ret;}

static function parents($p){$id=$p['id']; $cb=self::$cb;
	$r=sqljoin('cluster_parents'.'.id,pid,tit,utility,aval','cluster_parents',self::$db,'pid','id','where bid='.$id);
	$rb[]=[lang('title'),lang('id'),lang('utility'),lang('avalization'),''];
	if($r)foreach($r as $k=>$v){
		$edt=aj($cb.'tab|cluster,del_parent|id='.$id.',del='.$k,langpi('del'),'btdel');
		$parent=aj('popup|cluster,call|id='.$v[0],$v[1],'btn');
		$rb[]=[$parent,$v[0],$v[2],$v[3],$edt];}
	$ret=mktable($rb,1);
	$edt=aj(self::$cb.'edt|cluster,add_parent|id='.$id,langp('add dependance'),'btsav');
	return $ret.div($edt,'',$cb.'edt');}

//read
static function props($id){
	return sqljoin('cluster_prop.id,attr,prop,unit','cluster_prop','cluster_attr','atid','id','left join cluster_unit on cluster_unit.id=cluster_prop.unid where bid="'.$id.'"');}

static function properties($p){
	$id=$p['id']; $cb=self::$cb; $typ=val($p,'typ',0); 
	$j=$cb.'tab|cluster,mdf_prop|id='.$id;
	$r=self::props($id);
	$rb[]=[lang('attribut'),lang('referential'),lang('property'),''];
	foreach($r as $k=>$v){
		//$edt=aj($j.',idp='.$k,langpi('edit'),'btsav');
		$edt=aj($j.',idp='.$k.',del=1',langpi('del'),'btdel');
		$rb[]=[$v[0],$v[2],$v[1],$edt];}
	$ret=mktable($rb,1);
	$edt=aj($cb.'edt|cluster,add_attr|id='.$id.',typ='.$typ,langp('add attribut'),'btsav');
	$edt.=aj($cb.'edt|cluster,copyfrom|id='.$id.',typ='.$typ,langp('copy from'),'btn');
	return $ret.div($edt,'',$cb.'edt');}

static function subform($p){$id=$p['id']; $cb=self::$cb;
	$bt=aj($cb.'tab|cluster,properties|id='.$id,langp('properties'),'btn');
	$bt.=aj($cb.'tab|cluster,parents|id='.$id,langp('parents'),'btn');
	$ret=self::properties($p);
	return $bt.div($ret,'',$cb.'tab');}

static function fc_typ($k,$v){
	//if(!auth(6))return hidden($k,$v);
	return div(radio($k,self::$usage,$v,'',1));}

static function fc_status($k,$v){
	//if(!auth(6))return hidden($k,$v);
	return div(select($k,self::$status,$v,2).label($k,lang('state')));}

//static function fc_tit($k,$v){}
static function form($p){
	//$p['html']='txt';
	$p['fctyp']=1;
	$p['fcstatus']=1;
	//$p['barfunc']='barlabel';
	return appx::form($p);}

#edit
static function edit($p){
	//$p['help']='cluster_edit';
	$p['sub']=1;//mean local subform
	return appx::edit($p);}

static function add($p){
	$typ=$p['typ'];
	$ret=self::form($p);
	return $ret;}

static function create($p){
	//$p['pub']=0;//default privacy
	return appx::create($p);}

#build
static function build($p){
	return appx::build($p);}

static function template(){
	//return appx::template();
	return ['tit','list'=>'prop'];}

/*static function play($p){
	//$r=self::build($p);
	return appx::play($p);}*/

static function play($p){$id=val($p,'id');
	$r=self::build($p); $typ=val($r,'typ',0);
	$ret['tit']=div($r['tit'],self::$credits[$typ?$typ:0]);
	$r=self::props($id);
	if($r)foreach($r as $k=>$v)$ret['prop'][]=div($v[0].' ('.$v[2].')'.' : '.$v[1]);
	$r=sqljoin('pid,tit','cluster_parents',self::$db,'pid','kv','where bid='.$id);
	$ret['tb']=count($r).' '.lang('dependancies');
	if($r)foreach($r as $k=>$v)$ret['parents'][]=div(aj('popup|cluster,call|id='.$k,$v,'btn'));
	return phylo($ret,['tit',1=>'prop','tit'=>'tb',2=>'parents']);}

static function read_recursive($rc,$ra){$ret='';
	foreach($rc as $k=>$v){
		$typ=$ra[$k][1]?$ra[$k][1]:0; $c=self::$credits[$typ];
		$ret.=li(toggle('u-'.$k.'|cluster,call|id='.$k,$ra[$k][0],'btn '.$c));
		if(is_array($v))$ret.=self::read_recursive($v,$ra);
		else $ret.=ul('','','u-'.$k);}
	if($ret)return ul($ret);}

/*    [13] => Array
        (
            [15] => 1
            [3] => 1
            [12] => 1
            [7] => 1
            [6] => 1
            [14] => 1
        )
*/

static function find_parents($rx,$ra,$rb){$ret='';
	foreach($rb as $k=>$v){
		if(isset($ra[$k])){
			if(is_array($ra[$k])){
				$rb=self::find_parents($rx,$ra,$ra[$k]);
				$rx=$rb[0];
				$ret[$k]=$rb[1];}
			else $ret[$k]=$ra[$k];
			$rx[]=$k;}
		//else $ret[$k]=$v;
	}
	return [$rx,$ret];}

static function build_recursive($r){$ra=$r; $rx=''; $ret='';
	foreach($r as $k=>$v){
		if(is_array($v)){
			$rb=self::find_parents($rx,$ra,$v,$k);
			$rx=$rb[0];
			$ret[$k]=$rb[1]?$rb[1]:$v;}
		else $ret[$k]=$v;}
	$ret=self::clean($ret,$rx);
	return $ret;}

static function clean($r,$rb){
	foreach($rb as $k=>$v)if(isset($r[$v]))unset($r[$v]);
	return $r;}

static function menu($p){$ret='';
	//$r=sqljoin('bid,tit,typ,pid','cluster_parents',self::$db,'bid','rr','where uid="'.ses('uid').'" order by '.self::$db.'.id desc'); pr($r);
	$r=query('select '.self::$db.'.id,tit,typ,pid from '.self::$db.'
	left join '.'cluster_parents'.' on '.self::$db.'.id=bid
	where uid="'.ses('uid').'" order by '.self::$db.'.id desc','rr'); //pr($r);
	if($r)foreach($r as $k=>$v){
		$ra[$v['id']]=[$v['tit'],$v['typ']];
		$pid=$v['pid'];
		if($pid)$rb[$v['id']][$v['pid']]=1; else $rb[$v['id']]=1;
	}
	//pr($rb);//pr($ra);
	$rc=taxonomy($rb);// pr($rc);
	return self::read_recursive($rc,$ra);}

static function stream($p){
	$p['t']=self::$cols[1];
	return appx::stream($p);}

#call (read)
static function tit($p){
	$p['t']=self::$cols[1];
	return appx::tit($p);}

static function call($p){
	return appx::call($p);}

#com (edit)
static function com($p){
	return appx::com($p);}

#interface
static function content($p){
	self::install();
	return appx::content($p);}
}
?>