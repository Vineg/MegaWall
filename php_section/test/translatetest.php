	

This code worked for me -- on the iPhone web browser Safari and as an added bonus it even worked with FireFox 3.5 on my laptop! The Geolocation API Specification is part of the W3 Consortium’s standards But be warned: it hasn’t been finalized as yet.

alt text alt text

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Geolocation API Demo</title>
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport"/>
<script>
function successHandler(location) {
    var message = document.getElementById("message"), html = [];
    html.push("<img width='256' height='256' src='http://maps.google.com/maps/api/staticmap?center=", location.coords.latitude, ",", location.coords.longitude, "&markers=size:small|color:blue|", location.coords.latitude, ",", location.coords.longitude, "&zoom=14&size=256x256&sensor=false' />");
    html.push("<p>Longitude: ", location.coords.longitude, "</p>");
    html.push("<p>Latitude: ", location.coords.latitude, "</p>");
    html.push("<p>Accuracy: ", location.coords.accuracy, " meters</p>");
    message.innerHTML = html.join("");
}
function errorHandler(error) {
    alert('Attempt to get location failed: ' + error.message);
}
navigator.geolocation.getCurrentPosition(successHandler, errorHandler);
</script>
</head>
<body>
<div id="message">Location unknown</div>
</body>
</html>


<?php

//encoding_fix();
//cho translate2("Steve Dodd (born 1928) is an Indigenous Australian actor, notable for playing indigenous characters across seven decades of Australian film. After beginning his working life as a stockman and rodeo rider, Dodd was given his first film roles by prominent Australian actor Chips Rafferty. His career was interrupted by six years in the Australian Army during the Korean War, and limited by discrimination and typecasting. Despite this, by 1985 he had appeared in 55 movies or television features. Dodd has performed in some of Australia's most prominent movies, including Gallipoli and The Chant of Jimmie Blacksmith, in which he played Tabidgi, the murdering uncle of the lead character. He has also held minor parts in Australia-based international film productions including The Coca-Cola Kid, Quigley Down Under and The Matrix. He has appeared in minor roles in early Australian television series, such as Homicide and Rush, as well as more recent series including The Flying Doctors.");
exit;
//cho translate("We're lending, investing and giving to help spur economic growth in local communities. In 2011, we extended approximately $557 billion in credit to consumers, small businesses, large companies, non-profits and others, to help strengthen communities across the country.");
function translate($s){
	$sar = explode(".",$s);
	foreach ($sar as $value){
		sleep(0.5);
		$s = s2u($value);
		$res = request("http://translate.google.ru/translate_a/t?client=x&text=$s&sl=english&tl=russian");
		$ar = json_decode($res);
		$resar[]=$ar->sentences[0]->trans;
	}
	return implode($resar, ". ");
}

function translate2($s){
	$ar = explode(".", $s);
	$ar = rem_empthy_values($ar);
	for($i=0; $i<count($ar); $i+=3){
		$cs = join(".",array_slice($ar, $i, 3)).".";
		sleep(0.5);
		$res.=request("http://ets6.freetranslation.com/", array(post_data=>array(sequence=>"core", srctext=>$cs, language=>"English/Russian")));
	}
	return $res;
}
exit;
// $pattern = "#^((?!qwe).)*$#";
// //cho preg_match($pattern, "ereewrwq");
// //cho preg_match($pattern, "qqweq");
// //print_r($matches);
// //cho preg_match($pattern, "qwe");
// $max_user_id = 1000000;
// //cho h2s(print_r(get_random_page_info($max_user_id), true));
// $sock = fsockopen("localhost", "8082");
// fwrite($sock, "test");
$js=array(message=>"{!}return 'foo'+2;");
//cho h2s(request("http://localhost:8080/SimpleStreamServ/chat",array(post_data=>$js)));
exit;
add_pseudo_user(1);
exit;
$api = new VKapi();
$ar = $api->getProfiles("id1");
print_r($ar);
exit;
add_pseudo_user();
$sex = rand(1, 2);
//cho print_r(random_search_vk_ids($sex));
exit;

// function search_vk(){
// $api = new vkapi("ZxLKeT4Igb6Ucm4IITUp", 2379536);
// return $api->request(array(method=>"users.search", pq=>"Виноходов Егор"));
// }

add_pseudo_user();
exit;
print_r(generate_random_page_info());
exit;

for($i=0; $i<1; $i++){
	$sname = get_random_surname($max_user_id);
	$name = get_random_name($max_user_id, $sname[1]);
	//cho "$sname[0] $name[0]<br />";
}

function rem_html($s){
	return strip_tags($s);
}


