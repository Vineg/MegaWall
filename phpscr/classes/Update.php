<?php
class Update {
	static function link(){
		$linkq=my_q("select * from link where weight>0 and out_links<0");
		$cnt=my_n($linkq);
		if($cnt>0){
			log_msg("Ссылки с не пересчитанными olinks : $cnt");
		}
		for($i=0; $i<$cnt; $i++){
			Bot::add_task(BOT_RECOUNT_OLINKS, my_r($linkq, $i, "id"));
		}
	}
	
	static function tohosts() {
		$tohost_req = Host::$tohost_req;
		if (vars::$debug) {
			$max_in_hosts = 10; //tmp
		}
		$hosts = my_q ( "select * from host where in_hosts_count<$max_in_hosts" );
		for($i = 0; $i < my_n ( $hosts ); $i ++) {
			$host_id = my_r ( $hosts, $i, "id" );
			$update_tohost=BOT_UPDATE_TOHOST;
			Bot::add_task($update_tohost, $host_id);
		}
	}
	
	static function tolink($acceptor_host_id) {
		$link_req=Host::$link_req;
		$link_text_req=Host::$link_text_req;
		$host = my_q ( "select * from host where id=$acceptor_host_id" );
		$in_links_weight = my_fst ( $host, "in_links" );
		$max_out_links_weight = my_fst ( $host, "max_out_links" );
		$max_in_links_weight = $max_out_links_weight * 0.9;
		if ($in_links_weight < $max_out_links_weight) {
			$tohosts = my_q ( "select * from tohost order by links_count inc" );
			//$tohosts_count=my_n($tohosts);
			for($i = 0; $i < rand ( 0, st_vars::links_per_day ); $i ++) {
				$tohost_id=my_r($tohosts, $i, "id");
				$donor_host_id = my_r ( $tohosts, $i, "donor_host_id" );
				$acceptor_host_id = my_r ( $tohosts, $i, "acceptor_host_id" );
				$donor_link_id = my_fst ( "$link_req where host_id=$donor_host_id and my_out_links<host.links_per_page and weight>0 order by level asc,rand() limit 0,1", "id" );
				if (vars::$debug) {
					my_fst ( "$link_req where host.id=$donor_host_id and my_out_links<host.links_per_page and weight>=0 order by level asc,random() limit 0,1", "id" ); //tmp
				}
				$text = my_fst ( "select * from  where host_id=$acceptor_host_id order by rand() limit 0,1", "text" );
				$text = my_s ( $text );
				$host = Host::get_host ( $acceptor_host_id );
				$acceptor_link = my_s ( "http://" . $host . "/" );
				$donor_link = my_s ( $donor_link );
				$weight = my_fst ( "select * from link where id=$donor_link_id", "weight" );
				$acceptor_link_id = get_uri_id ( $acceptor_link );
				my_in ( "tolink:donor_uri_id=$donor_link_id,acceptor_uri_id=$acceptor_link_id,tohost_id=$tohost_id" );
				Bot::add_task("update_host", $donor_host_id);
			}
		} else {
		
		}
	}
	
	static function tohost($acceptor_host_id) {
		$theme_id = my_fst ( "select * from host where id=$acceptor_host_id", "theme_id" );
		
		//		$dissallowed_hosts_q=my_q("select acceptor_host_id from tolink where donor_host_id=$host_id");
		

		// 		for($i=0; $i<my_n($dissallowed_hosts_q); $i++){
		// 			$dissallowed_hosts[]=my_r($dissallowed_hosts_q, $i, "acceptor_host_id");
		// 		}
		

		$tohost_req = Host::$tohost_req;
		$hosts_count = my_qn ( "select * from host where 1" );
		$max_in_hosts = ceil ( min ( $hosts_count / 100, 1000 ) );
		$thematic_hosts_count = my_qn ( "select * from host where theme_id=$theme_id" );
		$max_thematic_in_hosts = ceil ( min ( $thematic_hosts_count / 100, 1000 ) );
		$thematic_donor_hosts_count = my_qn ( "$tohost_req where donor_host.theme_id=$theme_id and acceptor_host.id=$acceptor_host_id" );
		$thematicd = $max_thematic_in_hosts - $thematic_donor_hosts_count;
		if ($thematicd > 0) {
			$maxnh = $thematicd;
		} else {
			$donor_hosts_count = my_qn ( "select * from tohost where donor_host_id=$acceptor_host_id" );
			$theme_id = 0;
			$maxnh = $max_in_hosts - $donor_hosts_count;
		}
		$nh = min ( rand ( 0, 3 ), $maxnh );
		self::add_tohosts ( $acceptor_host_id, $nh, $theme_id );
	}
	
