<?php
function reg($login, $ipass, $mail, $params = array()) {
	require_once 'phpscr/login.php';
	if ($params [auto]) {
		$login = preg_replace ( "/[_]+/", "_", $login );
	}
	if (preg_match ( "/__[0-9]+$/", $login )) {
		return "Запрещены логины, кончающиеся на __число";
	}
	if (! $params [auto] && preg_match ( "/^__/", $login )) {
		return "Запрещены логины, начинающиеся на __";
	}
	if (my_qn ( "select * from user where login='$login'" ) || my_qn ( "select * from shortcut where shortcut='$login'" )) {
		if (! $params [auto]) {
			return "Пользователь с таким ником уже существует.";
		} else {
			$llog = my_fst ( "SELECT login  FROM user WHERE login REGEXP '^$login" . "__" . "([0-9]+)$' order by length(login) desc, login desc", "login" );
			//cho $llog;
			$n = substr ( $llog, strlen ( $login ) + 2 );
			$n ++;
			$n = max ( $n, 1 );
			$login = $login . "__" . $n;
		}
	}
	$invite = $_GET [inv] ? $_GET [inv] : $_COOKIE [invite];
	$invite_user_id = User::get_invite_user_id ( $invite );
	$params [invite_user_id] = $invite_user_id;
	

	$pass = md5 ( $ipass . vars::$secret );

	complete_reg ( $login, $pass, $mail, $params );
	if ($invite) {
		sc ( "invite", null );
		my_q ( "delete from invite where invite='$invite'" );
	}
	login ( $login, $ipass );
	$_SESSION ["captcha"] = null;
	return true;
}

function complete_reg($login, $pass, $mail, $params = array()) {
	$regdate = time ();
	$photo = s2u::translit ( $params [photo] );
	$def_page = my_s ( st_vars::$def_user_page );
	$invite_user_id = $params[invite_user_id];
	if (parse_url ( $photo, PHP_URL_SCHEME ) == "http") {
		$photo = my_s ( $photo );
	} else {
		$photo = false;
	}
	$photo_rec = s2u::translit ( $params [photo_rec] );
	if (parse_url ( $photo, PHP_URL_SCHEME ) == "http") {
		$photo_rec = my_s ( $photo_rec );
	} else {
		$photo_rec = false;
	}
	if ($invite_user_id) {
		$rate = st_vars::$rate_invited_user;
		$parent_id = $invite_user_id;
	} else {
		$rate = st_vars::$rate_new_user;
	}
	$login = my_s($login);
	$pass = my_s($pass);
	$mail = my_s($mail);
	$parent_id = h2i ( $parent_id );
	$id = my_q ( "insert into user(login, mail, pass, rate, date, user_page, parent_id, photo, photo_rec) values ('$login','$mail','$pass','$rate','$regdate', '$def_page', '$parent_id', '$photo', '$photo_rec')" );
	return $id;
}

function reg_vk_user($array) {
	$en_first_name = upper_first_letter ( s2link::translit ( $array [first_name] ) );
	$en_last_name = upper_first_letter ( s2link::translit ( $array [last_name] ) );
	//$array = mb_convert_encoding_ar ( $array, "utf8", "windows-1251" );//??
	$array [vk_uid] = h2i ( $array [uid] );
	$pass = md5 ( $array [vk_uid] . vars::$secret );
	$log = $en_first_name . "_" . $en_last_name;
	if (strlen ( $log ) > st_vars::$maxlog) {
		$log = substr ( $log, 0, st_vars::$maxlog - 1 );
	}
	reg ( $log, $pass, false, array (auto => true, photo => $array [photo], photo_rec => $array [photo_rec] ) );
	$array [uid] = User::get_c_id ();
	$array = my_s ( $array );
	$user_id = $array [uid];
	//cho "vk_user:vk_id,first_name,last_name,user_id,photo,photo_rec:$array[vk_uid],$array[first_name],$array[last_name],$array[uid],$array[photo],$array[photo_rec]";
	if (! my_qn ( "select * from vk_user where vk_id=$array[vk_uid]" )) {
		my_in ( "vk_user:vk_id,first_name,last_name,user_id,photo,photo_rec:$array[vk_uid],$array[first_name],$array[last_name],$array[uid],$array[photo],$array[photo_rec]" );
	} else {
		$array [user_id] = $array [uid];
		my_up ( "vk_user:vk_id=$array[vk_uid]", $array );
	}
}
?>