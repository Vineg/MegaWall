<?php

function r_get_request($url, $data = array(), &$cookies = "", $referer = '', $needcookie = '') {
	if (! is_array ( $data )) {
		$data = s2ar ( $data, "&", "=" );
	}
	$url = s2link ( $url );
	$urls = $url;
	$url = parse_url ( $url );
	if (is_array ( $cookies )) {
		$cs = ar2s ( $cookies, "; ", "=" );
	} else {
		$cs = $cookies;
	}
	
	// Convert the data array into URL Parameters like a=b&foo=bar etc.
	$data = http_build_query ( array_merge ( $data, s2ar ( $url [query], "&", "=" ) ) );
	// parse the given URL
	
	if ($url ['scheme'] != 'http' && $url ['scheme'] != "https") {
		print ("$urls Error: Only HTTP request are supported !") ;
		exit ();
	}
	
	// extract host and path:
	$host = $url ['host'];
	$path = $url ['path'];
	
	// open a socket connection on port 80 - timeout: 30 sec
	$fp = fsockopen ( $host, 80, $errno, $errstr, 30 );
	if ($data) {
		$data = "?$data";
	}
	if ($fp) {
		// send the request headers:
		fputs ( $fp, "GET $path$data HTTP/1.1\r\n" );
		fputs ( $fp, "Host: $host\r\n" );
		fputs ( $fp, "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:5.0) Gecko/20100101 Firefox/5.0\r\n" );
		
		if ($referer != '')
			fputs ( $fp, "Referer: $referer\r\n" );
		
		fputs ( $fp, "Cookie: " . $cs . "\r\n" );
		fputs ( $fp, "Accept:	text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" );
		fputs ( $fp, "Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3\r\n" );
		fputs ( $fp, "Accept-Charset:	utf-8;q=0.7\r\n" );
		fputs ( $fp, "Connection: close\r\n\r\n" );
		
		$result = '';
		while ( ! feof ( $fp ) ) {
			// receive the results of the request
			$p = fgets ( $fp, 128 );
			$result .= $p;
		
		}
	} else {
		return array ('status' => 'err', 'error' => "$errstr ($errno)" );
	}
	
	// close the socket connection:
	fclose ( $fp );
	
	// split the result header from the content
	$result = explode ( "\r\n\r\n", $result, 2 );
	
	$header = isset ( $result [0] ) ? $result [0] : '';
	$r = explode ( "\r\n", $header );
	$c = 0;
	for($i = 0; $i < count ( $r ); $i ++) {
		if (stripos ( $r [$i], ":" )) {
			$ar0 = explode ( ":", $r [$i], 2 );
			$ar0 [1] = cut_spaces ( $ar0 [1] );
			$ar0 [0] = cut_spaces ( $ar0 [0] );
			$res [$ar0 [0]] = $ar0 [1] ? $ar0 [1] : false;
		} else {
			$r [$i] = cut_spaces ( $r [$i] );
			$res [$c] = $r [$i];
			$c ++;
		}
	}
	$header = $res;
	if ($header ["Set-Cookie"]) {
		$nc = s2ar ( $header ["Set-Cookie"], ";", "=" );
		$cookies = array_merge ( $cookies, $nc );
		if ($nc [$needcookie]) {
			return true;
		}
	}
	$content = isset ( $result [1] ) ? $result [1] : '';
	$content = substr ( $content, 9, strlen ( $content ) - 18 );
	// cho h2s($content);
	
	if ($header [Location]) {
		if ($header [Location] == $urls) {
			exit ();
		}
		return get_request ( s2link ( $header [Location], $urls ), "", $cookies );
	}
	// return as structured array:
	return array ('status' => 'ok', 'header' => $header, 'content' => $content );
}