	static function add_tohosts($acceptor_host_id, $hosts_count, $theme_id) {
		$thematic = $theme_id ? 1 : 0;
		$max_link_value = st_vars::$max_link_value;
		for($i = 0; $i < $hosts_count; $i ++) {
			$dissallowed_hosts = array ();
			$dissallowed_hosts [] = $acceptor_host_id;
			if ($dissallowed_hosts) {
				for($y = 0; $y < count ( $dissallowed_hosts ); $y ++) {
					$dissallowed_hosts [$y] = "id!=" . $dissallowed_hosts [$y];
				}
			}
			$dissallowed_hosts = join ( " and ", $dissallowed_hosts );
			$dissallowed_hosts = $dissallowed_hosts ? "and $dissallowed_hosts" : "";
			$theme_filter = $thematic ? "and theme_id=$theme_id" : "";
			$donor_hosts = my_q ( "
select * from host where out_links-in_links<$max_link_value*2 $dissallowed_hosts $theme_filter and (moderated=-1 or moderated=1)
order by rand() limit 0, $hosts_count" );
			for($i = 0; $i < my_n ( $donor_hosts ); $i ++) {
				$donor_host_id = my_r ( $donor_hosts, $i, "id" );
				if (! my_qn ( "select * from tohost where donor_host_id=$donor_host_id and acceptor_host_id=$acceptor_host_id" )) {
					my_in ( "tohost:donor_host_id=$donor_host_id,acceptor_host_id=$acceptor_host_id,thematic=$thematic" );
					my_up ( "host:in_hosts_count='in_hosts_count+1':id=$acceptor_host_id" );
				}
			}
		}
	}
	
	static function check_links(){
		$time=time();
		$placedlinks=my_q("select * from tolink where placed=1 and $time-check_time>60*60*24*7;");
		$unplacedlinks=my_q("select * from tolink where placed=1 and $time-check_time>60*60*24*1;");
		for($i=0; $i<my_n($placedlinks); $i++){
			Bot::add_task("check_link", my_r($placedlinks, $i, "id"));
		}
		for($i=0; $i<my_n($placedlinks); $i++){
			Bot::add_task("check_link", my_r($placedlinks, $i, "id"), 0.01);
		}
	}
}

function daily_update(){
	shell_exec('mysqldump -uroot -P3306 -p123455 blog | gzip -c > /media/flash/mysqlbackup/`date "+%Y-%m-%d"`.gz');
	$cdate=time();
	$ucnt=get_users_cnt_rc();
	my_q("update post set daily_rate=daily_rate/(".(1+sqrt(1+$ucnt)).")");
	my_q("TRUNCATE TABLE vote");
	my_q("update post set a=0 where 1;");
	my_q("update post set daily_rate=1 where true order by rand() limit 1");
	update_archive();
	set_svar("last_update", "curdate()", true);
	set_svar("ya_requests", "0");
	Update::tohosts();
	Update::check_links();
	Update::link();
	submit_stack();
}

function hourly_update(){
	$dummy_sites_q = my_q("select * from project where project='dummy_site'");
	for($i=0; $i<my_n($dummy_sites_q); $i++){
		$type_id = my_r($dummy_sites_q, $i, "type_id");
		$users_cnt = my_n(Dummy::get_users_query($type_id));
		$posts_cnt = my_qn("select * from post where type_id = $type_id and pub>0");
		$max_users_count = $posts_cnt*7;
		if($users_cnt<$max_users_count){
			//cho rand(0, 23);
			if(rand(0, 23)<3){
				//cho 111111;
				add_pseudo_user($type_id);
			}
		}
	}
}

function add_pseudo_user($type_id){
	echo("tid:$type_id");
	$max_user_id = 1000000;
	$sex = rand(1,2);
	$infoar = generate_random_page_info($sex);
	$name = get_random_name($max_user_id, $sex);
	$surname = get_random_surname($max_user_id, $sex);
	$sex = $sex==2?"Мужской":"Женский";
	//$name = $name[0];
	//$surname = $surname[0];
	$info = array();
	if($name&&$surname){
		$info[]="Полное имя: $surname $name";
		$info[]="Пол $sex";
		foreach ($info as $key=>$value){
			$info[$key] = "<li>$value</li>";
		}
		
		foreach ($infoar as $key=>$value){
			if($key&&$value){
				$info[] = "<li>$key $value</li>";
			}
		}
		$info = "<ul>".join("", $info)."</ul>";
		$post = new Post();
		$post->pub = 0;
		$post->name = "Пользователь $name $surname";
		$post->content = $info;
		$post->link = s2link::translit($name)."-".s2link::translit($surname);
		$post->vars = "user";
		$post->type_id=$type_id;
		$post->submit();
		$cnt = my_n(Dummy::get_users_query($type_id));
		echo("log");
		log_msg("added puser number $cnt in type $type_id $name $surname");
	}else{
		debug_message("failed add puser");
	}

}

function get_random_name($max_user_id,$nsex=false, $deepness=0){
	$nsex = $nsex?$nsex:rand(1,2);
	if($deepness>7){
		return false;
	}
	sleep(0.5);
	//$uid = rand(1, $max_user_id);
	$api = new VKapi();
	$uids = random_search_vk_ids($nsex);
	$uid = $uids[0];
	$profile = $api->getProfiles($uid);
	$ar=$profile[response][0][user];
	//$name_ar = get_random_full_name($max_user_id);
	//rint_r($ar);
	$name = $ar[first_name];
	if(!$name){debug_message(print_r($profile));}
// 	if(!$name||!$sex||($nsex&&$nsex!=$sex)){
// 		$deepness++;
// 		return get_random_name($max_user_id,false, $deepness);
// 		exit;
// 	}
	return $name;
}


function get_random_surname($max_user_id, $nsex=false, $deepness=0){
	$nsex = $nsex?$nsex:rand(1,2);
	if($deepness>7){
		return false;
	}
	sleep(0.5);
	//$uid = rand(1, $max_user_id);
	$uids = random_search_vk_ids($nsex);
	$uid = $uids[0];
	$api = new VKapi();
	$profile = $api->getProfiles($uid);
	$ar=$profile[response][0][user];
	//$name_ar = get_random_full_name($max_user_id);
	//print_r($ar);
	$surname = $ar[last_name];
	if(!$surname){debug_message(print_r($profile));}
// 	if(!$surname||!$sex||($nsex&&$nsex!=$sex)){
// 		$deepness++;
// 		return get_random_surname($max_user_id,false, $deepness);
// 	}
	return $surname;
}

function random_search_vk_ids($sex){
	$offset = rand(0, 900);
	$remixsid = VK::login("vineg@yandex.ru", "Prosbanebespokoitsru");
	$s = request("http://vk.com/al_search.php", array(post_data=>"al=1&c%5Bname%5D=1&c%5Bsection%5D=people&c%5Bsex%5D=$sex&offset=$offset",cookies=>array(remixchk=>5,remixsid=>$remixsid, remixlang=>0)));
	$s = mb_convert_encoding($s, "UTF8", "windows-1251");
	preg_match_all("#\"/([a-z0-9]+)\"#", $s, $links);
	$res = array();
	$links = $links[1];

	foreach ($links as $name=>$value){
		if(!in_array($value,$res)){
			$res[]=$value;
		}
	}
	

	return $res;
}

function generate_random_page_info($sex){
	$res = array();
	$max_user_id = 1000000;
	$n=0;
	for($i=0; $i<10; $i++){
		$page_info = get_random_page_info($max_user_id, $sex);
		if($page_info[$n][0]){
			$n++;
			$res[$page_info[$n][0]]=$page_info[$n][1];
		}
	}
	return $res;
}

function get_random_page_info($max_user_id, $sex){
	sleep(1);
	$uids = random_search_vk_ids($sex);
	$uid = $uids[0];
	//$uid = rand(1, $max_user_id);
	//$uid = 1481827;
	//$uid = 1023;
	$s = get_page($uid);
// 	echo h2s($s."1");
// 	exit;
	// 		exit;
	//$s = "<title>Павел Дуров</title>";
	//cho h2s($s);
	preg_match_all('#<div class="label fl_l">(.+)</div>([^<]*)<div class="labeled fl_l">(.+)</div>#u', $s, $matches);
	//preg_match_all('#<div class="labeled fl_l">(.+)</div>#u', $s, $matches2);
	h2sprint_r($matches);
	$keys = $matches[1];
	$values = $matches[3];
	//		print_r($matches);
	//exit;
	$res = array();
	for($i=0; $i<count($keys); $i++){
		$nkey = strip_tags($keys[$i]);
		if($nkey&&!in_array($nkey, array("Родной город:", "Веб-сайт:", "Сестра:", "Брат:", "Сестры:", "Братья:", "Братья, сестры:", "Родители:", "Семейное положение:", "Отец:", "Мать:", "Twitter:", "Skype:"))){
			$value = strip_tags($values[$i]);
			if(!$value){continue;}
			$res[] = array($nkey, $value);
		}
	}
	// 		unset($res["Родной город:"]);
	// 		unset($res["Веб-сайт:"]);
	return $res;
}



function submit_stack(){
	$typeq = my_q("select * from type where start=1");
	for ($i=0; $i< my_n($typeq); $i++){
		$main_type_id = my_r($typeq, $i, "id");
		$query = my_q("select * from stack_post where main_type_id=$main_type_id order by RAND()");
		//cho $main_type_id."<br />";
		if(my_n($query)>0){
			$stack_id = my_fst($query, "id");
			$post_id = my_fst($query, "post_id");
			$pub = my_fst($query, "pub");
			my_up("post:pub=$pub:id=$post_id");
			my_q("delete from stack_post where id = $stack_id");
		}
	}
}

function update_archive(){
	$tres=my_q("select id from type where pub>=1");
	for($i=0; $i<my_n($tres); $i++){
		$arc=array();
		$types_ar=null;
		$ot="";
		$arcs="";
		$tid=my_r($tres, $i, "id");
		$types_ar=Type::get_all_childs($tid);
		$types_ar[0]=h2s($types_ar[0]);
		for($j=1; $j<count($types_ar); $j++){
			$types_ar[$j]=h2s($types_ar[$j]);
			$ot.="or type_id='$types_ar[$j]'";
		}
		$type_f=$types_ar[0]!==null?"and (type_id='$types_ar[0]' $ot)":"";
		$pres=my_q("select id from post where pub>=2 and parent=0 $type_f order by daily_rate desc limit 0,100");
		for($j=0; $j<my_n($pres); $j++){
			$arc[$j]=my_r($pres, $j, "id");
		}
		if(count($arc)<1){
			continue;
		}
		$arcs=implode(" ", $arc);
		my_q("insert into archive(date, type_id, posts) values(CURDATE(), '$tid', '$arcs');");
		print "insert into archive(date, type_id, posts) values(CURDATE(), '$tid', '$arcs');";
	}
	set_svar("archanged", true);
}

function get_users_cnt_rc(){
	$q=my_q("select id from user");
	return mysql_num_rows($q);
}

function check_users(){
	$minurate=st_vars::$rate_user_posts_remove;
	$rate_posts_remove=st_vars::$rate_post_auto_remove;
	$q=my_q("select * from user where rate<=$minurate");
	for($i=0; $i<my_n($q); $i++){
		$cuid=my_r($q, $i, "id");
		my_q("update post set pub=-2 where author_id=$cuid and rate<=$rate_posts_remove;");
	}
}

function update_hosts(){
	$hosts=my_q("select * from host where max_out_links_count=out_links_count and filled_time=0");
	for($i=0; $i<count($hosts); $i++){
		$host_id=my_r($hosts, $i, "id");
		my_in("bot_task:task=ya_index,host_id=$host_id");
	}
}

//function update_links(){
//	$tohosts=my_q("select * from tohost where links_count=0");
//	for($i=0; $i<$tohosts; $i++){
//		$donor_host_id=my_r($tohosts, $i, "donor_host_id");
//		$acceptor_host_id=my_r($tohosts, $i, "acceptor_host_id");
//
//		$links_per_page=my_r($donor_hosts, $i, "links_per_page");
//		$donor_link_id=my_f("select * from link where host_id=$donor_host_id and my_out_links<$links_per_page and weight>0 order by level asc,random() limit 0,1", "id");
//		if(vars::$debug){
//			my_f("select * from link where host_id=$donor_host_id and my_out_links<$links_per_page and weight>=0 order by level asc,random() limit 0,1", "id");//tmp
//		}
//		$text=my_f("select * from host_texts where host_id=$acceptor_host_id order by random() limit 0,1", "text");
//		$text=my_s($text);
//		$host=Host::get_host($acceptor_host_id);
//		$acceptor_link=my_s("http://".$host."/");
//		$donor_link=my_s($donor_link);
//		$weight=my_f("select * from link where id=$donor_link_id", "weight");
//		$acceptor_link_id=get_uri_id($acceptor_link);
//		my_in("tolink:donor_uri_id=$donor_link_id,acceptor_uri_id=$acceptor_link_id,donor_host_id=$donor_host_id,acceptor_host_id=$host_id");
//		if(!my_qn("select * from bot_task where params=$donor_host_id")){
//			my_in("bot_task:task=update_host,params=$donor_host_id");
//		}
//	}
//}

function links_bot(){
	// 	if(get_svar("link_bot_on")){
	// 		return false;
	// 	}else{
	//		set_svar("link_bot_on", "1");
	//		register_shutdown_function('bot_shutdown');
	$update_tolink=BOT_UPDATE_TOLINK;
	$reindex=BOT_INDEX;
	$update_tohost=BOT_UPDATE_TOHOST;
	$ya_index=BOT_YA_INDEX;
	$recount_olinks_all=BOT_RECOUNT_OLINKS_ALL;
	$recount=BOT_RECOUNT_OLINKS;
	
	$reindex_tasks=my_q("select * from bot_task_host where task=$reindex order by rate desc");
	for($i=0; $i<min(my_n($reindex_tasks), 1); $i++){
		$host_id=my_r($reindex_tasks, $i, "host_id");
		Host::index($host_id);
		print "reindexed:$host_id\n";
		my_q("delete from bot_task_host where task=$reindex AND host_id='$host_id'");
	}

	$recount_olinks_tasks=my_q("select * from bot_task_link where task=$recount order by rate desc");
	for($i=0; $i<min(my_n($recount_olinks_tasks), 1); $i++){
		$uri_id=my_r($recount_olinks_tasks, $i, "link_id");
		$linkq=my_q("select * from link where id=$uri_id");
		//$lvl=my_f($linkq,"level");
		//$uri=my_f($linkq, "uri");
		//$host_id=my_r($recount_olinks_tasks, $i, "var2");
		//$hostq=my_q("select * from host where id=$host_id");
		//$host_index_time=my_f($hostq, "reindex_time");
		//$last_recount_time=my_f($linkq, "recount_time");
		$index=false;//$index=($lvl<3)&&($host_index_time>=$last_recount_time)?true:false;
		count_out_links($uri_id, $index);
		print "recounted:$uri_id\n";
		my_q("delete from bot_task_link where task=$recount AND link_id='$uri_id'");
//		if(!my_qn("select * from bot_task where task=$recount AND var2=$host_id")){
//			$indextask=BOT_INDEX;
//			my_q("delete from bot_task where task=$indextask and params=$host_id");
//		}
	}

	$update_tasks=my_q("select * from bot_task_host where task='update_host' order by rate desc");
	for($i=0; $i<min(my_n($update_tasks), 3); $i++){
		$host_id=my_r($update_tasks, $i, "host_id");
		Host::update($host_id);
		print "updated:$host_id\n";
		my_q("delete from bot_task_host where task='update_host' AND host_id='$host_id'");
	}

	$ya_index_tasks=my_q("select * from bot_task_host where task=$ya_index order by rate desc");
	for($i=0; $i<min(my_n($ya_index_tasks), 3); $i++){
		$host_id=my_r($ya_index_tasks, $i, "host_id");
		Host::ya_index_next($host_id);
		print "ya_indexed:$host_id\n";
		my_q("delete from bot_task_host where task=$ya_index AND host_id=$host_id");
	}
	
	$recount_olinks_all_tasks=my_q("select * from bot_task_host where task=$recount_olinks_all order by rate desc");
	for($i=0; $i<min(my_n($recount_olinks_all_tasks), 3); $i++){
		$host_id=my_r($recount_olinks_all_tasks, $i, "host_id");
		Host::recount_olinks($host_id);
		print "recounted all olinks:$host_id\n";
		my_q("delete from bot_task_host where task=$recount_olinks_all AND host_id=$host_id");
	}
	
	$update_tohost_tasks=my_q("select * from bot_task_host where task=$update_tohost order by rate desc");
	for($i=0; $i<min(my_n($update_tohost_tasks), 3); $i++){
		$acceptor_host_id=my_r($update_tohost_tasks, $i, "host_id");
		Update::tohost($acceptor_host_id);
		print "updated_tohost:$acceptor_host_id\n";
		my_q("delete from bot_task_host where task=$update_tohost AND host_id='$acceptor_host_id'");
		Bot::rem_task($update_tohost, $acceptor_host_id);
	}
	
	$update_tolink_tasks=my_q("select * from bot_task_host where task=$update_tolink order by rate desc");
	for($i=0; $i<min(my_n($update_tohost_tasks), 3); $i++){
		$acceptor_host_id=my_r($update_tohost_tasks, $i, "host_id");
		Update::tolink($acceptor_host_id);
		print "updated_tolink:$acceptor_host_id\n";
		my_q("delete from bot_task_host where task=$update_tolink AND host_id='$acceptor_host_id'");
	}
	
	$check_tolink_tasks=my_q("select * from bot_task_link where task='check_tolink' order by rate desc");
	for($i=0; $i<min(my_n($check_tolink_tasks), 3); $i++){
		$tolink_id=my_r($check_tolink_tasks, $i, "link_id");
		Link::check_tolink($tolink_id);
		print "checked_tolink:$tolink_id\n";
		my_q("delete from bot_task_link where task='check_tolink' AND link_id='$tolink_id'");
	}
	// 		set_svar("link_bot_on", "0");
	// 	}
}

function bot_shutdown(){
	set_svar("link_bot_on", "0");
}

function console($s){
	print $s."\r\n";
	flush();
}