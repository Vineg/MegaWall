<?php
function hts($str) {
	return htmlspecialchars ( $str, ENT_QUOTES );
}

/**
 * alias for htmlspecialchars
 */
function h2s($str) {
	return htmlspecialchars ( $str );
}

function h2secho($s){
	print(h2s($s));
}

function h2sprint_r($ar){
	print h2s(print_r($ar,true));
}

function h2vs($str) {
	return to_visible_string::translit ( $str );
}
//function s2hs($str){
//	$str=str_replace("{", "{", $str);
//	$str=str_replace("}", "}", $str);
//	return hts($str);
//}
function my_q($query) {
	require_once "sys/vars.php";
	require_once "settings/mysql_connect.php";
	$query = my_s ( $query, true );
	$query = rawurldecode ( $query );
	global $mylink;
	if (vars::$debug == 1) {
		$res = mysql_query ( $query, $mylink ) or die ( myqerr ( $query ) );
	} else {
		$res = mysql_query ( $query, $mylink ) or die ( l404 () );
	}
	return $res;
}
function my_r($result, $row, $field) {
	date_default_timezone_set ( "Europe/Moscow" );
	if (my_n ( $result ) > $row) {
		return mysql_result ( $result, $row, $field );
	} else {
		if (vars::$debug == 1) {
			print_br ( debug_backtrace () );
			return false;
		} else {
			return false;
		}
	}
}

function my_fst($query, $field) {
	require_once "sys/vars.php";
	if (is_string ( $query )) {
		$q = my_q ( $query );
	} else {
		$q = $query;
	}
	
	if (my_n ( $q ) >= 1) {
		return my_r ( $q, 0, $field );
	} else {
		return false;
	}
}

function my_n($result) {
	if (! is_resource ( $result ) && vars::$debug) {
		$d = debug_backtrace ();
		print_r ( $d );
	}
	return mysql_num_rows ( $result );
}
function my_qn($query) {
	return my_n ( my_q ( $query ) );
}
function myqerr($query) {
	print $query;
	global $mylink;
	print_br ( debug_backtrace () );
	print mysql_error ( $mylink );
}

function obj($s) {
	if (substr ( $s, 0, 1 ) == "-") {
		$neg = true;
	}
	$ni = preg_replace ( "/([^0-9]*)/", "", $s );
	if ($neg) {
		$res = "-$ni";
	} else {
		$res = $ni;
	}
	return $ni ? $res : 0;
}

function h2b($s) {
	return $s == ("1" || "true" || "on" || "selected" || "checked") ? true : false;
}

function loc($s, $script = false) {
	//rint_r(debug_backtrace());
	// 	exit;
	//if(!vars::$debug){
	if (vars::$no_redirect) {
		return;
	}
	if ($script) {
		print <<<EOQ
			<script>
				window.location='$s';
			</script>
EOQ;
	} else {
		header ( "Location:$s" );
	}
	//}else{
	//cho $s;
	//}
	//cho debug_backtrace();
//	ex ();
}

function lloc($s) {
	if (get_level_host ( get_host ( $s, true ), vars::$host_level ) == st_vars::$host) {
		loc ( $s );
	} else {
		loc ( "/" );
	}
}

function loc_back() {
	$s = $_SERVER ["HTTP_REFERER"];
	if (vars::$host == get_level_host ( get_host ( $s, true ), vars::$host_level )) {
		if (get_url_path ( $s ) != $_SERVER ["REQUEST_URI"]) {
			loc ( "$s" );
		}
	}
	loc ( "/" );
}

function b2hcb($b) {
	if(is_array($b)){
		foreach ($b as $key=>$value){
			$b[$key] = b2hcb($value);
		}
		return $b;
	}else{
		return $b ? "checked=true" : "";
	}
}

function l404() {
	if (vars::$debug) {
		header ( "HTTP/1.1 404 Not Found" );
		$uri = Request::get_uri ();
		print "$uri it is actually 404 page ;)";
		print_br ( debug_backtrace () );
		print ("<br />") ;
		print_r ( $_POST );
		print_r ( $_SESSION );
		exit ();
	} else {
		header ( "HTTP/1.1 404 Not Found" );
		$page = Page::get_404 ();
// 		if ($page) {
// 			process_page ( $page );
// 		} else {
			print Page::get_404 ( true );
//		}
		exit ();
	}
}

function pid2id($pid) {
	return base_convert ( $pid, 36, 10 );
}

function id2pid($id) {
	return base_convert ( $id, 10, 36 );
}

function msg($s, $title = "Другое", $stylenscripts = false) {
	def_page ( $s, $title, $stylenscripts );
}

function sc($param, $value, $time = false) {
	if (! $time) {
		$time = time () + 60 * 60 * 24 * 365;
	} else {
		$time = time () + $time;
	}
	//$linkprojsl=vars::$seonuke_host;
	$sl = vars::$lt == 1 ? get_hostname(vars::$host) : null;
	setcookie ( $param, $value, $time, "/", $sl );

		//setcookie($param,$value,$time, "/", $linkprojsl);
}

function ex() {
	save_session ();
	exit ();
}

function url_encode($s){
	return str_replace ( "+", "%20", urlencode ( $s ) );
}

function s2u($s) {
	return url_encode($s);
}


function ar2u($ar) {
	return http_build_query ( $ar );
}
function u2s($s, $location = false) {
	//return str_replace("+", "%20", urlencode($s));
	return rawurldecode ( $s );
}
;

function s2link($s, $location = false) {
	if (! $location) {
		$location = Request::get_ht () . "/";
	}
	$folder = get_url_folder ( $location );
	$host = get_host ( $location );
	if (substr ( $s, 0, 1 ) == "/") {
		return "http://$host$s";
	} else if (stripos ( $s, ":" ) === false) {
		return $folder . $s;
	} else {
		return $s;
	}
}
?>
