<?php
include('php_section/test/msn/sendMsg/sendMsg.php');

function get_users_cnt(){
	return my_fst("select value from var where name='users_cnt'", "value");
}

// function get_svars(){
// 	$res=my_f("select value from var where name='vars' limit 0,1", "value");
// 	$res=unserialize($res);
// 	if(!$res){$res=array();}
// 	return $res;
// }

// function update_svars($ar){
// 	$ar0=get_svars();
////cho "ar0$ar0";
////cho "ar$ar";
// 	$ar=array_merge($ar+$ar0);
// 	$res=serialize($ar);
////	cho $res;
// 	my_q("update var set value = '$res' where name='vars'");
// }

// function get_svars(){
// 	$res=my_f("select value from var where name='vars' limit 0,1", "value");
// 	$res=unserialize($res);
// 	if(!$res){$res=array();}
// 	return $res;
// }

// function update_svars($ar){
// 	$ar0=get_svars();
////cho "ar0$ar0";
////cho "ar$ar";
// 	$ar=array_merge($ar+$ar0);
// 	$res=serialize($ar);
////	cho $res;
// 	my_q("update var set value = '$res' where name='vars'");
// }

function get_svar($var){
	$var=my_s($var);
	return my_fst("select * from var where name='$var'", "value");
}

function set_svars($array){
	foreach($array as $name=>$value){
		set_svar($name, $value);
	}
}

function set_svar($name, $value, $not_string=false){
	if($not_string){
	}else{
		$value=my_s($value);
		$value="'$value'";
	}
	$name=my_s($name);
	if(my_qn("select * from var where name='$name'")){
		my_q("update var set value=$value where name='$name'");
	}else{
		my_q("insert into var(name, value) values ('$name',$value);");
	}
}

function form_get($ar){
	if(count($ar)==0){return false;}
	$res="?";
	$n=0;
	foreach ($ar as $key=>$par){
		if($key){
			$res.=$par?"$key=$par&":"$key&";
			$n++;
		}
	}
	if(!$n){return false;}
	$res=substr($res, 0, strlen($res)-1);
	return $res;
}

function save_session($id=false){
	if(User::get_id()||$id){
		$ss=my_s(serialize($_SESSION));
		$id=$id?$id:User::get_id();
		
		my_q("update user set session='$ss' where id=$id");
	}
}

function load_session(){
	if(User::get_s_id()){
		$SESSION=unserialize(my_fst("select session from user where id ='".User::get_id()."'", "session"));
		if($SESSION){
			$_SESSION=array_merge($SESSION, $_SESSION);
		}
	}
}

function slog($s){
	my_q("insert into log(msg) values('$s')");
}


function set_class_vars($class, $array){
	foreach($array as $key=>$value){
		$class->{$key}=$value;
	}
	return $class;
}

function get_class_vars_r($class){
	$array=get_class_vars(get_class($class));
	foreach($array as $key=>$value){
		$array[$key]=$class->{$key};
	}
	return $array;
}

function my_sqrt($i){
	if($i<0){$i=-$i; $sgn=1;}
	$res=sqrt($i+1)-1;
	$res=$sgn?-$res:$res;
	return $res;
}

function my_sqrt_z($i){
	$res=my_sqrt($i);
	return $res>0?$res:0;
}

function post_yandextop(){
	$keyword_array=st_vars::$keyword_array;
	for($i=0;$i<count($keyword_array);$i++){
		post_yandextop_keyword($keyword_array[$i]);
	}
}

function post_yandextop_keyword($s){
	$res=load_yandextop($s);
	for($i=0; $i<count($res); $i++){
		$str=my_s($res[$i])."?";
		$from=Post_const::$post_select;
		$sysid=st_vars::$system_id;
		$type_yandextop=vars::$type_yandextop;
		//cho "select * from $from where author_id=$sysid and type_id=$type_yandextop and content='$str'";
		if(!my_qn("select * from $from where author_id=$sysid and type_id=$type_yandextop and content='$str'")){
			//cho "posted";
			$post=new Post();
			$uid=st_vars::$system_id;
			$ulink=User::get_u_link_id($uid);
			$post->name="Вопросы Яндекса";
			$post->version=0;
			$post->text=$str;
			$post->ftext=$str;
			$post->link=s2link::translit($str);
			$post->rate=Post::get_new_post_rate($uid);
			$post->author_id=$uid;
			$post->pub=1;
			$post->type_id=$type_yandextop;
			$post->submit();	
		}
	}
}

function load_yandextop($s){
	$str=urldecode($s);
	$res=upload("http://suggest.yandex.ru/suggest-ya.cgi?v=3&callback=jsonp1307513717821&part=$str&lr=2&yu=228302061301459515");
	for($i=0;$i<2;$i++){$res=crop($res, "[", "]");}
	$res=mark_out($res, "\"");
	return($res);
}

