<?php
class Host {
	static function get_max_olinks_count($host_id){
		return my_qn("select * from link where host_id=$host_id and places_count>0 and weight>0");
	}
	
	static function recount_max_olinks_count($host_id){
		$num=Host::get_max_olinks_count($host_id);
		my_up("host:max_out_links_count=$num:id=$host_id");
	}
	
	
	static function check_code($uri){

		$code_uri = $uri;
//		if (! $code_uri) {
//			$code_uri = "http://" . Host::get_host ( $host_id ) . "/";
//		}
		$user_id=User::get_id()?User::get_id():Host::get_author_id (Host::get_id(get_host($uri)));
		$mw_key = User::get_hash ($user_id);
		//get_request("http://$host/", "", array());
		$rand = rand ( 1, 1000000 );
		$s = request ( $code_uri, array (cookies => array (mw_key => $mw_key, check_code=>1, mw_rand => $rand ) ) );
		$res = preg_match_all("/<mw_responce_$rand>/", $s,$maches)>0?true:false;
		if(!$res){
			$res=$s;
		}
		if($res===true){
			return $res;
		}else{
			return "На странице $uri код не установлен. Пожалуйста, введите адрес страницы с кодом.<br />
		<div class=olist><a class=open>Посмотреть ответ сервера</a><div class='openobj hidden topbox'><div style='width:800px; height:800px; overflow:scroll'><pre>$res</pre></div></div></div>";
		}
	}
	
	static function reset_ya_page($host_id) {
		my_up ( "host:ya_page=0:id=$host_id" );
	}
	
	static function recount_links_weight($host_id){
		$qry=my_q("select * from link where host_id=$host_id");
		for($i=0; $i<my_n($qry); $i++){
			$link_id=my_r($qry, $i, "id");
			recount_link_weight($link_id);
		}
	}
	
	static function ya_index_next($host_id) {
		$ya_num = my_fst ( "select * from host where id=$host_id", "ya_num" );
		$ya_page = my_fst ( "select * from host where id=$host_id", "ya_page" );
		if ($ya_num > 0 && $ya_page * 100 >= $ya_num) {
			return;
		} else {
			$r = update_yandex_index ( $host_id, 100, $ya_page * 100 );
			if (! $r) {
				$time = time ();
				my_up ( "host:filled_time=$time:id=$host_id" );
			}
			$ya_page ++;
			my_up ( "host:ya_page=$ya_page:id=$host_id" );
		}
		Host::recount_max_olinks_count($host_id);
	}
	
	static function get_links($host) {
		$host_id = Host::get_id ( $host );
		$tolink_req = self::$tolink_req;
		$q = my_q ( "select * from $tolink_req where donor_host_id=$host_id" );
		for($i = 0; $i < my_n ( $q ); $i ++) {
			$res [my_r ( $q, $i, "donor_uri" )] [] = array ("uri" => my_r ( $q, $i, "acceptor_uri" ), "text" => my_r ( $q, $i, "text" ) );
		}
		$res [wcheck] = "done";
		return serialize ( $res );
	}
	
	static function get_id($host) {
		$host = my_s ( $host );	
		$host_id = my_fst ( "select id from host where host='$host'", "id" );
		return $host_id;
	}
	
	static function get_host($id) {
		return my_fst ( "select * from host where id=$id", "host" );
	}
	
	static function get_author_id($host_id) {
		return my_fst ( "select user_id from host where id='$host_id'", "user_id" );
	}
	
	static function index($host_id) {
		$host = self::get_host ( $host_id );
		my_up("link:indexed=0:host_id=$host_id");
//		$time=time();
//		my_up("host:reindex_time=$time:id=$host_id");
//		Bot::add_task(BOT_INDEX, $host_id);
		
		$processed_links=array();
		$lid=newurl ( "http://$host/", 1,true);
		$links = get_local_links ( $lid );
		$processed_links[]="http://$host/";
		
		$links=array_substr($links,$processed_links);
		console("processed:"."http://$host/");
		
		$linkslv3 = array ();
		for($i = 0; $i < min ( last_key ( $links ), 1000 ); $i ++) {
			$lid=newurl ( $links [$i], 2 ,true);
			$linkslv3 = array_merge_unique ( $linkslv3, get_local_links ( $lid ) );
			console("processed:".$links [$i]);
		}
		
		$processed_links=array_merge($processed_links, $links);
		$linkslv3=array_substr($linkslv3, $processed_links);
		for($i = 0; $i < min ( count ( $linkslv3 ), 10000 ); $i ++) {
			newurl ( $linkslv3 [$i], 3 ,true);
			//count_out_links($linkslv3 [$i]);
		}
		my_q ( "update link set level=4, indexed=1 where indexed=0 AND host_id=$host_id" );
		return true;
	}
	
	static function recount_links($host_id) {
		$links_count = my_qn ( "select * from link where host_id=$host_id and weight>0 and has_code=1" );
		my_up ( "host:max_out_links_count=$links_count" );
	}
	
	static function recount_out_links_count($host_id) {
		$tolink_req = Host::$tolink_req;
		$ol = my_qn ( "$tolink_req where donor_host_id=$host_id" );
		my_up ( "host:out_links_count=$ol:id=$host_id" );
	}
	
