<?php
require_once 'sys/vars.php';
require_once 'sys/st_vars.php';
require_once ("phpscr/string/functions.php");
require_once ("phpscr/uri/functions.php");
require_once ("phpscr/classes/Page.php");
require_once ("phpscr/classes/Request.php");
require_once 'phpscr/shortcuts.php';
pre_init ();

init ();

end_prog ();
// endcode();

function end_prog() {
	save_session ();
}

function init() {
	//exit;
	//link_exchange_init();
	global $_vars;
	require_once "phpscr/require_all.php";
	$host = my_s ( Request::get_host () );
	// include 'admin/init_database.php';
	$projq = my_q ( "select * from project where host='$host'" );
	$_vars [project_id] = my_fst ( $projq, "id" );
	if (! $_vars [project_id]) {
		l404 ();
	}
	if (my_n ( $projq ) > 0) {
		$main_type_id = my_fst ( $projq, 'type_id' );
		st_vars::$type_main = $main_type_id;
		$_vars [main_type_id] = $main_type_id;
		$project = my_fst ( $projq, "project" );
		st_vars::$proj = $project ? $project : "megawall";
	} else {
		st_vars::$proj = st_vars::$def_proj;
		// l404();
		// vars::$host = $_SERVER ["HTTP_HOST"];
		// st_vars::$type_main = 1;
		// st_vars::$proj="megawall";
	}
	// cho st_vars::$proj;
	
	// //cho st_vars::$proj;
	
	//session_start ();
	load_session ();
	//exit;
	
	if (! vars::$debug) {
		ini_set ( "display_errors", 0 );
		ini_set ( "error_log", "/home/vineg/logs/log.txt" );
	}
	$h = vars::$host;
	if (vars::$lt == 1) {
		session_set_cookie_params ( 60 * 60 * 24 * 365, '/', ".$h" );
	} else {
		session_set_cookie_params ( 60 * 60 * 24 * 365 );
	}
	ini_set ( 'session.gc_maxlifetime', 60 * 60 * 24 * 365 );
	// endinit
	
	Page::set_template ();
	
	Timer::start_time ();
	// cho Timer::dtime();
	$ht = Request::get_ht ();
	
	$user_id = User::get_id ();
	if ($user_id) {
		$login = User::get_login ( $user_id );
	}
	$req = $_SERVER ["REQUEST_URI"];
	$i = 0;
	
	$file = Page::get_file ();
	$ftree = get_ftree ();
	
	$path = join ( "/", $ftree );
	
	// write GET params
	
	// $_GETT=get_get($req);
	// if(strlen($_SERVER["HTTP_HOST"])>strlen(vars::$host)){
	// $_GET[u]=substr($_SERVER["HTTP_HOST"], 0,
	// strlen($_SERVER["HTTP_HOST"])-strlen(vars::$host)-1);
	// };
	// end
	// cho Timer::dtime();
	require_once 'blog_section/get_handle.php';
	
	if ($ftree [0] == false) {
		if ($file == "debug") {
			sc ( "debug", 1 );
			print "debug on";
			exit ();
		} elseif ($file == "nodebug") {
			sc ( "debug", 0 );
			print "debug off";
			exit ();
		}
	}
	// cho Timer::dtime();
	
	if ($file == "redirect") {
		if ($file == "redirect") {
			// Header( "HTTP/1.1 301 Moved Permanently" );
			$loc = $_GET [loc];
			if (is_numeric( $loc )) {
				$loc = my_fst ( "select value from var where name=$loc", "value" );
			}
			// $loc=str_replace("NNNN", "", $loc);
			// Header( "Location: $loc" );
			// exit;
			loc ( $loc, true );
		}
	}
	
	if ($ftree [0] == "files") {
		include 'files/main.php';
	} elseif (in_array ( $file, st_vars::$ya_files )) {
		exit ();
	} elseif (st_vars::$proj == "linkproj") {
		include 'proj/seonuke.php';
	} else if (st_vars::$proj == "megawall") {
		include 'proj/blog.php';
	} else if (Request::get_subdomen () == "www") {
		loc ( Request::get_ht () . "/" );
	} else {
		// $tl=new types_list();
		// $tl->selection=true;
		// $tl->table="theme";
		// $section_block = $tl->get_types_list();
		$proj = st_vars::$proj;
		include "proj/$proj.php";
	}
}
function debug_init() {
	// mb_internal_encoding('UTF-8');
	@ini_set ( 'zend_monitor.enable', 0 );
	if (@function_exists ( 'output_cache_disable' )) {
		@output_cache_disable ();
	}
	if (isset ( $_GET ['debugger_connect'] ) && $_GET ['debugger_connect'] == 1) {
		if (function_exists ( 'debugger_connect' )) {
			debugger_connect ();
			exit ();
		} else {
			echo "No connector is installed.";
		}
	}
	// phpinfo();exit;
	if (vars::$debug) {
		// $_POST["newsite"]="http://megawall.ru/";
		// $_POST["s0"]="1";
		// User::$get_c_id=2;
	}
	ini_set ( "display_errors", 1 );
	ini_set ( "error_reporting", E_ALL ^ E_NOTICE ^ E_DEPRECATED );
	ini_set ( "max_execution_time", "60" );
	ini_set ( "memory_limit", "100M" );
}
function locale_init() {
	date_default_timezone_set ( "Europe/Moscow" );
}
function pre_init() {
	debug_init ();
	locale_init ();
	$ftree = Page::get_ftree ();
	$file = Request::get_file ();
	if ($ftree [0] == "files") {
		include 'files/main.php';
		exit ();
	} elseif ($ftree [0] == "loc") {
		$id = h2i ( $file );
		$link = my_fst ( "select * from link where id=$id", "uri" );
		loc ( $link );
		exit ();
	} elseif (! $ftree [0] && in_array ( $file, st_vars::$empthy_pages )) {
		exit ();
	}
	
	// link_exchange_init ();
	
	// init
	
	$host = my_s ( Request::get_host () );
	
	if (preg_match ( "#(.*)void.megawall.ru#", $host )) {
		$host = "megawall.ru";
	}
	
	if ($host == "kulok.ru") {
		echo 1;
		exit ();
	}
	// phpinfo(); exit;
}
function link_exchange_init() {
	if (! defined ( '_SAPE_USER' )) {
		define ( '_SAPE_USER', 'f90b71e880c245fe3afba2974de89e65' );
	}
	require_once ($_SERVER ['DOCUMENT_ROOT'] . '/links_exchange/' . _SAPE_USER . '/sape.php');
	global $sape;
	global $mwclient;
	global $linkfeed;
	$sape = new SAPE_client ( array ("multi_site" => true, "charset" => "UTF8", "request_uri" => $_SERVER ["REQUEST_URI"] ) );
	
	define ( 'LINKFEED_USER', 'b24eef9674577d959c087236c01ad9ff9b2d9e83' );
	
	require_once ($_SERVER ['DOCUMENT_ROOT'] . '/links_exchange/' . LINKFEED_USER . '/linkfeed.php');
	
	$linkfeed = new LinkfeedClient ( array ("request_uri" => $_SERVER ["REQUEST_URI"] ) );
	
	if (! defined ( '_MW_USER' )) {
		define ( '_MW_USER', 'Vinegbdf254ce2b7fcdb6bdc4386fe8c6587c' );
	}
	require_once ($_SERVER ['DOCUMENT_ROOT'] . '/links_exchange/' . _MW_USER . '/megawall.php');
	$mwclient = new megwall_client ();
}
?>