function lpost($file, $dataar){
	foreach ($data as $key=>$value){
		$dataar1.="$key=$value";
	}
	$data=join("&", $dataar1);
	
	$fp = fsockopen(vars::$host, 80, $errno, $errstr, 10); // открыть указанный хост по 80 порту
	$out = "POST $file HTTP/1.1\n"; // открыть данный скрипт
	$out .= "Host: ".vars::$host."\n";
	$out .= "Referer: http://".vars::$host."$_SERVER[REQUEST_URI]/\n";
	$out .= "User-Agent: Opera\n";
	$out .= "Content-Type: application/x-www-form-urlencoded\n";
	$out .= "Content-Length: ".strlen($data)."\n\n";
	$out .= $data."\n\n";
	fputs($fp, $out); // отправка данных принимающему скрипту
	fclose($fp);
}

function error($msg=""){
	if(vars::$debug){
		print $msg."-----";
		print_br(debug_backtrace());
	}else{
		$s=my_s($msg."----".debug_backtrace());
		log_msg("$s");
	}
}

function update_vk_user_friends_list($uid){
	$userids=get_vk_friendslist($uid);
	if(!count($userids)){
		return false;
	}
	$secret=vars::$vk_secret_key;
	$liststr=join(" ", $userids);
	if($liststr){
		$cvkid=User::get_vk_id();
		//cho h2s("insert into vk_friends_list(vk_user_id, list, date) values ($cvkid, ENCODE('$liststr', '$secret'), CURDATE())");
		my_q("insert into vk_friends_list(vk_user_id, list, date) values ($cvkid, ENCODE('$liststr', '$secret'), ".time().")");
		my_q("update vk_user set last_vk_friends_list_update=".time()." where vk_id=$cvkid");
		return true;
	}else{
		return false;
	}
}

function get_vk_friendslist($uid){
	$email=User::get_vk_email();
	$pass=User::get_vk_pass();
	$remixsid=login_vk("$email", "$pass");
	if(!$remixsid){
		my_q("update vk_user set vk_login_fail=vk_login_fail+1 where vk_id=$uid");
		return false;
	}
	$cookies[remixsid]=$remixsid;
	$res=post_request("http://vk.com/al_friends.php","act=load_friends_silent&al=1&gid=0&id=$uid", $cookies);
	$list=$res[content];
	$list=mark_out($list, "{", "}");
	$list=$list[0];
	$list=substr($list, 7, strlen($list)-8);
	$list=mark_out($list, "[", "]");
	for($i=0; $i<count($list); $i++){
		$nar=s2ar($list[$i], ",");
		for($y=0; $y<count($nar); $y++){
			$nar[$y]=substr($nar[$y], 1, strlen($nar[$y])-2);
		}
		$list[$i]=$nar;
	};
	$users=array();
	for($i=0; $i<count($list); $i++){
		$usar=$list[$i];
		$nmar=explode(" ", $usar[4]);
		$first_name=iconv("CP1251", "UTF-8", $nmar[0]);
		$last_name=iconv("CP1251", "UTF-8", $nmar[1]);
		$users[$i]=array(first_name=>$first_name, last_name=>$last_name, id=>$usar[0], photo_rec=>$usar[1]);
		$userids[$i]=$usar[0];
	}
	for($i=0; $i<count($users); $i++){
		new_vk_user($users[$i]);
	}
	return $userids;
}

function close_topframe(){
	if(vars::$no_redirect){return;}
	print <<<EOQ
			<script>
				top.postMessage("mw_iframe_cmd=close_iframe&but=$_POST[iframebut]&stat=ok","*");
			</script>
EOQ;
}

function ret($s){
	print($s);
	exit;
}

/*function get_ya_index($url){
	sleep(1);
	$url0=$url;
	if(substr($url, 0, 6)=="http://"){
		$url=substr($url, 6);
	}
	$uurl=urlencode($url);
	cho upload(
	"http://yandex.ru/yandsearch?date=&text=site%3A$uurl&site=&rstr=&zone=all&wordforms=all&lang=all&within=0&from_day=&from_month=&from_year=&to_day=&to_month=&to_year=&mime=all&numdoc=1&lr=2&p=1&ajax=1&numdoc=10"
	);
// 	return strpos(upload(
// 	"http://yandex.ru/yandsearch?date=&text=&site=$uurl&rstr=&zone=all&wordforms=all&lang=all&within=0&from_day=&from_month=&from_year=&to_day=&to_month=&to_year=&mime=all&numdoc=1&lr=2"
// 	), "href=\"$url0\"")===false?false:true;
}*/

