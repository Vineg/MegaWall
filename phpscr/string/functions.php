<?php

function mysql_print($s){
	print mb_convert_encoding($s,"windows-1251");
}
function escape_ar($s, $escape_chars) {
	for($i = 0; $i < count ( $escape_chars ); $i ++) {
		$s = escape ( $s, $escape_chars [$i], $i == 0 );
	}
}
;
function escape($s, $char = "\"", $first_time = true) {
	if ($first_time) {
		$res = str_replace ( "\\", "\\\\", $s );
	}
	$res = str_replace ( $char, "\\" . $char, $s );
	return $res;
}
function contains($s, $arr) {
	return in_array ( $s, $arr );
}
function split_by_array($s, $arr) {
	$res = array ($s );
	for($i = 0; $i < count ( $arr ); $i ++) {
		$res = split_pieces ( $res, $arr [$i] );
	}
	return $res;
}

function split_pieces($needle_arr, $delim) {
	$res = array ();
	for($i = 0; $i < count ( $needle_arr ); $i ++) {
		$res = array_merge ( $res, explode ( "$delim", $needle_arr [$i] ) );
	}
	return $res;
}

function split_arrayin($needle_arr, $delim) {
	$res = array ();
	for($i = 0; $i < count ( $needle_arr ); $i ++) {
		$res [$i] = explode ( "$delim", $needle_arr [$i] );
	}
	return $res;
}

function split_array($needle_arr, $delim) {
	split_pieces ( $needle_arr, $delim );
}

function get_extension_from_header($head) {
	$ctype = $head ["Content-Type"];
	$ctype = h2vs ( $ctype );
	$type = end ( explode ( "/", $ctype ) );
	$type2s = array ("x-shockwave-flash" => "swf" );
	if ($type2s [$type]) {
		$ext = $type2s [$type];
	} else {
		$ext = $type;
	}
	return $ext;
		//return end(explode(".", $filename));
}

function ctype_to_ext($ctype) {
	if (strripos ( $ctype, "/" ) !== false) {
		return substr ( $ctype, strripos ( $ctype, "/" ) );
	}
}

function check_urls_h($s, $params = array()) {
	$pos = 0;
	$endpos = 0;
	while ( true ) {
		$url = "";
		if ($endpos >= strlen ( $s )) {
			break;
		}
		$bstr = " src=\"";
		$pos = stripos ( $s, $bstr, $endpos );
		$poss = $pos + strlen ( $bstr );
		if ($poss > strlen ( $s )) {
			$poss = strlen ( $s );
		}
		$endpos = stripos ( $s, "\"", $poss );
		if ($endpos === false) {
			$endpos = strlen ( $s );
		}
		if ($pos !== false) {
			$url = substr ( $s, $poss, $endpos - $poss );
			$nurl = checkh ( $url, array ($params ) );
			$ht = "http://" . vars::$host;
			//cho $url.$ch."<br />";
			if ($nurl === false) {
				$s = substr ( $s, 0, $pos ) . $ht . " rsrc=\"$url\"" . substr ( $s, $endpos + 1 );
				$endpos = $pos + 1 + strlen ( $ht . "$url" );
			} else if ($nurl !== true) {
				if ($params [post_id]) {
					change_post_url ( $params [post_id], $url, $nurl );
				}
				$strbegin = substr ( $s, 0, $pos ) . " src=\"";
				$strend = "\"" . substr ( $s, $endpos + 1 );
				$s = "$strbegin$nurl$strend";
				$endpos = strlen ( $strbegin . $nurl ) + 1;
			}
			;
		} else {
			break;
		}
	}
	return $s;
}

