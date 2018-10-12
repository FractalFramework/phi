<?php $r=db_read('usr/dav/tabler/solarsystem','','',1); //pr($r);
$ret='';

foreach($r as $k=>$v){
$name=$v[0];
$ds=str_replace(' ','',$v[1]); $ds=$ds/1000000;
$sz=str_replace(' ','',$v[2])/1000;
if($name=='Sun')$sz=50;

$ret.='var '.($name).'=BABYLON.Mesh.CreateSphere("'.($name).'", 10.0, '.$sz.', scene);'.n();

$map=strtolower($name).'map';
$ret.='var '.$map.'= new BABYLON.StandardMaterial("Texture2", scene);'.n();
$ret.=''.$map.'.diffuseTexture = new BABYLON.Texture("http://tlex.fr/usr/solarsys/'.strtolower($name).'.jpg", scene);'.n();
if($name=='Sun')$ret.=$map.'.emissiveColor = new BABYLON.Color3(1, 1, 0);'.n();

if($name=='Earth')$ret.=$map.'.emissiveTexture = new BABYLON.Texture("http://tlex.fr/usr/solarsys/earthcloud.jpg", scene);'.n();

$angle=rand(0,360);//$v[3]
//$angle=45;
$a=deg2rad($angle); 
$x=round(cos($a)*$ds,2);
$y=0;
$z=round(sin($a)*$ds,2);

//echo $v[0].' dist='.$ds.' size='.$sz.' x='.$x.br();

$ret.=''.($name).'.position=new BABYLON.Vector3('.$x.','.$y.','.$z.');'.n();
$ret.=''.($name).'.material='.$map.';'.n();

}