function get_all_links($uri){
	$res=array();
	$user_id=Host::get_author_id(Host::get_id(get_host($uri)));
	$mw_rand=rand(1, 100000);
	$s=upload($uri, array(cookies=>array(mw_key=>User::get_hash($user_id), index=>true, mw_random=>$mw_rand)));
	$linksar=array();
	$as=preg_match_all('/<a([^>]*)>/', $s, $matches);
	$as=$matches[0];
	for($i=0; $i<count($as); $i++){
		$link=$as[$i];
		if(get_attr($link, "rel")!="nofollow"){
			$res[$i]=get_attr($link, "href");
		}
	}
	$places_count=Host::count_places($s, false, $mw_rand);
	$uri=my_s($uri);
	my_up("link:places_count=$places_count:uri='$uri'");
	//sleep(0.1);
	return $res;
	//cho $as[164];
	//$link=
}

function get_local_links($uri_id){
	$res=array();
	$uri=Link::get_uri($uri_id);
	$host=get_host($uri);
	$links=get_all_links($uri);
	for($i=0; $i<last_key($links); $i++){
		if(get_host($links[$i])==$host){
			if(array_search($links[$i], $res)===false){$res[]=$links[$i];};
		}
	}
	$oln=0;
	for($i=0; $i<count($links); $i++){
		if(get_host($links[$i])&&get_host($links[$i])!=$host){
			$oln++;
		}
	}
	$uri=my_s($uri);
	$time=time();
	my_up("link:out_links=$oln,recount_time=$time:uri='$uri'");
	recount_link_weight($uri_id);
	Bot::rem_task(BOT_RECOUNT_OLINKS, $uri_id);
	return $res;
}

function count_out_links($id,$record_in_links=false, $lvl=4){
	$uri=Link::get_uri($id);
	$uris=my_s($uri);
//	if(my_f("select * from link where uri='$uri'", "out_links")!="-1"){
//		return;
//	}
	$links=get_all_links($uri);
	$oln=0;
	$host=get_host($uri);
	for($i=0; $i<count($links); $i++){
		if(get_host($links[$i])&&get_host($links[$i])!=$host){
			$oln++;
		}else{
			if($record_in_links){
				newurl(local_link($links[$i], $uri), $lvl++);
			}
		}
	}
	$time=time();
	console("recounted:$uri");
	my_up("link:out_links=$oln,recount_time=$time:uri='$uris'");
	recount_link_weight($id);
}

function local_link($link, $source_url){
		//cho htmlspecialchars($s)."@@@@@@@@@";
		$folder=get_url_folder($source_url);
		$host=get_host($source_url);
		$host=s2link::translit($host);
		if($host==""){
			return $link;
		}
		if(substr($link, 0, 1)=="/"){
			return "http://$host$link";
		}else if(!stripos($link, ":")){
			return "$folder$link";
		}else{
			return $link;
		}
}

function get_attr($tag, $attr){
	$s=tidy_repair_string($tag, array(), "UTF8");
	$s=rembody($s);
	$s=rxml($s);
	//cho h2s($s)."<br />";
	$s=cut_spaces($s);
	$s="<?xml version='1.0' encoding='UTF-8'?><root>$s</root>";
		//cho "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
	$xml = new DOMDocument;
	$xml->loadXML($s);
	//cho $xml->getElementsByTagName("body")->item(0)->getElementsByTagName("*")->item(0)->attributes[href];
	$attrs=get_attributes($xml->getElementsByTagName("root")->item(0)->childNodes->item(0));
	return $attrs[$attr];
	//return preg_match("/$attr=([\"']{0,1})(.*)([\"' >]{0,1})/", $tag);
};


function curdate(){
	return bdate(time());
}

function bdate($time){
	return date("Y-m-d-H-m-s",$time);
}

function get_day($date){
	$datear = explode("-", $date);
	return $datear[2];
}

function get_hour($date){
	$datear = explode("-", $date);
	return $datear[3];
}

function debug($s){
	if(vars::$debug){
		print $s;
	}else{
		//log_msg($msg)
	}
}

function include_abs($s){
	include get_full_path($s, 1);
}

function send_message_to_admin($message){
	$sendMsg = new sendMsg();
	$sendMsg->simpleSend('vineg@yandex.ru', '123455', 'vinegg@yandex.ru',$message);
}

function good_for_ad(){
	$pd = new proxy_detector();
	//echo get_user_country_code();
	$o = array(); // опции. необзятательно.
	$o['charset'] = 'utf-8'; // нужно указать требуемую кодировку, если она отличается от windows-1251
	
	//$geo = new Geo($o); // запускаем класс
	
	// этот метод позволяет получить все данные по ip в виде массива.
	// массив имеет ключи 'inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng'
	//$city = $geo->get_value('city');
	//echo $city;
	return (!($pd->detect())&&get_country_code_by_ip($_SERVER[REMOTE_ADDR])=="RU");
}

function referer_check($spam){
	if($_SERVER['HTTP_REFERER']=="http://megawall.ru/void"){
		def_page($spam);
		exit;
	}
}
?>