function load_urls($s) {
	$pos = 0;
	$endpos = 0;
	while ( true ) {
		$url = "";
		if ($endpos >= strlen ( $s )) {
			break;
		}
		$pos = stripos ( $s, "src=\"", $endpos );
		$poss = $pos + 5;
		if ($poss > strlen ( $s )) {
			$poss = strlen ( $s );
		}
		$endpos = stripos ( $s, "\"", $poss );
		if ($endpos === false) {
			$endpos = strlen ( $s );
		}
		if ($pos !== false) {
			$url = substr ( $s, $poss, $endpos - $poss );
			$ch = load_file ( $url );
			//cho $url.$ch."<br />";
			if ($ch === false) {
				$s = substr ( $s, 0, $pos ) . substr ( $s, $endpos + 1 );
				$endpos = $pos + 1;
			} else if ($ch !== true) {
				$ht = "http://" . vars::$host;
				$s = substr ( $s, 0, $pos ) . "src=\"$ch\"" . substr ( $s, $endpos + 1 );
				$endpos = $pos + 1 + strlen ( $ht . "$ch" );
			}
			;
		} else {
			break;
		}
	}
	return $s;
}

function my_explode($s, $delim, $offset = 0) {
	//cho $s."<br />";
	$nd = stripos ( $s, $delim, $offset );
	$nsl = stripos ( $s, "\\", $offset );
	if ($nsl === false) {
		$nsl = INF;
	}
	if ($nd === false) {
		//cho $s."<br />";
		return array (remsl ( $s ) );
	}
	if ($nd < $nsl) {
		$res [count ( $res )] = substr ( $s, 0, $nd );
		$res = array_merge ( $res, my_explode ( substr ( $s, $nd + 1 ), $delim ) );
	} else {
		if ($nsl < strlen ( $s )) {
			$nslc = substr ( $s, $nsl + 1, 1 );
		}
		//		if($nslc=="\\"||$nslc==$delim){
		$s = substr ( $s, 0, $nsl ) . substr ( $s, $nsl + 1 );
		$res = my_explode ( $s, $delim, $nsl + 1 );
	
		//		}else{
	//			$res=my_explode($s, $delim, $nsl+2);
	//		}
	}
	return $res;
}

function remsl($s, $offset = 0) {
	$nsl = stripos ( $s, "\\", $offset );
	if ($nsl === false) {
		return $s;
	}
	$s = substr ( $s, 0, $nsl ) . substr ( $s, $nsl + 1 );
	$res = remsl ( $s, $nsl + 1 );
	return $res;
}

function my_implode($ar, $glue, $nbs = false) {
	if (! $ar) {
		return false;
	}
	for($i = 0; $i < count ( $ar ); $i ++) {
		$as = $ar [$i];
		if (! $nbs) {
			$as = str_replace ( "\\", "\\\\", $as );
		}
		$as = str_replace ( "$glue", "\\$glue", $as );
		$ar [$i] = $as;
	}
	return implode ( $glue, $ar );
}

// function ar2ar($ar, $sdelim){


// }


function s2ar($s, $delim, $sdelim = false, $clean = true, $optional_key = false) {
	$res = array ();
	if (! $sdelim) {
		return explode ( "$delim", $s );
	}
	$ar = explode ( $delim, $s );
	//rint_r($ar);
	for($i = 0; $i < count ( $ar ); $i ++) {
		//cho $ar[$i],"<br />";
		$nar = explode ( $sdelim, $ar [$i] );
		//rint_r($ar[$i]);
		if (! $clean || $nar [0]) {
			if ($clean) {
				$nar [0] = cut_spaces ( $nar [0] );
				if ($nar [1]) {
					$nar [1] = cut_spaces ( $nar [1] );
				}
			}
			if ($optional_key && $nar [1] === null) {
				$res [] = $nar [0];
			} else {
				//cho $ar[$i]+"\r\n";
				$res [$nar [0]] = $nar [1];
			}
		}
	}
	return $res;
}

function ar2s($ar, $glue, $glue2 = false) {
	if (! $glue2) {
		return my_join ( $glue, $ar );
	}
	$resar = array ();
	foreach ( $ar as $key => $value ) {
		$rp = $glue2 . $value;
		if (! $value) {
			if ($value === null) {
				continue;
			} else {
				$rp = "";
			}
		}
		$resar [] = "$key$rp";
	}
	//cho my_join($glue, $resar);
	return my_join ( $glue, $resar );
}

