<?php

/*need to place app component befor headers in index to let ability to the App to decide of them*/
class Meta{
	static $title='Telex';
	static $description='Telex is an objects social network';
	static $image='http://ph1.fr/icon/comment-square-8x.png';

	static function set($t,$d,$v){
		self::$title=$t;
		self::$description=$d;
		self::$image=$v;
	}

	static function get(){
		return array(self::$title,self::$image,self::$description);
	}
}

?>