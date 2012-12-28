<?php
function get_host($uri, $host_exists = false) {
	$host = parse_url ( $uri, PHP_URL_HOST );
	if ($host) {
		return $host;
	}
 else if ($host_exists) {
		$begin = stripos ( $uri, "://" ) === false ? 0 : stripos ( $uri, "://" ) + 3;
		$end = stripos ( $uri, "/", $begin ) === false ? strlen ( $uri ) : stripos ( $uri, "/", $begin );
		return substr ( $uri, $begin, $end - $begin );
	} else {
		return false;
	}
}

function g_h($url, $ref = false, $timeout = 1500) {
	if ($ref === false) {
		$ref = Request::get_ht () . "/";
	}
	$url = s2u::translit ( $url );
	if (! is_url ( $url )) {
		return false;
	}
	
	$ch = curl_init ();
	
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_HEADER, true );
	curl_setopt ( $ch, CURLOPT_NOBODY, true );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_REFERER, $ref );
	curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:5.0) Gecko/20100101 Firefox/5.0" );
	curl_setopt ( $ch, CURLOPT_TIMEOUT_MS, $timeout );
	$r = curl_exec ( $ch );
	
	$r = explode ( "\n", $r );
	$c = 0;
	for($i = 0; $i < count ( $r ); $i ++) {
		if (stripos ( $r [$i], ":" )) {
			$ar0 = explode ( ":", $r [$i], 2 );
			$res [$ar0 [0]] = $ar0 [1] ? $ar0 [1] : false;
		} else {
			$res [$c] = $r [$i];
			$c ++;
		}
	}
	//rint_r($res);
	if ($res [Location]) {
		$res = g_h ( "$res[Location]", $ref, $timeout );
	}
	
	$res [final_location] = $res [final_location] ? $res [final_location] : $url;
	return $res;
}

function is_url($url) {
	
	// Используем функцию parse_url для разбиения URL на части:
	

	$up = parse_url ( $url );
	
	// Если одна из частей или вся строка целиком не существуют, то - ошибка.
	

	if (! $up || ! $up ['scheme'] || ! $up ['host'] || ! $up ['path']) {
		return false;
	}
	
	// Если первая часть строки «scheme» является чем-то средним между // http(S) и ftp: ошибка
	

	if (! (($up ['scheme'] == 'http') || ($up ['scheme'] == 'https') || ($up ['scheme'] == 'ftp'))) {
		return false;
	}
	
	// Проверка пройдена успешно, адрес можно считать правильным.
	

	return true;
}

function url_fail($url) {
	$url = my_s ( $url );
	$rate = my_fst ( "select rate from flink where url='$url'", "rate" );
	if ($rate === false) {
		create_url_row ( $url );
		$rate = my_fst ( "select rate from flink where url='$url'", "rate" );
	}
	if ($rate < - 1000) {
		return "";
	}
	if ($rate <= 0) {
		$lurl = my_fst ( "select * from flink where url='$url'", "file_id" );
		if ($lurl) {
			$ext = my_fst ( "select * from file where id='$lurl'", "ext" );
			return Request::get_ht () . st_vars::$ud . "/$lurl.$ext";
		} else {
			$nf = load_file ( $url );
			if ($nf) {
				$nf = Request::get_ht () . $nf;
				return $nf;
			} else {
				return false;
			}
		}
	} else {
		$rate --;
		my_q ( "update flink set rate='$rate' where url='$url'" );
		return false;
	}
}

function url_ok($url) {
	$rate = my_fst ( "select rate from flink where url='$url'", "rate" );
	if ($rate === false) {
		create_url_row ( $url );
		$rate = my_fst ( "select rate from flink where url='$url'", "rate" );
	}
	$rate ++;
	my_q ( "update flink set rate='$rate' where url='$url'" );
}

function create_url_row($url) {
	$url = my_s ( $url );
	my_q ( "insert into flink(url, vars, file_id, rate) values ('$url', '', 0, 0)" );
}

function src_safe($s) {
	if (stripos ( $s, "src" ) !== false) {
		$pos = 0;
		$endpos = 0;
		while ( true ) {
			$url = "";
			$pos = stripos ( $s, "src=\"", $endpos );
			$poss = $pos + 5;
			$endpos = stripos ( $s, "\"", $poss );
			if ($pos !== false) {
				$url = substr ( $s, $poss, $endpos - $poss );
				$res = min ( $res, $this->url_safe ( $url ) );
			} else {
				break;
			}
		}
		return true;
	} else {
		return true;
	}
}

function url_safe($url) {
	require_once 'phpscr/shortcuts.php';
	require_once 'settings/mysql_connect.php';
	$host = get_host ( $url );
	$host = h2s ( $host );
	$res = my_fst ( "select issafe from safe_site where host='$host'", "issafe" );
	return $res;
}

function get_addr($url) {
	$addrar = explode ( "?", $url );
	return $addrar [0];
}