function replace_php_vars($s, $vars) {
	//cho h2s($s)."<br />";
	//	if(!empty($s)){
	//		$cond=$s;
	//		$reg='[{$]([a-zA-Z_0-9]+)[}]';
	//		if(ereg($reg,$cond,$carr)){
	//			$from="{\$$carr[1]}";
	//			$to=$vars[$carr[1]];
	//			$reg1=$from;
	//			$findpos=stripos($s, $from);
	//			$cond=substr($cond,0,$findpos)."$to".replace_php_vars(substr($cond,$findpos+strlen($from)), $vars);
	//
	//		}
	$ar = $vars;
	return preg_replace ( "/{\\\$(\w+)}/e", "\$ar[\\1]", $s );

		//	cho ereg_replace('\$script([^a-zA-Z_0-9])', "1", " \$script ");
}

function purify($s) {
	require_once 'phpscr/purifier/HTMLPurifier.auto.php';
	
	$config = HTMLPurifier_Config::createDefault ();
	$purifier = new HTMLPurifier ( $config );
	
	//$s="<form><input></input></form>";
	$config->set ( 'Output.FlashCompat', true );
	$config->set ( 'URI.IframeHostWhitelist', array ("vkontakte.ru", "vk.com", "armorgames.com", "youtube.com", "google.com", "rutube.ru", "www.youtube.com" ) );
	$config->set ( 'HTML.SafeIframe', true );
	$config->set ( 'HTML.SafeObject', true );
	$config->set ( 'HTML.SafeEmbed', true );
	$config->set ( 'Attr.EnableID', true );
	$config->set ( 'HTML.AllowedAttributes', 'data,href,style,src,height,width,name,value,align,type,class,codebase,classid,frameborder, colspan,rowspan, value, placeholder,content, flashvars' );
	$config->set ( 'HTML.Allowed', 'iframe, embed[allowfullscreen,quality,flashvars,pluginspage], object[type, codebase, classid], param[name,value], nobr, input, form, video, meta' );
	//cho h2s($s);
	//$config->set('HTML.Allowed', 'object[width|height|data],param[name|value],embed[src|type|allowscriptaccess|allowfullscreen|width|height],img');
	//EOQ;
	$clean_html = $purifier->purify ( $s );
	return ($clean_html);
}

function format_text($s) {
	$s = str_replace ( " - ", " &mslash; ", $s );
	preg_match_all ( "#((https?|ftp)://[\w\-.]+\.[\w]+[/\w\?&%]+)(\|([\w]+))?#", $s, $matches );
	$links = $matches [1];
	$newlink;
	foreach ( $links as $link ) {
		if (get_host ( $link ) != $_SERVER [HTTP_HOST]) {
			$id = newurl ( $link );
			$newlink [$link] = "http://" . $_SERVER [HTTP_HOST] . "/loc/$id";
		}
	}
	$s = preg_replace(
"#((https?|ftp)://[\w\-.]+\.[\w]+[/\w\?&%]+)(\|([\w]+))?#e",
"\"<a href='\".\$newlink['\\1'].\"'>\".('\\4'?'\\4':shorten_string('\\1')).\"</a>\""
,$s);
	return $s;

}

function edittext($s, $params = null) {
	//cho htmlspecialchars($s)."????????????????";
	$ar1 = explode ( "<", $s );
	for($i = 0; $i < count ( $ar1 ); $i ++) {
		$ar2 = explode ( ">", $ar1 [$i] );
		if ($ar2 [1] === null) {
			$text = $ar2 [0];
		} else {
			$tag = $ar2 [0];
			$text = $ar2 [1];
		}
		if ($params [brreplace] == true) {
			$text = brreplace ( $text );
		}
		if ($params ["final"] == true) {
			$tag = adrreplace ( $tag, $params ["from"] );
			if (! $params ["nocheckuri"]) {
				$tag = check_urls ( $tag );
				$tag = check_urls_h ( $tag );
			}
		}
		$tag = $tag ? "<$tag>" : "";
		$res .= "$tag$text";
	}
	return $res;
}

function brreplace($s) {
	return str_replace ( "\n", "<br />", $s );
}