	static function recount_in_links_count($host_id) {
		$tolink_req = Host::$tolink_req;
		$il = my_qn ( "$tolink_req where acceptor_host_id=$host_id" );
		my_up ( "host:in_links_count=$il:id=$host_id" );
	}
	
	static function recount_olinks($host_id) {
		$host = self::get_host ( $host_id );
		$q = my_q ( "select * from link where host_id=$host_id and weight>0" );
		for($i = 0; $i < my_n ( $q ); $i ++) {
			$id = my_r ( $q, $i, "id" );
			count_out_links ( $id );
		}
	
	}
	
	static function update($id) {
		// 	if(is_int($host)){
		// 		$host=Host::get_host($host);
		// 	}
		$r = my_q ( "select * from host where id=$id" );
		$code_uri = my_fst ( $r, "code_uri" );
		if (! $code_uri) {
			$code_uri = "http://" . Host::get_host ( $id ) . "/";
		}
		
		$mw_key = User::get_hash ( Host::get_author_id ( $id ) );
		//get_request("http://$host/", "", array());
		$rand = rand ( 1, 1000000 );
		$res = request ( $code_uri, array (cookies => array (mw_key => $mw_key, update_links => true, mw_random => $rand ) ) );
		
		
	
		//cho "<pre>".h2s($res)."</pre>";
	}
	
	static function count_places($s, $is_uri = true, $mw_rand = false) {
		if ($is_uri) {
			$mw_rand = rand ( 1, 100000 );
			$s = upload ( $s, array (cookies => array (mw_key => User::get_hash (), index => true, mw_random => $mw_rand ) ) );
		}
		$checkstring = st_vars::$check_code_string;
		//cho h2s("<!--$checkstring"."_$mw_rand-->");
		$res = preg_match_all("/<!--$checkstring" . "_$mw_rand-->/", $s,$maches);
		//cho $res;
		//cho h2s($s);exit;
		return $res;
	}
	
	static $tolink_req = "
	select tolink.placed, tolink.id, tolink.text, acceptor_link.uri as acceptor_uri,
	acceptor_link.host_id as acceptor_host_id, donor_link.uri as donor_uri,
	donor_link.host_id as donor_host_id from (
		tolink left join link as acceptor_link on tolink.acceptor_uri_id=acceptor_link.id
	) left join link as donor_link on tolink.donor_uri_id=donor_link.id
";
	static $tohost_req = "
		select * from (
			tohost left join host as acceptor_host on tohost.acceptor_host_id=acceptor_host.id
		) left join host as donor_host on tohost.donor_host_id=donor_host.id
	";
	
	static $link_req = "
		select * from (
			link left join host on link.host_id=host.id
		)
	";
	
	static $link_text_req = "
		select * from (
			link_text left join (link left join host on link.host_id=host.id) on link_id=link.id
		)
	";
}

function newsite($uri, $theme) {
	$uid = User::get_c_id ();
	if (! $uid) {
		return "Необходимо войти.";
	}
	$host = get_host ( $uri, true );
	if (! $host) {
		return "Не удаётся распознать хост.";
	}
	$hosti = my_s ( $host );
	if (my_qn ( "select * from host where host='$hosti' and active=1" )) {
		return "Такой сайт уже существует.";
	}
	if (! $theme) {
		return "Выберите рездел.";
	}
	$uwc = is_url ( $uri ) ? $uri : "http://$host/";
	$mw_rand = rand ( 1, 100000 );
	//$s = upload ( $uwc, array (cookies => array (mw_key => User::get_hash (), index => true, mw_random => $mw_rand ) ) );
	$code_installed = Host::check_code ( $uwc );
	if ($code_installed !== true) {
		$s=$code_installed;
		//$s = h2s ( $s );
		return $s;
	}
	$level = get_host_level ( $host );
	if ($level > 2 && getTCY ( "http://$host/" < 10 )) {
		return "Сайт должен быть первого уровня, или иметь ТИЦ больше нуля.";
	}
	$uwc = my_s ( $uwc );
	$hostq=my_q("select * from host where host='$hosti'");
	if (my_n ( $hostq )) {
		my_up ( "host:user_id=$uid,code_uri=$uwc,theme_id=$theme:host='$hosti'" );
		$host_id=my_fst($hostq, "id");
	}else{
		my_in ( "host:host=$hosti,user_id=$uid,code_uri=$uwc,theme_id=$theme" );
		$host_id = mysql_insert_id ();
	}
	$lid = get_uri_id ( "http://$host/" );
	$text = my_s ( "http://$host/" );
	
	my_in ( "link_text:link_id=$lid,text=$text" );
	//Bot::add_task(BOT_YA_INDEX, $host_id);
	ya_index_next($host_id);
	if(!vars::$yandex_index_required||my_fst("select * from host where id=$host_id","ya_num")>0){
		my_up("host:active=1:id=$host_id");
		$reindex=BOT_INDEX;
		Bot::add_task(BOT_INDEX, $host_id);
		return true;
	}else{
		return "Сайт должен быть проиндексирован яндексом.";
	}

		// 	index_site($host);
// 	update_yandex_index($host, $lim);
}

function newhost($uri){
	$hostname = get_host($uri, true);
	$id = Host::get_id($hostname);
	if(!$id){
		$hostname=my_s($hostname);
		$id = my_in("host:host=$hostname");
	}
	return $id;
}
?>