function get_lhost($url) {
	
	$host = get_host ( $url );
	$hostar = explode ( ".", $host );
	$host = join ( ".", array_slice ( $hostar, count ( $hostar ) - 2 ) );
	
	return $host;
}

function check_l($l) {
	//	$ls="|ru|com|ua|net|biz|рф|gov|org|localhost|am|info|";
	//	if(stripos($ls, "|$l|")!==false){return true;}else{return false;}
	return true;
}

function get_url_l($url) {
	$host = get_host ( $url );
	$lppos = strripos ( $host, "." );
	if ($lppos === false) {
		$lppos = - 1;
	}
	return substr ( $host, $lppos + 1 );
}

function checkh($url, $params = array()) {
	require_once 'sys/vars.php';
	require_once 'phpscr/functions.php';
	
	if (st_vars::$standalone) {
		return true;
	}
	if (get_host ( $url ) == vars::$host) {
		return true;
	}
	
	$ht = Request::get_ht () . "";
	if (substr ( $url, 0, 1 ) == "/") {
		$url = "$ht$url";
	}
	if (substr ( $url, 0, 7 ) != "http://") {
		$url = "http://$url";
	}
	if (url_safe ( $url )) {
		return true;
	}
	$sa = g_h ( $url );
	//cho $url;
	//rint_r($sa);
	if ($sa ["www-authenticate"] !== null && $params [posted] && $params [post_id]) {
		$author_id = Post::get_author_id ( $params [post_id] );
		$fine = st_vars::rate_authenticate_fine;
		my_q ( "update user set rate=rate+$fine where id=$author_id" );
		return false;
	}
	if ($sa ["www-authenticate"] !== null || ! $sa || stripos ( $sa [0], "200" ) === false || (st_vars::$ug_users && User::get_rate () < st_vars::$rate_safe_src)) {
		$nu = url_fail ( $url );
		if (! $nu && $params [posted]) {
			return $url;
		} else {
			return $nu;
		}
	} else if (stripos ( $sa [0], "200" ) !== false) {
		url_ok ( $url );
		return true;
	}
}

function get_ftree($url = false) {
	return Request::get_ftree ( $url );
}

function get_url_path($url = false) {
	if (! $url) {
		$url = $_SERVER ["REQUEST_URI"];
	}
	if (preg_match ( '/^([^\/]*):\/\//', $url )) {
		$req = substr ( $url, stripos ( $url, "://" ) + 3 );
		$req = substr ( $req, stripos ( $req, "/" ) );
	} else if (substr ( $url, 0, 1 ) == "/") {
		$req = $url;
	} else {
		return false;
	}
	$adrend = (strpos ( $req, "?", 0 ) === false) ? strlen ( $req ) : strpos ( $req, "?", 0 );
	return substr ( $req, 0, $adrend );

		// 		$url=parse_url($url);
// 		return $url[path];


}

function get_file($path = false) {
	return Page::get_file ( $path );
}

function get_url_folder($url) {
	
	//rint_r(get_ftree($url));
	return "http://" . get_host ( $url ) . "/" . join ( "/", get_ftree ( $url ) ) . "/";
}

function index_site($host) {
	Host::index ( $host );
}

function get_host_level($host) {
	return substr_count ( "$host", "." );
}

function get_level_host($host, $level) {
	$hostar = explode ( ".", $host );
	return join ( ".", array_splice ( $hostar, count ( $hostar ) - $level, $level ) );
}

function getTCY($url) {
	//считываем XML-файл с данными
	$xml = file_get_contents ( 'http://bar-navig.yandex.ru/u?ver=2&show=32&url=' . $url );
	
	//если XML файл прочитан, то возвращаем значение параметра value, иначе возвращаем false - ошибка
	return $xml ? ( int ) substr ( strstr ( $xml, 'value="' ), 7 ) : false;
}

function get_host_id($host) {
	return newhost($host);
}

function update_host($id) {
	Host::update ( $id );
}

function newurl($uri, $lvl=4, $indexing=false) {
	$host_id = get_host_id ( get_host ( $uri ) );
	if (! $host_id) {
		return false;
	}
	$uris = my_s ( $uri );
	$urires = my_q ( "select * from link where uri='$uris'" );
	//$hostres=my_q("select * from host where id=$host_id");
	if (! my_n ( $urires )) {
		$id=my_in ( "link:uri=$uris,level=$lvl,host_id=$host_id,indexed=1" );
	} else {
		$id=my_fst($urires, "id");
		if($indexing){
			$indexed=my_fst($urires, "indexed");
			if(!$indexed){
				my_up ( "link:level=$lvl,indexed=1:uri='$uris'" );
			}
		}
		recount_link_weight ( $id );
	}
//	if ($has_code == - 1) {
//		$uri_id = get_uri_id ( $uri );
//	}
	if($id==false){
		debug_end();
	}
	return $id;
}

