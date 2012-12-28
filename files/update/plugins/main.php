<?php
include_once "phpscr/user.php";
include_once "phpscr/reg.php";
include_once 'phpscr/mysql/functions.php';
$ftree = Request::get_ftree_nocache ();
if(count($ftree)<4){l404();}
$username = $ftree [2];
$upload_secret = $ftree [3];
$uid = User::get_id ( $username );
if ($uid && User::get_upload_secret ( $uid )) {
	$username = $username;
} else {
	$username = false;
}

if (! $username) {
	$uid = User::get_anonimous_id ();
	$username = User::get_login ( $uid );
	$upload_secret = User::get_upload_secret($uid);
}

set_include_path ( "" );
$curdir = get_full_path ( get_dir ( __FILE__ ) );
$plugin_name = Request::get_file ();
$filepath = "$curdir/$plugin_name";
if (file_exists ( $filepath )) {
	$zip = new ZipArchive ();
	if ($zip->open ( $filepath ) === TRUE) {
		$zip->addFromString ( ".mwuserinfo", "userName:$username\r\nuserSecret:$upload_secret" );
		$zip->close ();
	} else {
		print 'failed';
	}
	print_file ( "$curdir/$plugin_name", true );
} else {
	l404 ();
}