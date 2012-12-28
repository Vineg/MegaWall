<?php
foreach(glob("phpscr/post/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/string/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/user/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/uri/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/page_elements/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/files/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/types/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/net/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/classes/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/other/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/dom/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/mysql/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/lang/*.php") as $class_filename) {
	require_once($class_filename);
}
foreach(glob("phpscr/debug/*.php") as $class_filename) {
	require_once($class_filename);
}

foreach(glob("phpscr/ext_api/*.php") as $class_filename) {
	require_once($class_filename);
}
/*
include 'phpscr/classes/page.php';
include 'phpscr/dom/functions.php';
include 'phpscr/files/functions.php';
include 'phpscr/other/functions.php';
include 'phpscr/page_elements/functions.php';
include 'phpscr/post/functions.php';
include 'phpscr/purifier/HTMLPurifier.php';
include 'phpscr/string/functions.php';
include 'phpscr/types/functions.php';
include 'phpscr/url/functions.php';
*/
//include 'phpscr/user/';
?>