function recount_link_weight($uri_id) {
	$uri=Link::get_uri($uri_id);
	$uris = my_s ( $uri );
	$uriq = my_q ( "select * from link where id=$uri_id" );
	$YAC = my_fst ( $uriq, "YAC" );
	$level = my_fst ( $uriq, "level" );
	$olinks = max(my_fst ( $uriq, "out_links" ),0);
	$l2w = array (1 => 1, 2 => 0.45, 3 => 0.30, 4 => 0.20 );
	$weight = $YAC * $l2w [$level] / pow ( 1 + $olinks / 5, 1.4 );
	if($olinks<0&&$weight>0){
		Bot::add_task(BOT_RECOUNT_OLINKS, "$uri_id");
	}
	if($weight>0&&Link::get_weight($uri_id)==0){
		$host=get_host($uri);
		//my_up ( "host:max_out_links_count='max_out_links_count+1':host='$host'" );
	}else if($weight==0&&Link::get_weight($uri_id)>0){
		$host=get_host($uri);
		//my_up ( "host:max_out_links_count='max_out_links_count-1':host='$host'" );
	}
	my_up ( "link:weight=$weight:uri='$uris'" );
}

function update_yandex_index($host_id, $count = 100, $offset = 0) {
	$urlsperreq = 100;
	
	$urls = get_yandex_index ( $host_id, $count, $offset );
	$urls = rem_empthy_values ( $urls );
	$urlss = my_s ( $urls );
	$t = 0;
	for($i = 0; $i < count ( $urls ); $i ++) {
		$id=newurl($urls[$i], 4);
		my_up ( "link:YAC=1:id='$id'" );
		recount_link_weight($id);
	}
	if ($t) {
		return true;
	} else {
		return false;
	}
}

function get_yandex_index($host_id, $count = 100, $offset = 0) {
	$urlsperreq = st_vars::$ya_urls_per_req;
	$first = true;
	$groups = array ();
	if ($count) {
		for($i = intval ( $offset / $urlsperreq ) * $urlsperreq; $i < $offset + $count; $i += $urlsperreq) {
			$group_n = $i / $urlsperreq;
			$ar = get_yandex_index_100 ( $host_id, $group_n );
			if (! $ar) {
				break;
			}
			$groups [] = $ar;
		}
	} else {
		$ar = true;
		while ( true && count ( $groups ) < st_vars::$max_ya_groups_per_request ) {
			$group_n = $i / $urlsperreq;
			$ar = get_yandex_index_100 ( $host_id, $group_n );
			if (! $ar) {
				break;
			}
			$groups [] = $ar;
			$i += $urlsperreq;
		}
	}
	if (! $groups [0]) {
		return array();
	}
	$groups [0] = array_slice ( $groups [0], $offset % 100 );
	$groups [count ( $groups ) - 1] = array_slice ( $groups [count ( $groups ) - 1], 0, (($count + (count ( $groups ) > 1 ? $offset : 0)) - 1) % 100 + 1 );
	return Ar::ar2ar ( $groups );
}

function get_yandex_index_100($host_id, $group_n = 0) {
	$host = Host::get_host ( $host_id );
	$urlsperreq = st_vars::$ya_urls_per_req;
	$page = $group_n ++;
	$query = "host:$host";
	$req = <<<EOQ
<?xml version="1.0" encoding="UTF-8"?> 	
	<request>
		<page>$page</page>
		<query>$query</query>
		<groupings>
			<groupby attr=" " mode="flat" groups-on-page="$urlsperreq"  docs-in-group="1" />	
		</groupings> 	
	</request>
EOQ;
	$ya_user = st_vars::$ya_user;
	$ya_key = st_vars::$ya_key;
	
	$rq = request ( "http://xmlsearch.yandex.ru/xmlsearch?user=$ya_user&key=$ya_key", array ('post_data' => $req, 'method' => 'post' ) );
	$cont = $rq;
	$max = mark_out ( $cont, "<found priority=\"all\">", "</found>" );
	$max = h2i ( $max [0] );
	my_up ( "host:ya_num=$max:id=$host_id" );
	$doc = new DOMDocument ();
	$doc->loadXML ( $cont );
	$urlsd = $doc->getElementsByTagName ( "url" );
	for($i = 0; $i < $urlsd->length; $i ++) {
		$urls [] = cut_spaces ( $urlsd->item ( $i )->nodeValue );
	}
	return count ( $urls ) ? $urls : false;
}

function get_uri_id($uri) {
	return Link::get_id ( $uri );
}

function ya_index_next($host_id) {
	return Host::ya_index_next ( $host_id );
}

function get_hostname($host){
	return preg_replace('#:(.*)$#','',$host);
}

function get_charset($uri){
	$headers = get_headers($uri,true);
	$ct=$headers["Content-Type"];
	$cp = explode("=",$ct);
	$charset=end($cp);
	return $charset;
}
?>
