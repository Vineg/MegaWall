<?php
function curpath($deepness = 0) {
	$d = debug_backtrace ();
	$ps = $d [$deepness] ["file"];
	$dir = get_dir ( $ps );
	return $dir;
}

function get_full_path($path, $deepness = 0) {
	if(substr($path, 0, 2)=="./"){
		$ps = substr($path, 2);
		$file = curpath($deepness+1)."/".$ps;
	}else{
		$file = $path;
	}
	return $file;
}

function fr($filename) {
	$file = get_full_path ( $filename );
	$f = fopen ( $file, "r" );
	if (filesize ( $file ) < 1) {
		return false;
	}
	$s = fread ( $f, filesize ( $file ) );
	fclose ( $f );
	return $s;
}

function fp($filename, $s, $append = false) {
	$file = get_full_path ( $filename, 1 );
	
	if (! $append) {
		$f = fopen ( $file, "w+" );
	} else {
		$f = fopen ( $file, "a+" );
	}
	fwrite ( $f, $s );
	fclose ( $f );
}

function load_file($url) {
	$vars = my_fst ( "select vars from flink where url='$url'", "vars" );
	if (! my_qn ( "select file_id from flink where url='$url'" )) {
		my_q ( "insert into flink(url) values('$url')" );
	}
	$id = my_fst ( "select file_id from flink where url='$url'", "file_id" );
	if ($id) {
		return get_file_link ( $id );
	}
	$vars = serialize ( $vars );
	if (time () - $vars [last_upload_try] < 60 * 60 * 24) {
		return false;
	}
	;
	$head = g_h ( $url, "", 15000 );
	if (stripos ( $head [0], "200" ) === false) {
		fail_load_file ( $url );
	}
	require_once 'phpscr/functions.php';
	$ext = get_extension_from_header ( $head );
	$url = $head [final_location];
	if (array_search ( $ext, array ("jpg", "png", "gif", "swf", "jpeg" ) )) {
		$ref = "http://" . get_host ( $url ) . "/";
		$cont = upload ( $url, array (method => get, referer => $ref ) );
		if (strlen ( $cont ) > 30 * pow ( 10, 6 )) {
			return false;
		}
		mysql_query ( "insert into file(ext, rate) values ('$ext', 10)" );
		$id = mysql_insert_id ();
		fp ( st_vars::$ud . "/$id.$ext", $cont );
		mysql_query ( "update flink set file_id='$id' where url='$url'" );
		return st_vars::$ud . "/$id.$ext";
	}
	return false;
}

function get_file_link($id) {
	$ext = my_fst ( "select ext from file where id=$id", "ext" );
	return st_vars::$ud . "/$id.$ext";
}

function fail_load_file($url) {
	$vars = my_fst ( "select vars from flink where url='$url'", "vars" );
	$rate = my_fst ( "select rate from flink where url='$url'", "rate" );
	
	$rate = $rate - 10;
	$vars = serialize ( $vars );
	$vars [last_upload_try] = time ();
	
	my_q ( "update flink set rate='$rate' where url='$url'" );
}

function upload($url, $options = array()) {
	return request ( $url, $options );
}

function exists($s, $curpath = false) {
	if ($curpath === false) {
		$curpath = get_include_path ();
	}
	if (substr ( $s, 0, 2 ) == "./") {
		$cp = substr ( $s, 2 );
		$path = get_include_path () . "/" . $cp;
	} else {
		$path = $s;
	}
	return file_exists ( $path );
}

function print_file($filename, $download = false, $nocatche=false) {
	def_headers ( $filename, $download, $nocatche );
	//$file = fopen ( get_full_path($filename), "r" );
	$file = fopen ( $filename, "r" );
	if ($file) {
		while ( ! feof ( $file ) ) {
			print fgets ( $file, 1024 );
		}
	} else {
		return false;
	}
}

function get_dir($path) {
	if (stripos ( $path, "\\" )) {
		$path = substr ( $path, 0, strrpos ( $path, "\\" ) );
	} else {
		$path = substr ( $path, 0, strrpos ( $path, "/" ) );
	}
	return $path;
}

function extension_to_header($ext){
	$ext2type = array ("png" => "image/png", "jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "css" => "text/css", "js" => "text/javascript","html"=>"text/html","htm"=>"text/html");
	return $ext2type[$ext]?$ext2type[$ext]:"text/html";
}

function def_headers($file, $download = false, $nocatche=false) {
	$filename = Request::get_file($file);
	$ext = get_extension ( $file );
	$ext2type = array ("png" => "image/png", "jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "css" => "text/css", "js" => "text/javascript","html"=>"text/html","htm"=>"text/html" );
	$type = $ext2type [$ext]?$ext2type [$ext]:"text/html";
	
	$mt = filemtime ( $file );
	if (isset ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] )) {
		$cache_mt = $_SERVER ['HTTP_IF_MODIFIED_SINCE'];
		//cho date2unixstamp($cache_mt)."+".$mt;exit;
		if (date2unixstamp ( $cache_mt ) >= $mt) {
			header ( 'HTTP/1.1 304 Not Modified' );
			exit ();
		}
	}
	// Отправляем требуемые заголовки
	header ( $_SERVER ["SERVER_PROTOCOL"] . ' 200 OK' );
	// Тип содержимого. Может быть взят из заголовков полученных от клиента
	// при закачке файла на сервер. Может быть получен при помощи расширения PHP Fileinfo.
	header ( 'Content-Type: ' . $type );
	if($nocatche){header ( "Cache-Control: " . "no-cache" );}else{
		header ( "Cache-Control: " . "max-age=290304000, public" );
	}
	header ( "Pragma: " . "" );
	// Дата последней модификации файла
	header ( 'Last-Modified: ' . gmdate ( 'r', filemtime ( $file ) ) );
	// Отправляем уникальный идентификатор документа,
	// значение которого меняется при его изменении.
	// В нижеприведенном коде вычисление этого заголовка производится так же,
	// как и в программном обеспечении сервера Apache
	header ( 'ETag: ' . '"' . sprintf ( '%x-%x-%x', fileinode ( $file ), filesize ( $file ), $mt ) ) . '"';
	// Размер файла
	header ( 'Content-Length: ' . (filesize ( $file )) );
	if($download){
		header ( 'Content-Disposition: attachment; filename="' . basename ( $filename ) . '";' );
	}
	header ( 'Connection: close' );

	// Имя файла, как он будет сохранен в браузере или в программе закачки.
	// Без этого заголовка будет использоваться базовое имя скрипта PHP.
	// Но этот заголовок не нужен, если вы используете mod_rewrite для
	// перенаправления запросов к серверу на PHP-скрипт
	//header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
	// Отдаем содержимое файла
}
?>