// function get_random_full_name($max_user_id){
// 	sleep(1);
// 	$uid = rand(0, $max_user_id);
// 	$s = get_page($uid);
// 	$sex = get_sex($s);
// 	//$s = "<title>Павел Дуров</title>";
// 	preg_match_all("#<title>([а-яА-ЯёЁ]+ [а-яА-ЯёЁ]+)</title>#u", $s, $matches);
// 	$full_name = $matches[1][0];
// 	$res = array($full_name, $sex);
// 	return $res;
// }

exit;
$text="olol o lolol http://vk.com/|q oklkl l;l;l;lhttp://ya.ru/ qwe 111";
preg_match_all("#((https?|ftp)://[\w\-.]+\.[\w]+[/\w\?&%]+)(\|([\w]+))?#", $text, $matches);
//print_r($matches);
$links = $matches[1];
$newlink;
foreach ($links as $link){
	if(get_host($link)!=$_SERVER[HTTP_HOST]){
		$id = newurl($link);
		$newlink[$link] = "http://".$_SERVER[HTTP_HOST]."/loc/$id";
	}
}
//print_r($newlink);
$s = preg_replace(
"#((https?|ftp)://[\w\-.]+\.[\w]+[/\w\?&%]+)(\|([\w]+))?#e",
"\"<a href='\".\$newlink['\\1'].\"'>\".('\\4'?'\\4':shorten_string('\\1')).\"</a>\""
,$text);
//cho $s;
exit;
$s = preg_replace("#((https?|ftp)://[\w\-.]+\.[\w]+[/\w\?&%]+)(\|([\w]+))?#","<a href=\\1>\\4</a>",'http://vk.com/qwe|qwe');
//cho $s;
exit;
//cho test();
function test(){
	$res = 0;
	include "../php_section/test/includetest.php";
	return $res;
}
exit;
//recount_link_weight("http://megawall.ru/");
//recount_link_weight($uri_id)
//print_r(array_slice(array(1,2,3), 0, 2));
//cho get_full_path("req.log");exit;
//cho "http://d1.megawall.ru/files/update/",User::get_login(),"/",User::get_upload_secret(),"/";
exit;
//cho base_convert(math.pow(36, 11), 10, 36);
exit;
//cho preg_match("/^ru\.megawall\.java\.[a-z\.]*$/", "ru.megawall.java.test");
exit;
if(($id=false)){
	//cho 1;
}
exit;
$info="[a]
b=c
[d]
e
f";

print_r($res);


$headers_name = $headers_name_ar[1];

print_r($matches);
exit;



$zip = new ZipArchive;
if ($_FILES["file"]["error"] > 0)
  {
  //cho "Error: " . $_FILES["file"]["error"] . "<br />";
  }
else
  {
  //cho "Upload: " . $_FILES["file"]["name"] . "<br />";
  //cho "Type: " . $_FILES["file"]["type"] . "<br />";
  //cho "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
  //cho "Stored in: " . $_FILES["file"]["tmp_name"];
  }
move_uploaded_file($_FILES["file"]["tmp_name"], "tmp.zip");
if ($zip->open('tmp.zip') === TRUE) {
    //cho $zip->getFromName('.mwinfo');
    $zip->close();
} else {
    //cho 'failed';
}
exit;
for($y=0; $y<1000; $y++){
	for($i=0; $i<12; $i++){
		$s.=rand(0, 1);	
	}
	$c+=stripos($s, "11111")!=false;
	$s="";
}
print $c;
exit;
exit;
Host::index(Host::get_id("d.megawall.ru"));
exit;
// ini_set("max_execution_time", 600);
// $q=my_q("select * from link");
// for($i=0; $i<my_n($q); $i++){
// 	$id=my_r($q, $i, "id");
// 	$text=my_s(my_r($q, $i, "uri"));
// 	my_in("link_text:text=$text,link_id=$id");
// }
// exit;
// Host::reset_ya_page(1);
// ya_index_next(1);
// exit;
ini_set("max_execution_time", 1);
print update_host_script ( "d.megawall.ru" );
function update_host_script($host) {
	$id = Host::get_id ( $host );
	$r = my_q ( "select * from host where id=$id" );
	$code_uri = my_fst ( $r, "code_uri" );
	if (! $code_uri) {
		$code_uri = "http://" . Host::get_host ( $id ) . "/";
	}
	
	$mw_key = User::get_hash ( Host::get_author_id ( $id ) );
	//get_request("http://$host/", "", array());
	$rand = rand ( 1, 1000000 );
	$s = request ( $code_uri, array (cookies => array (mw_key => $mw_key, mw_random => $rand, mw_update_script=>1 ) ) );
	print "|$s|";
	$resar = mark_out ( $s, "<mw_responce_$rand>", "</mw_responce_$rand>" );
	return $resar [0] == "ok" ? true : false;
}
exit;
ini_set ( "max_execution_time", 20 );
$q = my_q ( "select * from link where weight=0" );
for($i = 0; $i < my_n ( $q ); $i ++) {
	$uri = my_r ( $q, $i, "uri" );
	recount_link_weight ( $uri );
}
exit ();
newurl ( $uri, $lvl );
// update_all_hosts();
// exit;
// include('msn/sendMsg/sendMsg.php');