function adrreplace($s, $url = "") {
	//cho htmlspecialchars($s)."@@@@@@@@@";
	$folder = get_url_folder ( $url );
	$host = get_host ( $url );
	$host = s2link::translit ( $host );
	if ($host == "") {
		return $s;
	}
	$s = preg_replace ( "/([^%a-zA-Z_0-9:\/<> ])[\/]/", "\\1http://" . $host . "/", $s );
	$s = preg_replace ( "/(src|href)=\"([a-zA-Z_0-9%])([^:]*)\"/", "\\1=\"" . $folder . "\\2\\3\"", $s );
	//cho "".htmlspecialchars($s)."!!!!!!!!!!!!!!!".htmlspecialchars($res)."$$$$$$$$$$$$";
	return $s;
}

function hide_blocks($xml, $hideall = false) {
	$xml = addembedparent ( $xml );
	
	hide ( $xml, "object", $hideall );
	hide ( $xml, "iframe", $hideall );
	
	//cho htmlspecialchars(rxml($xml->saveHTML()));
	return $xml;
}

function rembody($s) {
	$res = preg_replace ( "/<!doctype([^>]*)>/i", "", $s );
	$res = preg_replace ( "/<body([^>]*)>/i", "", $res );
	$res = str_replace ( "</body>", "", $res );
	$res = str_replace ( "<head>", "", $res );
	$res = str_replace ( "</head>", "", $res );
	$res = str_replace ( "<title>", "", $res );
	$res = str_replace ( "</title>", "", $res );
	$res = str_replace ( "<html>", "", $res );
	$res = str_replace ( "</html>", "", $res );
	return $res;
}

function cerrarTag($tag, $xml) {
	$indice = 0;
	while ( $indice < strlen ( $xml ) ) {
		$pos = strpos ( $xml, "<$tag ", $indice );
		if ($pos) {
			$posCierre = strpos ( $xml, ">", $pos );
			if ($xml [$posCierre - 1] == "/") {
				$xml = substr_replace ( $xml, "></$tag>", $posCierre - 1, 2 );
			}
			$indice = $posCierre;
		} else
			break;
	}
	return $xml;
}

function cerrarTags($s, $tags) {
	foreach ( $tags as $tag ) {
		$s = cerrarTag ( $tag, $s );
	}
	return $s;
}

function sclose_tags($s, $tags) {
	foreach ( $tags as $tag ) {
		$s = sclose_tag ( $s, $tag );
	}
	return $s;
}

function sclose_tag($xml, $tag) {
	$xml = preg_replace ( "/<\/$tag>/", "", $xml );
	$xml = preg_replace ( "/<$tag([^(\/>)]*)>/", "<$tag$1/>", $xml );
	return $xml;
}

function check_urls($s) {
	$pos = 0;
	$endpos = 0;
	while ( true ) {
		$url = "";
		if ($endpos >= strlen ( $s )) {
			break;
		}
		$pos = stripos ( $s, "src=\"", $endpos );
		$poss = $pos + 5;
		if ($poss > strlen ( $s )) {
			$poss = strlen ( $s );
		}
		$endpos = stripos ( $s, "\"", $poss );
		if ($endpos === false) {
			$endpos = strlen ( $s );
		}
		if ($pos !== false) {
			$url = substr ( $s, $poss, $endpos - $poss );
			$url = preg_match ( "^(http://|ftp://)(.)*$^", $url ) ? $url : "http://$url";
			//if(get_host($url)=="clip2net.com"&&!count(get_get($url))){$url.="?nocache=1";}
			

			$l = get_url_l ( $url );
			if (! check_l ( $l )) {
				$url = "";
			}
			;
			$s = substr ( $s, 0, $pos + 5 ) . $url . substr ( $s, $endpos );
			$endpos = $pos + 1;
		
		} else {
			break;
		}
	}
	return $s;
}

function innerHTML(&$dom, &$node, $html = false) {
	
	## if html parameter not specified, return the current contents of $node
	if ($html === false) {
		
		$doc = new DOMDocument ();
		//foreach ($node->childNodes as $child)
		$doc->appendChild ( $doc->importNode ( $node, true ) );
		
		return $doc->saveXML ();
	
	} else {
		
		## get rid of all current children
		foreach ( $node->childNodes as $child )
			$node->removeChild ( $child );
		
		## if html is empty, we are done.
		if ($html == '')
			return;
		
		## load up $html as DOM fragment, append it to our now-empty $node
		$f = $dom->createDocumentFragment ();
		$f->appendXML ( $html );
		$node->appendChild ( $f );
	}

}