function r_post_request($url, $data = "", &$cookies = "", $referer = '') {
	// if(!is_array($data)){
	// $data=s2ar($data, "&", "=");
	// }
	$url = s2link ( $url );
	$urls = $url;
	$url = parse_url ( $url );
	if (is_array ( $cookies )) {
		$cs = ar2s ( $cookies, "; ", "=" );
	} else {
		$cs = $cookies;
	}
	
	// Convert the data array into URL Parameters like a=b&foo=bar etc.
	if (is_array ( $data )) {
		$data = http_build_query ( $data );
	}
	$cs = ar2s ( $cookies, "; ", "=" );
	
	// Convert the data array into URL Parameters like a=b&foo=bar etc.
	
	if ($url ['scheme'] != 'http' && $url ['scheme'] != "https") {
		print ("$urls Error: Only HTTP request are supported !") ;
		exit ();
	}
	
	// extract host and path:
	$host = $url ['host'];
	$path = $url ['path'];
	$query = $url ['query'] ? "?$url[query]" : "";
	
	// open a socket connection on port 80 - timeout: 30 sec
	if ($url ['scheme'] == "http") {
		$fp = fsockopen ( $host, 80, $errno, $errstr, 2 );
	} else if ($url ['scheme'] == "https") {
		$fp = fsockopen ( "ssl://" . $host, 443, $errno, $errstr, 30 );
	}
	
	if ($fp) {
		// send the request headers:
		fputs ( $fp, "POST $path$query HTTP/1.1\r\n" );
		fputs ( $fp, "Host: $host\r\n" );
		fputs ( $fp, "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:5.0) Gecko/20100101 Firefox/5.0\r\n" );
		fputs ( $fp, "Accept:	text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" );
		fputs ( $fp, "Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3\r\n" );
		// fputs($fp, "Accept-Encoding: identity\r\n");
		fputs ( $fp, "Accept-Charset:	utf-8;q=0.7\r\n" );
		if ($referer != '')
			fputs ( $fp, "Referer: $referer\r\n" );
		
		fputs ( $fp, "Content-type: application/x-www-form-urlencoded\r\n" );
		fputs ( $fp, "Content-length: " . strlen ( $data ) . "\r\n" );
		if ($cs) {
			fputs ( $fp, "Cookie: " . $cs . "\r\n" );
		}
		fputs ( $fp, "Connection: close\r\n\r\n" );
		
		fputs ( $fp, $data );
		
		$result = '';
		while ( ! feof ( $fp ) ) {
			// receive the results of the request
			$result .= fgets ( $fp, 128 );
		}
	} else {
		return array ('status' => 'err', 'error' => "$errstr ($errno)" );
	}
	
	// close the socket connection:
	fclose ( $fp );
	
	// split the result header from the content
	$result = explode ( "\r\n\r\n", $result, 2 );
	
	$header = isset ( $result [0] ) ? $result [0] : '';
	$r = explode ( "\n", $header );
	$c = 0;
	for($i = 0; $i < count ( $r ); $i ++) {
		if (stripos ( $r [$i], ":" )) {
			$ar0 = explode ( ":", $r [$i], 2 );
			$ar0 [1] = cut_spaces ( $ar0 [1] );
			$ar0 [0] = cut_spaces ( $ar0 [0] );
			$res [$ar0 [0]] = $ar0 [1] ? $ar0 [1] : false;
		} else {
			$r [$i] = cut_spaces ( $r [$i] );
			$res [$c] = $r [$i];
			$c ++;
		}
	}
	$header = $res;
	
	if ($header ["Set-Cookie"]) {
		$nc = s2ar ( $header ["Set-Cookie"], ";", "=" );
		$cookies = array_merge ( $cookies, $nc );
	}
	$content = isset ( $result [1] ) ? $result [1] : '';
	$content = count ( $contar > 1 ) ? $contar [1] : $content;
	
	// if($header[Location]){
	// if($header[Location]==$urls){print "err";exit;}
	// return get_request($header[Location], "", $cookies);
	// }
	
	// return as structured array:
	return array ('status' => 'ok', 'header' => $header, 'content' => $content );
}

function urlencode_ar($ar) {
	$res = array ();
	foreach ( $ar as $key => $value ) {
		$res [urlencode ( $key )] = urlencode ( $value );
	}
	return $res;
}

