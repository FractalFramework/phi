<?php
//walk-phi-telex
#admin
if(isset($params['api']))$admin='';
elseif($app=='telex')$admin='';
elseif($app=='login')$admin='';
else $admin=App::open('Admin',$params);
#headers
Head::add('code','<base'.atb('href',$_SERVER['HTTP_HOST']).' />');
Head::add('meta',array('attr'=>'http-equiv','prop'=>'Content-Type','content'=>'text/html; charset=utf-8'));//iso-8859-1//according to cfng
Head::add('tag',array('title','',lang('Telex_title')));
Head::add('rel',array('name'=>'shortcut icon','value'=>'/favicon.ico'));
/*Head::add('meta',array('attr'=>'property','prop'=>'og:title','content'=>$title));
Head::add('meta',array('attr'=>'property','prop'=>'og:description','content'=>$description));
Head::add('meta',array('attr'=>'property','prop'=>'og:image','content'=>$image));*/
Head::add('meta',array('attr'=>'name','prop'=>'generator','content'=>'walk-phi'));
Head::add('meta',array('attr'=>'name','prop'=>'version','content'=>'1610'));
Head::add('meta',array('attr'=>'name','prop'=>'viewport','content'=>'user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width'));
Head::add('jslink','/js/ajax.js');
Head::add('jslink','/js/utils.js');
Head::add('csslink','/css/global.css');
Head::add('csslink','/css/pictos.css');
Head::add('csslink','/css/main.css');
Head::add('csslink','/css/fa.css');
//if(method_exists($app,'headers'))$app::headers();
#content
$content=App::open($app,$params);
stats::save($app,$params);
#render
$ret=Head::generate();
$ret.='<body onmousemove="popslide(event)" onmousedown="closebub(event)">'."\n";//
$ret.=tag('div',array('id'=>'closebub','onclick'=>'bubClose()'),'');
$ret.=tag('div',array('id'=>'admin'),$admin);
$ret.=tag('div',array('id'=>'page'),$content);
$ret.=tag('div',array('id'=>'popup'),'');
$ret.='</body>';
echo encode($ret);
?>