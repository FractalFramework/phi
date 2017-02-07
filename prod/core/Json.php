<?php

class Json{

    static function error(){
	switch(json_last_error()){
		case JSON_ERROR_NONE:$ret=' - Aucune erreur';break;
		case JSON_ERROR_DEPTH:$ret=' - Profondeur maximale atteinte';break;
		case JSON_ERROR_STATE_MISMATCH:$ret=' - Inadéquation des modes ou underflow';break;
		case JSON_ERROR_CTRL_CHAR:$ret=' - Erreur lors du contrôle des caractères';break;
		case JSON_ERROR_SYNTAX:$ret=' - Erreur de syntaxe ; JSON malformé';break;
		case JSON_ERROR_UTF8:$ret=' - Caractères UTF-8 malformés';break;
		default:$ret=' - Erreur inconnue';break;}
	return $ret;}

	static function build($r){
		return json_encode($r);
	}
	
	static function content($p){
		$app=val($p,'app'); ses('app',$app);
		//echo json_encode($r);//JSON_FORCE_OBJECT|//,JSON_UNESCAPED_UNICODE
		//echo self::error();
		return json_r($r);
	}
	
}

?>