// $sendMsg = new sendMsg();


// for($i=0; $i<10; $i++){
// 	$sendMsg->simpleSend('vinegg@yandex.ru', '123455', 'vineg@yandex.ru',$i);


// 	print $sendMsg->result.'q'.$sendMsg->error;
// }
// exit;
//$remixsid=login_vk("vineg@yandex.ru", "Prosbanebespokoitsru");
//print $remixsid;exit;
//print sendMessage(curl_init(), array(remixsid=>$remixsid), 1481827);


//exit;
//$q=my_q("select * from tolink;");
// for($i=0;$i<min(my_n($q), 3); $i++){
// 	$id=my_r($q, $i,"id");
// 	print check_tolink($id)?"1":"0";
// }
// exit;


// update_all_hosts();
// exit;
// update_all_host_counters();
// exit;
// print check_tolink(1);
// exit;
// print ServerLoad();
// function ServerLoad()
// {


// 	$stats = exec('uptime');


// 	preg_match('/averages?: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/', $stats, $regs);


// 	return ($regs[1].', '.$regs[2].', '.$regs[3]);


// }


// exit;


for($i = 4; $i < 100; $i ++) {
	newsite ( "http://q$i" . "w.com/", rand ( 1, 10 ) );
}
exit ();

for($i = 0; $i < 1000; $i ++) {
	print ("127.0.0.1		q$i" . "w.com<br />") ;
}
exit ();
get_local_links ( "http://vineg.org.ua/login_vk" );
exit ();
print h2s ( request_proxy ( "http://yandex.ru/yandsearch?text=php+pfuhepbnm+uri&lr=2", 10 ) );
exit ();

// print strpos(upload(
// "http://yandex.ru/yandsearch?date=&text=&site=megawall.ru%2Ftest&rstr=&zone=all&wordforms=all&lang=all&within=0&from_day=&from_month=&from_year=&to_day=&to_month=&to_year=&mime=all&numdoc=10&lr=2"
// ), "href=\"http://megawall.ru/test\"");
//print get_ya_index("http://megawall.ru/test");


//print User::get_login().md5(User::get_login().vars::$secret);


//$mdate=strtotime(get_svar(birthday));


/* $mdate=strtotime("06-04-1994");
 for($i=time(); $i>$mdate; $i-=60*60*24){
$cal[date("Y", $i)][date("M", $i)][date("d", $i)]=null;
}

print_r($cal);



exit;
print da;
exit; */

/*
 print h2s(preg_replace("/#a#(.+)#\/a#/", "<a href=test>\\1</a>", "qwe #a#qwe#/a#qwe"));
exit;
*/
//update_yandex_index("megawall.ru");


print "<meta name='charset' value='windows-1251'>";
$file = "$this->mw_user/$this->host.mwlinks.db";
$fileh = fopen ( $file, "a+" );
$res = fread ( $fileh, filesize ( $file ) );
print mb_convert_encoding ( "тест", 'windows-1251', "utf8" );
exit ();

Type::$table = "";
print Type::get_pub ( 1 );
exit ();
print shorten_string ( "qwertyuiopasdfghjklzxcvbnm", 10 );
exit ();
ini_set ( "max_execution_time", "1000" );
update_all_hosts ();
exit ();
Host::recount_olinks ( 1 );
exit ();
print curdate ();
exit ();
print check_code ( "http://megawall.ru/" );
exit ();
print get_host ( "http://megawall.ru/qwe", true );
exit ();
index_site ( "megawall.ru" );
update_yandex_index ( "megawall.ru", 0, 100 );
exit ();
$mw_user = "Vinegbdf254ce2b7fcdb6bdc4386fe8c6587c";

exit ();

ini_set ( "max_execution_time", "1000" );
//print_r(get_local_links("http://megawall.ru/archive/2011/August/06"));
//newsite("http://megawall.ru/");
index_site ( "localhost" );
exit ();

newurl ( "http://blog.ru/", "1" );

exit ();

//print get_attr("<a href=test>q</a>", "href");
//print local_link("/", "http://megawall.ru/");
load_file ( $url );
function update_all_hosts() {
	$r = my_q ( "select * from host where 1" );
	for($i = 0; $i < my_n ( $r ); $i ++) {
		$id = my_r ( $r, $i, "id" );
		update_host ( $id );
	}
}

