<?php

function login($ilogin, $ipass) {
	require_once "phpscr/shortcuts.php";
	require_once 'sys/vars.php';
	require_once ("settings/mysql_connect.php");
	
	$ilogin = hts ( $ilogin );
	//		$ipass=hts($ipass);
	$logqry = mysql_query ( "select * from user where login='$ilogin'" );
	$uid = my_fst ( $logqry, "id" );
	$e = 0;
	if (mysql_numrows ( $logqry ) == 1) {
		if (mysql_result ( $logqry, 0, "pass" ) == md5 ( $ipass . vars::$secret )) {
			$msg = 1;
			$e = 0;
		} else {
			$msg = ("Неверно введен логин или пароль");
			$e = 1;
		}
	} else {
		$msg = ("Неверно введен логин или пароль");
		$e = 1;
	}
	if ($e == 0) {
		$sid = session_id ();
		$remixsid = User::get_remixsid ( $uid );
		if (! $remixsid) {
			$remixsid = md5 ( $sid . vars::$secret );
		}
		//cho $remixsid;
		sc ( "registered", 1 );
		sc ( "remixsid", $remixsid );
		sc ( "id", $uid );
		my_q ( "update user set remixsid='$remixsid' where id='$uid'" );
		User::re_vars ();
		$_SESSION = array ();
		User::$get_c_id = $uid;
		return true;
	} else {
		$user_id = false;
		$ilogin = false;
	}
	return $msg;
}

function login_fast($uid) {
	$sl = vars::$lt == 1 ? "." . vars::$host : null;
	$sid = session_id ();
	$remixsid = User::get_remixsid ( $uid );
	if (! $remixsid) {
		$remixsid = md5 ( $sid . vars::$secret );
	}
	sc ( "registered", 1 );
	sc ( "remixsid", $remixsid );
	sc ( "id", $uid );
	sc ( "invite", null );
	my_q ( "update user set remixsid='$remixsid' where id='$uid'" );
	User::re_vars ();
	$_SESSION = array ();
	User::$get_c_id = $uid;
}

?>