function request($url, $options = array()) {
	if ($options [params] && $options [method] == "post") {
		$options [post_data] = $options [params];
	}
	$default_options = array ('method' => 'get', 'post_data' => false, 'return_info' => false, 'return_body' => true, 'cache' => false, 'referer' => '', 'headers' => array (), 'session' => false, 'session_close' => false, 'cookies' => '' );
	// Sets the default options.
	foreach ( $default_options as $opt => $value ) {
		if (! isset ( $options [$opt] ))
			$options [$opt] = $value;
	}
	
	if (is_array ( $options [cookies] )) {
		$options [cookies] = urlencode_ar ( $options [cookies] );
		$options [cookies] = ar2s ( $options [cookies], ";", "=" );
	}
	
	$url_parts = parse_url ( $url );
	$ch = false;
	$info = array (	// Currently only supported by curl.
	'http_code' => 200 );
	$response = '';
	
	$send_header = array_merge ( array ('Accept' => '*/*', 'User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1' ), $options ['headers'] ); // }
	                                                                                                                                                                        // }
	
	if ($options ['post_data'] == true) {
		// There is an option to specify some data to be posted.
		$options ['method'] = 'post';
		if (is_array ( $options ['post_data'] )) {
			// The data is in array format.
			$post_data = array ();
			foreach ( $options ['post_data'] as $key => $value ) {
				$post_data [] = "$key=" . urlencode ( $value );
			}
			// $url_parts['query'] = implode('&', $post_data);
		} else { // Its a string
			         // $url_parts['query'] = $options['post_data'];
		}
	} elseif (isset ( $options ['multipart_data'] )) {
		// There is an option to specify some data to be posted.
		$options ['method'] = 'post';
		$url_parts ['query'] = $options ['multipart_data'];
	}
	
	// /////////////////////////// Curl /////////////////////////////////////
	// If curl is available, use curl to get the data.
	$post_data = false;
	if (function_exists ( "curl_init" ) and (! (isset ( $options ['use'] ) and $options ['use'] == 'fsocketopen'))) {
		// Don't use curl if it is specifically stated to use fsocketopen in the
		// options
		
		if ($options ['post_data'] == true) {
			// There is an option to specify some data to be posted.
			$page = $url;
			$options ['method'] = 'post';
			
			if (is_array ( $options ['post_data'] )) {
				// The data is in array format.
				$post_data = array ();
				foreach ( $options ['post_data'] as $key => $value ) {
					$post_data [] = "$key=" . urlencode ( $value );
				}
				$url_parts ['query'] = implode ( '&', $post_data );
				$post_data = implode ( '&', $post_data );
			} else { // Its a string
				$url_parts ['query'] = $options ['post_data'];
				$post_data = $options ['post_data'];
			}
		} else {
			if (isset ( $options ['method'] ) and $options ['method'] == 'post') {
				$page = $url_parts ['scheme'] . '://' . $url_parts ['host'] . $url_parts ['path'] . "?" . $url_parts ['query'];
			} else {
				$page = $url;
			}
		}
		
		if ($options ['session'] and isset ( $GLOBALS ['_binget_curl_session'] ))
			$ch = $GLOBALS ['_binget_curl_session']; // Session
		else
			$ch = curl_init ( $url_parts ['host'] );
		curl_setopt ( $ch, CURLOPT_URL, $page ) or die ( "Invalid cURL Handle Resouce" );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HEADER, true ); // We need the headers
		curl_setopt ( $ch, CURLOPT_COOKIE, $options [cookies] );
		curl_setopt ( $ch, CURLOPT_NOBODY, ! ($options ['return_body']) );
		$tmpdir = NULL; // This acts as a flag for us to clean up temp files
		if (isset ( $options ['method'] ) and $options ['method'] == 'post' && $options [post_data]) {
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
		}
		
		// Set the headers our spiders sends
		curl_setopt ( $ch, CURLOPT_USERAGENT, $send_header ['User-Agent'] );
		$custom_headers = array ("Accept: " . $send_header ['Accept'] );
		if (isset ( $options ['modified_since'] ))
			array_push ( $custom_headers, "If-Modified-Since: " . gmdate ( 'D, d M Y H:i:s \G\M\T', strtotime ( $options ['modified_since'] ) ) );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $custom_headers );
		if ($options ['referer'])
			curl_setopt ( $ch, CURLOPT_REFERER, $options ['referer'] );
		
		curl_setopt ( $ch, CURLOPT_COOKIEJAR, "/tmp/binget-cookie.txt" ); // If
		                                                                  // ever
		                                                                  // needed...
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt ( $ch, CURLOPT_MAXREDIRS, 5 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_TIMEOUT_MS, 2000 );
		
		$custom_headers = array ();
		unset ( $send_header ['User-Agent'] ); // Already done (above)
		foreach ( $send_header as $name => $value ) {
			if (is_array ( $value )) {
				foreach ( $value as $item ) {
					$custom_headers [] = "$name: $item";
				}
			} else {
				$custom_headers [] = "$name: $value";
			}
		}
		if (isset ( $url_parts ['user'] ) and isset ( $url_parts ['pass'] )) {
			$custom_headers [] = "Authorization: Basic " . base64_encode ( $url_parts ['user'] . ':' . $url_parts ['pass'] );
		}
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $custom_headers );
		
		$response = curl_exec ( $ch );

		if (isset ( $tmpdir )) {
			// rmdirr($tmpdir); //Cleanup any temporary files :TODO:
		}
		
		$info = curl_getinfo ( $ch ); // Some information on the fetch
		
		if ($options ['session'] and ! $options ['session_close'])
			$GLOBALS ['_binget_curl_session'] = $ch; // Dont
				                                         // close
				                                         // the
				                                         // curl
				                                         // session.
				                                         // We
				                                         // may
				                                         // need
				                                         // it
				                                         // later
				                                         // -
				                                         // save
				                                         // it
				                                         // to
				                                         // a
				                                         // global
				                                         // variable
		else
			curl_close ( $ch ); // If the session option is not set, close the
				                    // session.
				                    
		// ////////////////////////////////////////// FSockOpen
				                    // //////////////////////////////
	} else {
		print "install curl";
		exit ();
	}
	
	// Get the headers in an associative array
	$headers = array ();
	// print_r($response);
	
	if ($info ['http_code'] == 404) {
		$body = "";
		$headers ['Status'] = 404;
	} else {
		// Seperate header and content
		$header_text = substr ( $response, 0, $info ['header_size'] );
		$body = substr ( $response, $info ['header_size'] );
		
		foreach ( explode ( "\n", $header_text ) as $line ) {
			$parts = explode ( ": ", $line );
			if (count ( $parts ) == 2) {
				if (isset ( $headers [$parts [0]] )) {
					if (is_array ( $headers [$parts [0]] ))
						$headers [$parts [0]] [] = chop ( $parts [1] );
					else
						$headers [$parts [0]] = array ($headers [$parts [0]], chop ( $parts [1] ) );
				} else {
					$headers [$parts [0]] = chop ( $parts [1] );
				}
			}
		}
	
	}
	
	if ($options ['return_info']) {
		//$effective_uri = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		return array ('headers' => $headers, 'body' => $body, 'info' => $info, 'curl_handle' => $ch);
	} else {
		return $body;
	}
}


function get_user_country_code(){
	$ip = $_SERVER['REMOTE_ADDR'];
	//echo $ip;
	return get_country_code_by_ip($ip);
}

function get_country_code_by_ip($ip){
	
	$charset = 'utf8'; //или cp1251
	mysql_query("SET NAMES $charset");
	mysql_query("SET CHARACTER SET '$charset'");
	mysql_select_db('mydatabase');
	$res = mysql_query("SELECT * FROM `ipgeobase` WHERE ".ip_to_digit($ip)." BETWEEN from_ip AND to_ip");
	if (!$res) {
		die(mysql_error());
	}
	return my_fst($res,"country_code");
}

function ip_to_digit($ip){
	list($a,$b,$c,$d) = explode('.', $ip);
	return pow(256,3)*$a + pow(256,2)*$b + 256*$c + $d;
}