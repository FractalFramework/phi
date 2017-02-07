<?php

Sql::connect();

class Sql{
private static $prefix='usr';
private static $table;
private static $dbq;
static function table($b){return self::$prefix.'_'.$b;}
static function connect(){require('cnfg/connect.php'); self::$dbq=$dbq;}

#read
static function query($sql,$z=''){if($z)echo $sql;
	$rq=mysqli_query(self::$dbq,$sql);
	if($rq==null)echo mysqli_error(self::$dbq).br().$sql.hr(); return $rq;}
static function qfar($r){if($r)return mysqli_fetch_array($r);}
static function qfas($r){if($r)return mysqli_fetch_assoc($r);}
static function qfrw($r){if($r)return mysqli_fetch_row($r);}//return mysqli_insert_id();

#treat
private static function sqlformat($rq,$p){$ret=array();
if($p=='rq')return $rq;
if($p=='ry')return self::qfar($rq);
if($p=='ra')return self::qfas($rq);
if($p=='rw')return self::qfrw($rq);
if($p=='v'){$r=self::qfrw($rq); return $r[0];}
if($p=='rr'){while($r=mysqli_fetch_assoc($rq))$ret[]=$r; return $ret;}
while($r=mysqli_fetch_row($rq))if($r[0])switch($p){
	case('k'):$ret[$r[0]]=1; break;
	case('rv'):$ret[]=$r[0]; break;
	case('kv'):$ret[$r[0]]=$r[1]; break;
	case('kr'):$ret[$r[0]][]=$r[1]; break;
	case('kk'):$ret[$r[0]][$r[1]]=1; break;
	case('kkc'):$ret[$r[0]][$r[1]]=1; break;
	case('vv'):$ret[]=array($r[0],$r[1]); break;
	case('kkv'):$ret[$r[0]][$r[1]]=$r[2]; break;
	case('kkr'):$ret[$r[0]][$r[1]][]=$r[2]; break;
	case('kkk'):$ret[$r[0]][$r[1]][$r[2]]=1; break;
	case('kvv'):$ret[$r[0]]=array($r[1],$r[2]); break;
	case('id'):$k=array_shift($r); $ret[$k]=$r; break;
	default:$ret[]=$r; break;}
return $ret;}

//read('id','qda','rv','where id=""');
static function read($d,$b,$p,$q='',$z=''){
if(substr($d,-6)=='timeup')$d=substr($d,0,-6).'UNIX_TIMESTAMP('.$b.'.up) as time';
if(substr($d,-6)=='dateup')$d=substr($d,0,-6).'DATE_FORMAT('.$b.'.up,"%d/%m/%Y") as date';
if($d=='all')$d=self::columns($b,2);
$sql='select '.$d.' from '.$b.' '.$q;
$rq=self::query($sql,$z);
if($rq){$ret=self::sqlformat($rq,$p);
	mysqli_free_result($rq);
	return $ret;}}

//join b2 to b1, associating b2.$key to b1.id
static function read_inner($d,$b1,$b2,$key,$p,$q='',$z=''){
$q='left join '.$b2.' on '.$b1.'.'.$key.'='.$b2.'.id '.$q;
return self::read($d,$b1,$p,$q,$z);}

#modif
static function modif($r,$act,$n,$ra,$nb=''){switch($act){
case('arr'):$r=$ra; break;
case('add'):$r[]=$ra; break;
case('mdf'):$r[$n]=$ra; break;
case('del'):unset($r[$n]); break;
case('mdv'):$r[$n][$nb]=$ra; break;
case('push'):array_unshift($r,$ra); break;
case('app'):foreach($ra as $k=>$v){if($n=='mdf')$r[$k]=$v;
	elseif($n=='del')unset($r[$k]); else $r[]=$v;} break;}
return $r;}

static function escape($v){
	if(ses('enc'))$v=($v); else $v=html_entity_decode($v);//utf8_decode
	return mysqli_real_escape_string(self::$dbq,stripslashes($v));}

//array(1=>'hello',2=>hey)
static function mysql_array($r,$o=''){
	foreach($r as $k=>$v){
		if(substr($v,0,8)=='PASSWORD')$rb[$k]=$v;
		else $rb[$k]='"'.self::escape($v).'"';}
	if($o)return '('.implode(',',$rb).')';
	else return '(NULL,'.implode(',',$rb).',"'.date('Y-m-d H:i:s',time()).'")';}

//array(array(1,'hello'),array(2,hey))
static function mysql_array2($r,$o=''){
	foreach($r as $k=>$v)$rb[]=self::mysql_array($v,$o);
	return implode(',',$rb);}

static function insert2($b,$r,$o='',$z=''){
	if($o){self::backup($b); self::trunc($b);}
	$sql='insert into '.$b.' values '.self::mysql_array2($r,$o);
	$rq=self::query($sql,$z); return mysqli_insert_id(self::$dbq);}

#update
static function insert($b,$r,$z=''){
	$sql='insert into '.$b.' values '.self::mysql_array($r);
	$rq=self::query($sql,$z); return mysqli_insert_id(self::$dbq);}
static function select($s,$b,$w=''){
	self::query('select '.$s.' from '.$b.' '.$w);}
static function update($b,$d,$v,$id,$col='',$z=''){$col=$col?$col:'id';
	self::query('update '.$b.' set '.$d.'="'.self::escape($v).'" where '.$col.'="'.$id.'"',$z);}
static function updates($b,$r,$id,$z=''){$com='';
	foreach($r as $k=>$v)$rb[]=$k.'="'.self::escape($v).'"';
	self::query('update '.$b.' set '.implode(',',$rb).' where id="'.$id.'"',$z);}
static function reflush($b,$o=''){
	self::query('alter table '.$b.' order by id');}
static function reflush_ai($b){$id=self::lastid($b)+1;
	self::query('alter table '.$b.' auto_increment='.$id.'');}
static function lastid($b){
	return self::read('id',$b,'v','order by id desc limit 1');}
static function delete($b,$id,$col=''){$col=$col?$col:'id';
	self::query('delete from '.$b.' where '.$col.'="'.$id.'"');}
static function trunc($b){self::query('truncate '.$b);}
static function drop($b){self::query('drop table '.$b);}
static function transpose($b,$bb,$r){$bb=$b.'_bak'.date('ymdHis');
	self::query('create table '.$bb.' like '.$b);
	self::query('insert into '.$bb.' select * from '.$b); return $bb;}
static function backup($b,$d=''){$bb=$b.'_bak'.$d;
	if(self::exists($bb))self::query('drop table '.$bb);
	self::query('create table '.$bb.' like '.$b);
	//self::query('alter table '.$bb.' add primary key (id)');
	self::query('insert into '.$bb.' select * from '.$b);
	return $bb;}
static function restore($b,$d=''){$bb=$b.'_bak'.$d;
	if(!self::exists($bb))return;
	self::query('truncate '.$b);
	self::query('alter table '.$b.' auto_increment=1');
	self::query('insert into '.$b.' select * from '.$bb.'');
	return $b;}
static function exists($b){
	$rq=self::query('show tables like "'.$b.'"');
	return mysqli_num_rows($rq)>0;}
static function columns($b,$o=''){$rq=self::query('select COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS where table_name="'.$b.'";');
	while($r=mysqli_fetch_assoc($rq)){$type=$r['DATA_TYPE'];
		if($type=='varchar')$type='var'; if($type=='mediumtext')$type='text';
		$rb[$r['COLUMN_NAME']]=$type;}
	if(!isset($rb))return;
	if($o==1 or $o==2){array_shift($rb); array_pop($rb);}
	if($o==2 or $o==3)return implode(',',array_keys($rb));
	else return $rb;}

#update structure
static function trigger($b,$ra){$rb=self::columns($b); $rnew=''; $rold='';
	if(isset($rb['id']))unset($rb['id']); if(isset($rb['up']))unset($rb['up']);	
	if($rb){$rnew=array_diff_assoc($ra,$rb); $rold=array_diff_assoc($rb,$ra);}//old
	if($rnew or $rold){$bb=self::backup($b,date('ymdHis')); self::drop($b);
		$rtwo=array_intersect_assoc($ra,$rb);//common
		$rak=array_keys($ra); $rav=array_values($ra);
		$rnk=array_keys($rnew); $rnv=array_values($rnew); $nn=count($rnk);
		$rok=array_keys($rold); $rov=array_values($rold); $no=count($rok);
		$na=count($rnew); $nb=count($rold); $ca=array_keys($rtwo); $cb=array_keys($rtwo);
		if($na==$nb){
			for($i=0;$i<$nn;$i++){
				if($rnv[$i]==$rov[$i] or $rnv[$i]!='int'){
					$ca[]=$rnk[$i]; $cb[]=$rok[$i];}}}
		return 'insert into '.$b.'(id,'.implode(',',$ca).',up) select id,'.implode(',',$cb).',up from '.$bb;}}

#create
private static function create_cols($r){$ret='';
$collate='collate latin1_general_ci';
foreach($r as $k=>$v)
	if($v=='int')$ret.='`'.$k.'` int(11),'."\n";
	elseif($v=='var')$ret.='`'.$k.'` varchar(255) '.$collate.' NOT NULL default "",';
	elseif($v=='text')$ret.='`'.$k.'` mediumtext '.$collate.',';
	elseif($v=='date')$ret.='`'.$k.'` date NOT NULL,';
return $ret;}

//array('id'=>'int','ib'=>'int','val'=>'var');
static function create($b,$r,$up=''){if(!is_array($r))return; reset($r);
if($up)$sql=self::trigger($b,$r);
self::query('create table if not exists `'.$b.'` (
  `id` int(11) NOT NULL auto_increment,
  '.self::create_cols($r).'
  `up` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM collate latin1_general_ci;');
if(isset($sql))self::query($sql,1);}

#call
static function mkbcp($prm){return self::backup($prm['b']);}
static function rsbcp($prm){return self::restore($prm['b']);}
static function call($params,$db){$ret='';
	$cols=val($params,'cols'); $mode=val($params,'mode');
	$req=val($params,'req'); $see=val($params,'see');
	$cols=str_replace('-',',',$cols);
	return Sql::read($cols,$db,$mode,$req,$see);}

}

?>