function my_s($s, $direct = false) {
	if (is_array ( $s )) {
		$res = array ();
		foreach ( $s as $name => $value ) {
			$res [$name] = my_s ( $value );
		}
		return $res;
	} else {
		if (! $direct) {
			$s = ecran ( $s, array (",", ":", "=", "'", "~" ) );
		} else {
			//$s=str_replace("'", "\\'", $s);
			$s = str_replace ( "\\", "\\\\", $s );
			$s = str_replace ( "%27", "\\%27", $s );
		}
		return $s;
	}
}

function print_br($ar, $return = false) {
	//print "$param<ul>".str_replace(")", "</ul></li>", str_replace("(", "<li><ul>", rint_r($ar, true)))."</ul>";
	if (! st_vars::$console) {
		$res = "<pre>" . h2s ( print_r ( $ar, true ) ) . "</pre>";
	} else {
		$res = print_r ( $ar, true );
	}
	if (! $return) {
		print $res;
	} else {
		return $res;
	}
}

function ecran($s, $symbols, $first_time = true) {
	if (is_string ( $symbols )) {
		if ($first_time) {
			$upc = urlencode ( "%" );
			$s = str_replace ( "%", $upc, $s );
		}
		$sym = $symbols;
		$rs = urlencode ( $sym );
		$s = str_replace ( $sym, "$rs", $s );
		return $s;
	} else {
		for($i = 0; $i < count ( $symbols ); $i ++) {
			$s = ecran ( $s, $symbols [$i], $i == 0 ? 1 : 0 );
		}
		return $s;
	}
}

class s2link {
	static $trans = array ('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'jh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'i', 'ь' => '', 'э' => 'je', 'ю' => 'ju', 'я' => 'ja', '?' => '', ' ' => '-', '/' => '' );
	static $allowed = array ('a' => '1', 'b' => '1', 'c' => '1', 'd' => '1', 'e' => '1', 'f' => '1', 'g' => '1', 'h' => '1', 'i' => '1', 'j' => '1', 'k' => '1', 'l' => '1', 'm' => '1', 'n' => '1', 'o' => '1', 'p' => '1', 'q' => '1', 'r' => '1', 's' => '1', 't' => '1', 'u' => '1', 'v' => '1', 'w' => '1', 'x' => '1', 'y' => '1', 'z' => '1', '_' => '1', '1' => '1', '2' => '1', '3' => '1', '4' => '1', '5' => '1', '6' => '1', '7' => '1', '8' => '1', '9' => '1', '0' => '1', '.' => '1', '-' => '1' );
	
	static function translit($s) {
		$s = strtolower_u ( $s );
		for($i = 0; $i < strlen_u ( $s ); $i ++) {
			$chr = substr_u ( $s, $i, 1 );
			$ns .= (self::$allowed [$s [$i]] == 1) ? $chr : self::$trans [$chr];
		}
		$ns = self::cut_end ( $ns );
		return htmlspecialchars ( $ns );
	}
	
	private function replace_spaces($s) {
		str_replace ( "--", "-", $s );
	}
	
	private static function cut_end($s) {
		while ( substr_u ( $s, 0, 1 ) == "-" ) {
			$s = substr_u ( $s, 1 );
		}
		while ( substr_u ( $s, strlen_u ( $s ) - 1, 1 ) == "-" ) {
			$s = substr_u ( $s, 0, strlen_u ( $s ) - 1 );
		}
		return $s;
	}
}
class s2u {
	static $allowed = array ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '_', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.', '/', '?', '#', '%', '&', '-', ':', '=' );
	
	static function translit($s) {
		//$s=strtolower($s);
		for($i = 0; $i < strlen ( $s ); $i ++) {
			$ns .= (array_search ( strtolower ( $s [$i] ), self::$allowed ) !== false) ? $s [$i] : "";
		}
		return htmlspecialchars ( $ns );
	}
}

