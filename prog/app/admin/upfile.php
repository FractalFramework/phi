<?php

class upfile{
	static $private='1';
	
	static function injectJs(){
		return '
		function upload(p){
			var form=getbyid("upl");
			var fileSelect=getbyid("upfile");
			var formData=new FormData();
			var files=fileSelect.files;
			for(var i=0;i<files.length;i++){
				var file=files[i];
				if(!file.type.match("image.*"))continue;
				formData.append("upfile",file,file.name);
				//insert(file.name,"div");
			}
			if(p)var prm="getinp:1"; else var prm="";
			var url="/call.php?appName=Upload&appMethod=save&params="+prm;
			ajax=new AJAX(url,"after","upbck","load",formData);
		}
		';}
		
	static function headers(){
		Head::add('csscode','
		label.uplabel input[type=file]{position:fixed; top:-100px;}
		');
		Head::add('jscode',self::injectJs());}
	
	//interface
	static function content($p){
		$p['rid']=randid('md');
		$ret=Upload::call();
		return div($ret,'',$p['rid']);}
}
?>
