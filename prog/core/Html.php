<?php

class Html{
	static function read($f){//$d=file_get_contents($f); //$r=unserialize($d); //pr($r);
	//pr(get_headers($f));
	}
	
	static function contents(){return array('<div class="entry-content">','<div class="entry">','<div class="texte">','<div class="contenuArticle">','<div class="post-content">','<div class="content">',"<div class='post-body entry-content'",'<div class="post-body entry-content"','<div class="post-text">','<div id="content">','<div class="article">','<div class="post">','<div class="entry-content clearfix">','<div class="article-body">','<div class="surlignable">','<div class="chapo">','<div class="article-content">','<div class="post-header">','<div class="entrytext">','<section class="entry-content clearfix" itemprop="articleBody">','<div class="content clearfix">','<div class="main">','<div class="post-body">','<div class="post hentry">','<div align="justify">','<div class="text">','<div class="entry-body">','<article');}

	static function metas($f){$d=File::read($f); if(!$d)return; $ds=strtolower($d);
		$d=mb_ereg_replace("[ ]{2,}",' ',$d);
		$enc=segment($ds,'charset=','"'); if(!$enc)$enc=mb_detect_encoding($d);
		if(strtolower($enc)=='utf-8')$d=mb_convert_encoding($d,'HTML-ENTITIES','UTF-8');
		$tit=segment($d,'<meta property="og:title" content="','"');
		if(!$tit)$tit=segment($d,"<meta property='og:title' content='","'");
		if(!$tit)$tit=segment($d,'<meta property="title" content="','"');
		if(!$tit)$tit=segment($d,'<meta itemprop="name" content="','"');
		if(!$tit)$tit=segment($d,'<title>','</title>');
		$txt=segment($d,'<meta property="og:description" content="','"');
		if(!$txt)$txt=segment($d,"<meta property='og:description' content='","'");
		if(!$txt)$txt=segment($d,'<meta property="description" content="','"');
		if(!$txt)$txt=segment($d,'<meta itemprop="description" content="','"');
		if(!$txt)$txt=segment($d,'<meta name="description" content="','"');
		$img=segment($d,'<meta property="og:image" content="','"');
		if(!$img)$img=segment($d,"<meta property='og:image' content='","'");
		if(!$img)$img=segment($d,'<meta property="image" content="','"');
		if(!$img)$img=segment($d,'<link itemprop="thumbnailUrl" href="','"');
		if(strpos($tit,'<'))$tit='';if(strpos($txt,'<'))$txt='';if(strpos($img,'<'))$img='';
		return array($tit,$txt,$img);}

}

?>