function h2i($s, $min = null, $max = null) {
	$negative = substr ( $s, 0, 1 ) == "-";
	$res = preg_replace ( "/([^0-9]+)/", "", $s );
	if ($min !== null) {
		$res = max ( $res, $min );
	}
	if ($max !== null) {
		$res = min ( $res, $max );
	}
	$res = $negative ? - $res : $res;
	return $res ? $res : 0;
}

function h2d($s, $min = null, $max = null) {
	$negative = substr ( $s, 0, 1 ) == "-";
	$res = preg_replace ( "/([^0-9\.]+)/", "", $s );
	if ($min !== null) {
		$res = max ( $res, $min );
	}
	if ($max !== null) {
		$res = min ( $res, $max );
	}
	$res = $negative ? - $res : $res;
	return $res ? $res : 0;
}

class to_visible_string {
	static $allowed = array ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '_', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.', '/', '?', '#', '%', '&', '-', ':', '=', '!', '@', '$', '%', '^', '*', '(', ')', '+', '"', '№', ';', ':', '*', '{', '}', '[', ']', '|', '\\', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', '`', '~', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', '<', '>', ' ' );
	
	static function translit($s) {
		for($i = 0; $i < strlen ( $s ); $i ++) {
			//cho array_search(strtolower(1), self::$allowed)!==false;
			$cchar = substr ( $s, $i, 1 );
			$ns .= (array_search ( strtolower ( $cchar ), self::$allowed ) !== false) ? $cchar : "";
		}
		return $ns;
	}
}

class s2file {
	static $allowed = array ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '_', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.', '/', '?', '#', '%', '&', '-', ':', '=', '!', '@', '$', '%', '^', '*', '(', ')', '+', '"', '№', ';', ':', '*', '{', '}', '[', ']', '|', '\\', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', '`', '~', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', '<', '>', ' ' );
	
	static function translit($s) {
		$ns = "";
		for($i = 0; $i < strlen ( $s ); $i ++) {
			$ns .= (array_search ( strtolower ( $s [$i] ), self::$allowed ) !== false) ? $s [$i] : "";
		}
		$ns = preg_replace ( "/([.]+)/", ".", $ns );
		return $ns;
	}
}

function s2file($s) {
	return s2file::translit ( $s );
}

function get_get($req) {
	$GET = array ();
	$_GETex = stripos ( $req, "?", 0 ) ? true : false;
	$adrend = (strpos ( $req, "?", 0 ) === false) ? strlen ( $req ) : strpos ( $req, "?", 0 );
	$parstr = substr ( $req, $adrend + 1, strlen ( $req ) );
	
	$parar = explode ( "&", $parstr );
	for($i = 0; $i < count ( $parar ); $i ++) {
		$ar0 = explode ( "=", $parar [$i] );
		if ($ar0 [0]) {
			$GET [$ar0 [0]] = $ar0 [1] ? $ar0 [1] : false;
		}
	}
	
	//	if(strlen($_SERVER["HTTP_HOST"])>strlen(vars::$host)){
	//		$_GET["u"]=substr($_SERVER["HTTP_HOST"], 0, strlen($_SERVER["HTTP_HOST"])-strlen(vars::$host)-1);
	//	};
	return $GET;
}

function s2vs($s) {
	return to_visible_string::translit ( $s );
}

//function unserialize($ar){
//	if(!is_array($ar)&&vars::$debug){
//		return false;
//	}
//	$i=0;
//	foreach ($ar as $key => $value) {
//		$res[$i]=my_implode(array($key, $value), "=");
//		$i++;
//	}
//	$res=my_implode($res, "$", true);
//	return $res;
//}


function crop($s, $delim, $enddelim = false) {
	if (! $enddelim) {
		$enddelim = $delim;
	}
	$s = substr ( $s, stripos ( $s, $delim ) + 1 );
	$s = substr ( $s, 0, strripos ( $s, $enddelim ) );
	return $s;
}

