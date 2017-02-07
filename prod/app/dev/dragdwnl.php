<?php

class dragdwnl{
	static $private='1';
	static $db='telex';
	
	static function injectJs(){
		return "
//upload
//https://www.devbridge.com/sourcery/components/drag-and-drop-uploader/
html5Upload.initialize({
	// URL that handles uploaded files
	uploadUrl: '/file/upload',
	// HTML element on which files should be dropped (optional)
	dropContainer: document.getElementById('dragndropimage'),
	// HTML file input element that allows to select files (optional)
	inputField: document.getElementById('upload-input'),
	// Key for the file data (optional, default: 'file')
	key: 'File',
	// Additional data submitted with file (optional)
	data: { ProjectId: 1, ProjectName: 'Demo' },
	// Maximum number of simultaneous uploads
	// Other uploads will be added to uploads queue (optional)
	maxSimultaneousUploads: 2,
	// Callback for each dropped or selected file
	// It receives one argument, add callbacks 
	// by passing events map object: file.on({ ... })
	onFileAdded: function (file) {
		var fileModel = new models.FileViewModel(file);
		uploadsModel.uploads.push(fileModel);
		file.on({
			// Called after received response from the server
			onCompleted: function (response) {
				fileModel.uploadCompleted(true);
			},
			// Called during upload progress, first parameter
			// is decimal value from 0 to 100.
			onProgress: function (progress, fileSize, uploadedBytes) {
				fileModel.uploadProgress(parseInt(progress, 10));
			}
		});
	}
});";}
	static function headers(){
		Head::add('csscode','');
		Head::add('jscode',self::injectJs());}
	static function admin(){
		$r[]=array('','j','popup|drag,content','plus',lang('open'));
		return $r;}
	static function install(){
		Sql::create('drag',array('mid'=>'int','mname'=>'var'),0);}//1=update
	static function titles($p){
		$d=val($p,'appMethod');
		$r['content']='welcome';
		$r['build']='drag';
		if(isset($r[$d]))return lang($r[$d]);}
	
	//edit
	static function edit(){
		$ret=Form::com(['table'=>self::$db]);}
	
	//builder
	static function build($p){
		$ret=' <div id="dragndropimage" class="uploadimage-dragndrop">
            <div class="uploadimage-text">Drag images here</div>
            <div>Or, if you prefer...</div>
            <div class="uploadimage-input">
                <input type="file" multiple="multiple" name="uploadFiles" id="upload-input" />
            </div>
        </div>

        <div id="upload-liveuploads" data-bind="template: { name: \'template-uploads\' }"></div>

        <div class="description">
            <h2>RequireJS, Knockout and Uploader Harmony</h2>
            <p>
                Scripts are loaded using module loader <a href="http://requirejs.org/">RequireJS</a>. This demo 
                demonstrates how to use <a href="https://github.com/devbridge/html5-file-uploader">html5 ajax uploader</a> and <a href="http://knockoutjs.com/">knockout</a> to customize UI for uploads. 
            </p>
            
            <h2>Show me the code</h2>
            <script type="text/javascript" src="https://gist.github.com/4028759.js"></script>
        </div>

        <script type="text/html" id="template-uploads">
            <div data-bind="visible: showTotalProgress()">
                <div>
                    <span data-bind="text: uploadSpeedFormatted()"></span>
                    <span data-bind="text: timeRemainingFormatted()" style="float: right;"></span>
                </div>
                <div class="uploadimage-totalprogress">
                    <div class="uploadimage-totalprogressbar" style="width: 0%;" data-bind="style: { width: totalProgress() + \'%\' }"></div>
                </div>
            </div>
            <div data-bind="foreach: uploads">
                <div class="uploadimage-upload" data-bind="css: { \'uploadimage-uploadcompleted\': uploadCompleted() }">
                    <div class="uploadimage-fileinfo">
                        <strong data-bind="text: fileName"></strong>
                        <span data-bind="text: fileSizeFormated"></span>
                        <span class="uploadimage-progresspct" data-bind="visible: uploadProgress() < 100"><span data-bind="text: uploadSpeedFormatted()"></span></span>
                    </div>
                    <div class="uploadimage-progress">
                        <div class="uploadimage-progressbar" style="width: 0%;" data-bind="style: { width: uploadProgress() + \'%\' }"></div>
                    </div>
                </div>
            </div>
        </script>


        <script type="text/javascript" data-main="/scripts/main" src="/Scripts/require.js"></script>

</div>';
		return $ret;}
	
	//interface
	static function content($p){
		//self::install();
		$p['rid']=randid('md');
		$p['p1']=val($p,'param',val($p,'p1'));
		//$ret=hlpbt('drag');
		$ret=input('inp1',$p['p1'],'10',1);
		$ret.=aj('popup|drag,build|msg=text|inp1',lang('send'),'btn');
		return div($ret,'',$p['rid']);}
}
?>