function request_proxy($uri, $host_id) {
	$r = my_q ( "select code_uri from host where id=$host_id" );
	$code_uri = my_fst ( $r, "code_uri" );
	if (! $code_uri) {
		$code_uri = "http://" . Host::get_host ( $host_id ) . "/";
	}
	
	
	
	$mw_key = User::get_hash ( Host::get_author_id ( $host_id ) );
	//get_request("http://$host/", "", array());
	$linkproj_secret = User::get_linkproj_secret ( Host::get_author_id ( $host_id ) );
	$rand = rand ( 1, 1000000 );
	$res = request ( $code_uri, array (cookies => array (mw_key => $mw_key, mw_secret => $linkproj_secret, request_proxy => $uri, mw_rand => $rand ) ) );
	$res = mark_out ( $res, "<mw_responce_$rand>", "</mw_responce>" );
	$res = $res [0];
	return $res;
}

function update_all_host_counters() {
	$hosts = my_q ( "select * from host" );
	for($i = 0; $i < my_n ( $hosts ); $i ++) {
		$tolink_req = Host::$tolink_req;
		$id = my_r ( $hosts, $i, "id" );
		$ol = my_qn ( "select * from $tolink_req where donor_host_id=$id" );
		my_up ( "host:out_links_count=$ol:id=$id" );
		$il = my_qn ( "select * from $tolink_req where acceptor_host_id=$id" );
		my_up ( "host:in_links_count=$il:id=$id" );
		$l = my_qn ( "select * from link where host_id=$id" );
		my_up ( "host:max_out_links_count=$l:id=$id" );
	}
}

function send_post_data($ch, $url, $postdata = false, $ip = null, $timeout = 10) {
	//set various curl options first
	

	// set url to post to
	curl_setopt ( $ch, CURLOPT_URL, $url );
	
	// return into a variable rather than displaying it
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	
	//bind to specific ip address if it is sent trough arguments
	if ($ip) {
		curl_setopt ( $ch, CURLOPT_INTERFACE, $ip );
	}
	
	//set curl function timeout to $timeout
	curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
	
	if ($postdata) {
		
		//set method to post
		curl_setopt ( $ch, CURLOPT_POST, true );
		
		//generate post string
		$post_array = array ();
		if (is_array ( $postdata )) {
			foreach ( $postdata as $key => $value ) {
				$post_array [] = urlencode ( $key ) . "=" . urlencode ( $value );
			}
			
			$post_string = implode ( "&", $post_array );
		
		} else {
			$post_string = $postdata;
		}
		
		// set post string
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_string );
	
	}
	
	//and finally send curl request
	$result = curl_exec ( $ch );
	
	if (curl_errno ( $ch )) {
		print "Error Occured in Curln";
		print "Error number: " . curl_errno ( $ch ) . "n";
		print "Error message: " . curl_error ( $ch ) . "n";
		return false;
	} else {
		return $result;
	}
}

global $hashes;

$hashes = array ();
function decoded_hashes($hash) {
	$r = '';
	for($i = 0; $i < strlen ( $hash ); ++ $i)
		$r .= $hash [strlen ( $hash ) - $i - 1];
	$r = substr ( $r, 8, 13 ) . substr ( $r, 0, 5 );
	return $r;
}

function dec_hash($hash) {
	global $hashes;
	$hashes [$hash] = decoded_hashes ( substr ( $hash, 0, strlen ( $hash ) - 5 ) + substr ( $hash, 4, strlen ( $hash ) - 12 ) );
}

function decodehash($hash) {
	global $hashes;
	dec_hash ( $hash );
	return $hashes [$hash];
}

function sendMessage($ch, $cookie, $id, $message = 'Сообщение', $title = 'Тема') {
	curl_setopt ( $ch, CURLOPT_COOKIE, $cookie );
	
	$arr = array ('act' => 'write_box', 'al' => 1, 'to' => $id );
	
	$content = send_post_data ( $ch, 'http://vkontakte.ru/al_mail.php', $arr );
	
	$hash = substr ( $content, strpos ( $content, 'cur.decodehash(' ) + 16, strlen ( 'c57c4696ab919b952a0aab60c' ) );
	
	$post = array ('act' => 'a_send', 'ajax' => '1', 'al' => '1', 'chas' => decoded_hashes ( $hash ), 'from' => 'box', 'message' => $message, 'title' => $title, 'to_id' => $id );
	
	$content = send_post_data ( $ch, 'http://vkontakte.ru/al_mail.php', $post );
	
	return $content;
}
?>