function mark_out($s, $delim, $enddelim = false) {
	if (! $enddelim) {
		$res = explode ( $delim, $s );
		for($i = 0; $i < count ( $res ); $i ++) {
			if ($i % 2 == 1) {
				$rres [] = $res [$i];
			}
		}
		return $rres;
	} else {
		$res = explode ( $delim, $s );
		$res = split_arrayin ( $res, $enddelim );
		for($i = 1; $i < count ( $res ); $i ++) {
			$rres [$i - 1] = $res [$i] [0];
		}
		return $rres;
	}
}

function strlen_u($s) {
	return mb_strlen ( $s, "UTF8" );
}

function substr_u($s, $start, $length = null) {
	return mb_substr ( $s, $start, $length, "UTF8" );
}

function strtolower_u($s) {
	return mb_strtolower ( $s, "UTF8" );
}

function upper_first_letter($s) {
	$s = strtoupper ( $s [0] ) . substr ( $s, 1 );
	return $s;
}

function ar_replace($ar, $from, $to = false) {
	foreach ( $ar as $key => $value ) {
		$ar [$key] = str_replace ( $from, $to, $value );
		$ar [str_replace ( $from, $to, $key )] = str_replace ( $from, $to, $value );
	}
	return $ar;
}

function rem_empthy_values($ar) {
	foreach ( $ar as $key => $value ) {
		if ($value) {
			if (is_int ( $key )) {
				$res [] = $value;
			} else {
				$res [$key] = $value;
			}
		}
	}
	return $res;
}

function get_extension($s) {
	$ar = explode ( ".", $s );
	if (count ( $ar ) > 1) {
		return end ( $ar );
	} else {
		return false;
	}
}

