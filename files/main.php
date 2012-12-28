<?php
include_once 'phpscr/files/functions.php';
$dr = $_SERVER['DOCUMENT_ROOT'];
set_include_path ( "files" );
$ftree = Request::get_ftree ();
if ($ftree [1] == "update") {
	if(count($ftree)<4){l404();}
	$ftree = array_merge(array_slice($ftree, 0, 2), array_slice($ftree, 4));
	if ($ftree [2] == "plugins") {
		include 'update/plugins/main.php';
		exit ();
	}
}
$file = s2file::translit ( Page::get_file () );
if ($file == "main.php") {
	exit ();
}
$path = join ( "/", $ftree );
$ext = get_extension ( $file );
$file = "$path/$file";
if ($ext == "php") {
	if (file_exists ( $file )) {
		include ($file);
		exit ();
	} else {
		l404 ();
	}
}
// $ext2type = array ("png" => "image/png", "jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "css" => "text/css", "js" => "text/javascript", "html"=>"text/html" );
// $type = $ext2type [$ext];
if ($file && file_exists ( $file )) {
	ini_set ( "max_execution_time", max ( ceil ( filesize ( $file ) / 1024 / 1024 / 5 ), 5000 ) );
	print_file ( $file );
} else {
	l404 ();
}