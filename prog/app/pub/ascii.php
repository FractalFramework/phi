<?php

class ascii{
	static $private='1';
	
	static function vars(){return '&acute; &oelig; &Scaron; &scaron; &Yuml; &Agrave; &Aacute; &Acirc; &Atilde; &Auml; &Aring; &AElig; &Ccedil; &Egrave; &Eacute; &Ecirc; &Euml; &Igrave; &Iacute; &Icirc; &Iuml; &ETH; &Ntilde; &Ograve; &Oacute; &Ocirc; &Otilde; &Ouml; &Oslash; &Ugrave; &Uacute; &Ucirc; &Uuml; &Yacute; &THORN; &szlig; &agrave; &aacute; &acirc; &atilde; &auml; &aring; &aelig; &ccedil; &egrave; &eacute; &ecirc; &euml; &igrave; &iacute; &icirc; &iuml; &eth; &ntilde; &ograve; &oacute; &ocirc; &otilde; &ouml; &oslash; &ugrave; &uacute; &ucirc; &uuml; &yacute; &thorn; &yuml; &#9825; &#9788; &#9787; &#9786; &#9785; &#9775; &#9774; &#9762; &#9760; &#9759; &#9758; &#9757; &#9756; &#9755; &#9754; &#9752; &#9749; &#9748; &#9746; &#9745; &#9744; &#9743; &#9742; &#9741; &#9740; &#9739; &#9738; &#9737; &#9736; &#9735; &#9734; &#9733; &#9732; &#9731; &#9730; &#9729; &#9728; &hearts; &iexcl; &sect; &laquo; &shy; &macr; &para; &middot; &cedil; &raquo; &iquest; &bull; &hellip; &oline; &circ; &tilde; &ensp; &emsp; &thinsp; &zwnj; &zwj; &lrm; &rlm; &ndash; &mdash; &lsquo; &rsquo; &sbquo; &ldquo; &rdquo; &bdquo; &dagger; &Dagger; &permil; &lsaquo; &rsaquo; &brvbar; &copy; &reg; &deg; &micro; &#10217; &#10216; &rfloor; &lfloor; &rceil; &lceil; &OElig; &weierp; &image; &real; &trade; &alefsym; &#9800; &#9801; &#9802; &#9803; &#9804; &#9805; &#9806; &#9807; &#9808; &#9809; &#9810; &#9811; &larr; &uarr; &rarr; &darr; &harr; &crarr; &lArr; &uArr; &rArr; &dArr; &hArr; &#9832; &#9823; &#9822; &#9821; &#9820; &#9819; &#9818; &#9817; &#9816; &#9815; &#9814; &#9813; &#9812; &#9799; &#9798; &#9797; &#9796; &#9795; &#9794; &#9793; &#9792; &#9791; &#9790; &#9789; &#9784; &#9783; &#9782; &#9781; &#9780; &#9779; &#9778; &#9777; &#9776; &#9773; &#9772; &#9771; &#9770; &#9769; &#9768; &#9767; &#9766; &#9765; &#9764; &#9763; &#9761; &#9753; &#9751; &#9750; &#9747; &Alpha; &Beta; &Gamma; &Delta; &Epsilon; &Zeta; &Eta; &Theta; &Iota; &Kappa; &Lambda; &Mu; &Nu; &Xi; &Xi; &Omicron; &Pi; &Rho; &Sigma; &Tau; &Upsilon; &Phi; &Chi; &Psi; &Omega; &alpha; &beta; &gamma; &delta; &epsilon; &zeta; &eta; &iota; &kappa; &lambda; &mu; &nu; &xi; &omicron; &pi; &rho; &sigmaf; &sigma; &tau; &upsilon; &phi; &chi; &psi; &omega; &thetasym; &upsih; &piv; &ordf; &plusmn; &sup2; &sup3; &sup1; &ordm; &frac14; &frac12; &frac34; &times; &divide; &forall; &part; &exist; &empty; &nabla; &isin; &notin; &ni; &prod; &sum; &minus; &lowast; &radic; &prop; &infin; &ang; &and; &or; &cap; &cup; &int; &there4; &sim; &cong; &asymp; &ne; &equiv; &le; &ge; &sub; &sup; &nsub; &sube; &supe; &oplus; &otimes; &perp; &sdot; &frasl; < > &#9831; &#9828; &loz; &#9825; &spades; &clubs; &diams; &hearts; &yen; &pound; &euro; &cent; $ &#9839; &#9838; &#9837; &#9836; &#9835; &#9834; &#9833; &#9834; &#9835; &#9836; &#9850; &#9851; &#9855; &#9872; &#9873; &#9874; &#9875; &#9879; &#9881; &#9888; &#9893; &#9898; &#9899; &#9940; &#9986; &#9993; &#9997; &#9998; &#9999; &#10004; &#10005; &#10006; &#10059; &#10060; &#10084; &#10133; &#10134; &#10135; &#10140; &#10145; &#10154; &#11035; &#11036; &#127383; &#127987; &#127988; &#128161; &#128172; &#128193; &#128194; &#128196; &#128198; &#128200; &#128201; &#128214; &#128215; &#128216; &#128217; &#128220; &#128221; &#128230; &#128231; &#128240; &#128274; &#128275; &#128278; &#128295; &#128308; &#128514; &#128515; &#128522; &#128525; &#128528; &#128556; &#128577; &#128578; &#128683;';}
	
	static function all($v){$ret='';
		$u=50000; $n=$u*($v-1); $max=$n+$u;
		for($i=$n;$i<$max;$i++)
			//$ret.=mb_convert_encoding('&#'.$i.';','HTML-ENTITIES','UTF-8').' ';
			$ret.=$i.' ';
		return $ret;
	}
	
	static function content($p){
		$id=val($p,'id'); $all=val($p,'all'); $ret='';
		for($i=1;$i<5;$i++)
			$ret.=aj('popup,,xy|ascii|popwidth:500px,all='.$i.',id='.$id,$i,'btn '.($i==$all?'active':''));
		if($all)$d=self::all($all); else $d=self::vars();
		//heart:&#10084;&#65039;
		$r=explode(' ',$d);
		foreach($r as $v)
			$ret.=btj('&#'.$v.';','insert(\'&#'.$v.';\',\''.$id.'\');','btn').' ';
		return div($ret,'','ascii');
	}
}
?>
