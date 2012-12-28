<?php
$content = "";
$file = s2file::translit ( Page::get_file () );
$phpfile = array ("linkproj_code.zip", "linkproj_code.php" );

if (in_array ( $file, $phpfile )) {
	include "/phpfile/$file.php";
} else {
	$bro = get_browser ();
	if ($bro->browser == "Chrome" && $file == "megawallus.user.js") {
		loc ( "megawallusl.user.js" );
	}
	//cho $filename == "megawallus.user.js"; exit;
	
	if (! User::get_id ()) {
		def_page ( "Необходимо войти" );
		exit ();
	}
	
	$file = "get/file/$file";
	$uid = User::get_c_id ();
	$replace = array ();
	$sk = User::get_submit_secret ();
	if (! $sk) {
		$sk = md5 ( rand ( 0, 100000000000 ) );
		my_q ( "update user set submit_secret='$sk' where id='$uid'" );
	}
	$newbie = User::get_rate () < st_vars::$rate_no_captcha ? 1 : 0;
	$replace ["megawallus.user.js"] = array (secret_key => $sk, user_id => $uid, host => vars::$host, newbie => $newbie );
	sc ( "update_try", null );
	$replace ["megawallusl.user.js"] = $replace ["megawallus.user.js"];
	$replace ["megawallusdev.user.js"] = $replace ["megawallus.user.js"];
	if (! $content && file_exists ( $file )) {
		$content = file_get_contents ( $file );
	} else {
		l404 ();
	}
	$filemtime = filemtime ( $file );
	$fileinode = fileinode ( $file );
	$gmdate = gmdate ( 'r', $filemtime );
}
$s = replace_php_vars ( $content, $replace [$file] );
if ($_GET [no_load] === null) {
	// Отправляем требуемые заголовки
	header ( $_SERVER ["SERVER_PROTOCOL"] . ' 200 OK' );
	// Тип содержимого. Может быть взят из заголовков полученных от клиента
	// при закачке файла на сервер. Может быть получен при помощи расширения PHP Fileinfo.
	header ( 'Content-Type: ' . 'application/octet-stream' );
	// Дата последней модификации файла
	header ( 'Last-Modified: ' . $gmdate );
	// Отправляем уникальный идентификатор документа,
	// значение которого меняется при его изменении.
	// В нижеприведенном коде вычисление этого заголовка производится так же,
	// как и в программном обеспечении сервера Apache
	header ( 'ETag: ' . sprintf ( '%x-%x-%x', $fileinode, strlen ( $s ), $filemtime ) );
	// Размер файла
	header ( 'Content-Length: ' . (strlen ( $s )) );
	header ( 'Connection: close' );
	// Имя файла, как он будет сохранен в браузере или в программе закачки.
	// Без этого заголовка будет использоваться базовое имя скрипта PHP.
	// Но этот заголовок не нужен, если вы используете mod_rewrite для
	// перенаправления запросов к серверу на PHP-скрипт
	header ( 'Content-Disposition: attachment; filename="' . basename ( $file ) . '";' );

	// Отдаем содержимое файла
}
print replace_php_vars ( $content, $replace [$file] );