function date2unixstamp($s) {
	
	$months = array ('Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12 );
	
	$a = explode ( ' ', $s );
	$b = explode ( ':', $a [4] );
	return gmmktime ( $b [0], $b [1], $b [2], $months [$a [2]], $a [1], $a [3] );
}

function my_join($glue, $ar) {
	$ar = Ar::remvalue ( $ar, null, true );
	return join ( $glue, $ar );
}

function cut_spaces($ar) {
	if (is_array ( $ar )) {
		foreach ( $ar as $name => $value ) {
			$res [cut_spaces ( $name )] = cut_spaces ( $value );
		}
		return $res;
	} else {
		// 		$ar=preg_replace("/^([ ]+)/", "", $ar);
		// 		$ar=preg_replace("/([ ]+)$/", "", $ar);
		return trim ( $ar );
	}
}

function utf8_encode_ar($ar) {
	foreach ( $ar as $name => $value ) {
		$ar [$name] = utf8_encode ( $value );
	}
	return $ar;
}

function mb_convert_encoding_ar($ar, $to_encoding, $from_encoding = false) {
	foreach ( $ar as $name => $value ) {
		$ar [$name] = mb_convert_encoding ( $value, $to_encoding, $from_encoding );
	}
	return $ar;
}

function shorten_string($s, $max_symbols = 50, $one_side = false) {
	if (strlen ( $s ) > $max_symbols) {
		if (! $one_side) {
			$end = $max_symbols * 0.3;
			$start = $max_symbols * 0.7;
			return substr ( $s, 0, $start ) . "..." . substr ( $s, strlen ( $s ) - $end, $end );
		} else {
			return substr ( $s, 0, $max_symbols - 3 ) . "...";
		}
	} else {
		return $s;
	}
}

function get_text($html) {
	$search = array ("'<br[^>]*?/>'", "'<script[^>]*?>.*?</script>'si", "'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'", "'&(quot|#34);'i", "'&(amp|#38);'i", "'&(lt|#60);'i", "'&(gt|#62);'i", "'&(nbsp|#160);'i", "'&(iexcl|#161);'i", "'&(cent|#162);'i", "'&(pound|#163);'i", "'&(copy|#169);'i", "'&#(\d+);'e" );
	
	$replace = array ("\n", "", "", "\\1", "\"", "&", "<", ">", " ", chr ( 161 ), chr ( 162 ), chr ( 163 ), chr ( 169 ), "chr(\\1)" );
	
	$text = preg_replace ( $search, $replace, $html );
	return $text;
}

function get_image($html) {
	$xml = parse_xml ( $html );
	$elements = $xml->getElementsByTagName ( "img" );
	if ($elements->length == 0) {
		return false;
	}
	$element = $elements->item ( 0 );
	return $element->getAttribute ( "src" );
}

function parse_info($s) {
	function process_body($s) {
		return s2ar ( $s, "\r\n", ":", true, true );
	
		//return rem_empthy_values ( explode ( "\r\n", $s ) );
	}
	
	preg_match_all ( "/\[([a-z]*)\]/", $s, $headers_ar, PREG_OFFSET_CAPTURE );
	$headers = $headers_ar [1];
	$headers_name = array ();
	$bodies = array ();
	for($i = 0; $i < count ( $headers ); $i ++) {
		$headers_name [] = $headers [$i] [0];
	}
	
	$full_headers = $headers_ar [0];
	foreach ( $full_headers as $i => $full_header ) {
		$header_full_name = $full_header [0];
		$header_offset = $full_header [1];
		$next_header_offset = isset ( $full_headers [$i + 1] ) ? $full_headers [$i + 1] [1] : strlen ( $info );
		$body_begin = $header_offset + strlen ( $header_full_name );
		$body_end = $next_header_offset;
		$body = substr ( $s, $body_begin, $body_end - $body_begin );
		$body = process_body ( $body );
		$bodies [] = $body;
	}
	
	foreach ( $headers_name as $i => $name ) {
		$res [$name] = $bodies [$i];
	}
	return $res;
}

function random_string() {
	$i = rand ( math . pow ( 36, 10 ), math . pow ( 36, 11 ) );
	return base_convert ( $i, 10, 36 );
}

function code($s){
	$s=h2s($s);
	return "<pre>$s</pre>";
}

class translit {
	//static $trans = array ('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'jh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'i', 'ь' => '', 'э' => 'je', 'ю' => 'ju', 'я' => 'ja', '?' => '', ' ' => '-', '/' => '' );
	//static $allowed = array ('a' => '1', 'b' => '1', 'c' => '1', 'd' => '1', 'e' => '1', 'f' => '1', 'g' => '1', 'h' => '1', 'i' => '1', 'j' => '1', 'k' => '1', 'l' => '1', 'm' => '1', 'n' => '1', 'o' => '1', 'p' => '1', 'q' => '1', 'r' => '1', 's' => '1', 't' => '1', 'u' => '1', 'v' => '1', 'w' => '1', 'x' => '1', 'y' => '1', 'z' => '1', '_' => '1', '1' => '1', '2' => '1', '3' => '1', '4' => '1', '5' => '1', '6' => '1', '7' => '1', '8' => '1', '9' => '1', '0' => '1', '.' => '1', '-' => '1' );
	static $trans = array('a'=>'а','b'=>'б','c'=>'к','d'=>'д','e'=>'е','f'=>'ф','g'=>'г','h'=>'х','i'=>'и',
			'j'=>'ж','k'=>'к','l'=>'л','m'=>'м','n'=>'н','o'=>'о','p'=>'п','q'=>'к','r'=>'р','s'=>'с','t'=>'т','u'=>'у',
			'v'=>'в','w'=>'в','x'=>'кс','y'=>'и','z'=>'з');
	static function to_russian($s) {
		$s = strtolower_u ( $s );
		for($i = 0; $i < strlen_u ( $s ); $i ++) {
			$chr = substr_u ( $s, $i, 1 );
			$ns .= (self::$allowed [$s [$i]] == 1) ? $chr : self::$trans [$chr];
		}
		$ns = self::cut_end ( $ns );
		return htmlspecialchars ( $ns );
	}

	private function replace_spaces($s) {
		str_replace ( "--", "-", $s );
	}

	private static function cut_end($s) {
		while ( substr_u ( $s, 0, 1 ) == "-" ) {
			$s = substr_u ( $s, 1 );
		}
		while ( substr_u ( $s, strlen_u ( $s ) - 1, 1 ) == "-" ) {
			$s = substr_u ( $s, 0, strlen_u ( $s ) - 1 );
		}
		return $s;
	}
}
?>