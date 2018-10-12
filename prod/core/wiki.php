<?php

class wiki{
//https://en.wikipedia.org/w/api.php
//https://www.ibm.com/developerworks/xml/library/x-phpwikipedia/index.html

static function parsoid($d){


return $ret;}

static function api($word){
if(!$word)$word='Main Page';
$lg=ses('lng'); $t=rawurlencode($word);
$http='https://'.$lg.'.wikipedia.org/w/api.php?action=query';
//from prefix
//$http.'&list=allcategories&acprop=size&format=xml&acprefix='.$t;
//search
//$http.'&list=search&srwhat=text&format=xml&srsearch='.$t;
//content::wikistyle
//$http.'&prop=revisions&rvprop=content&format=xml&titles='.$t;
$u=$http.'&titles='.$t.'&prop=revisions&rvprop=content&format=json';//formatversion=2
$d=files::curl($u);
$r=json_decode($d,true);
return $r;}

#reader
static function build($p){
$r=self::api(val($p,'word')); //pr($r);
return $r;}

static function read($p){
$r=self::build($p);
$rb=$r['query']['pages']; 
$k=key($rb); //pr($rb[$k]);
$text=$rb[$k]['revisions'][0]['*'];
if(substr($text,0,12)=='#REDIRECTION')return self::read(['word'=>substr($text,15,-2)]);
//$text=decode($text);
return div($text,'pane');}

//com (apps)
static function com($p){
$r=self::build($p);
return $r['text'][0];}

//interface
static function content($p){
$rid=randid('yd');
$p['txt']=val($p,'txt',val($p,'p'));
$ret=input('word',$p['txt']);
$ret.=aj($rid.'|wiki,read||word',lang('open'),'btn');
return $ret.div('','board',$rid);}
}
?>