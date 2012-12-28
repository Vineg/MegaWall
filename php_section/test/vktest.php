<?php


/**
 API Class Vkontakte.ru
by Dostelon aka Rutrum
icq: 577366
http://devtown.ru/
**/



$api        = new VKapi($api_secret, $api_id);

print_r($api->getProfiles('2396708'));


exit;
$content=fr("test.jpg");
$api_id = 2379536; // ID вашего приложения
$api_key = 'ZxLKeT4Igb6Ucm4IITUp'; // Ключ приложения


//
//"http://api.vkontakte.ru/oauth/authorize?client_id=2379536&redirect_uri=http://megawall.ru/&display=popup&response_type=token"
$upload_server=request("https://api.vk.com/method/photos.getUploadServer?aid=147138059&access_token=b269c2f8fcb502d1b2223a03a3b25b118bbb27fb27f5e9b610ec8cef769d9f4");
$server=json_decode($upload_server)->response->upload_url;
$r=json_decode(request("$server", array(post_data=>array('file1'=>"@E://appserv/www/blog/test/test.jpg"))));
print_r($r);
print h2s(request("https://api.vk.com/method/photos.save?aid=147138059&server=$r->server&photos_list=$r->photos_list&hash=$r->hash&access_token=b269c2f8fcb502d1b2223a03a3b25b118bbb27fb27f5e9b610ec8cef769d9f4"));
exit;
//
//print adrreplace(request("http://vkontakte.ru/login.php?app=2379536&layout=popup&type=browser"),"http://vk.com/");
//exit;
$api_uid = 1481827; // ID пользователя приложения
//$sig=md5("$api_uid"."api_id=2379536method=getFriendsv=3.0ZxLKeT4Igb6Ucm4IITUp");
$VK = new vkapi($api_id, $api_key);

$at="b47b0e4cfaa7af00b430f6b721b449dd3fbb46db46d922fa435bed18c9468e9";

$resp = $VK->api('photos.getUploadServer', array('uids'=>'1481827', 'sid'=>'de55ed20bbcfac014ab39133404d182ea62375b0705d635ac96edf3ce631bb'));

print_r($resp);
exit;
//$content="-----------------------------491289511942 Content-Disposition: form-data; name=\"photo\"; filename=\"test.jpg\" Content-Type: image/jpeg\n\r".$content."\n\r-----------------------------491289511942--";

print request("http://api.vkontakte.ru/api.php?v=3.0&api_id=2379536&method=getProfiles&format=json&rnd=343&uids=100172&fields=photo%2Csex&sid=10180116c4fd93480439bca47d636d6dd75fac30b851d4312e82ec3523&sig=$sig", array(
cookies=>array(remixchk=>5,remixsid=>"$remixsid"),
post_data=>"$content"
));
exit;
print h2s(request("http://vkontakte.ru/al_wall.php",
array(params=>"act=post&al=1&facebook_export=&friends_only=&hash=250b48c7d8fc9e3c87&message=%D0%BA%D1%86%D1%84%D0%BA&note_title=&official=&status_export=&to_id=-32310096&type=own",method=>"post",
cookies=>array(remixchk=>5,remixsid=>"$remixsid"))));

//
//print h2s(request("http://vkontakte.ru/",
//array(method=>"POST",
//cookies=>array(remixchk=>5,remixsid=>"b17bde67a4413abeabd3853ad6f9893675ded35205138edc457a0d82735b"))));


 

/**
 * VKAPI class for vk.com social network
 *
 * @package server API methods
 * @link http://vk.com/developers.php
 * @autor Oleg Illarionov
 * @version 1.0
 */
 
// class vkapi {
// 	var $api_secret;
// 	var $app_id;
// 	var $api_url;
	
// 	function vkapi($app_id=false, $api_secret=false, $api_url = 'api.vk.com/api.php') {
// 		if(!$app_id){
// 			$app_id=vars::$vk_api_id;
// 		}
// 		if(!$api_secret){
// 			$api_secret = vars::$vk_secret_key;
// 		}
// 		$this->app_id = $app_id;
// 		$this->api_secret = $api_secret;
// 		if (!strstr($api_url, 'http://')) $api_url = 'http://'.$api_url;
// 		$this->api_url = $api_url;
// 	}
	
// 	function api($method,$params=false) {
// 		if (!$params) $params = array(); 
// 		$params['api_id'] = $this->app_id;
// 		$params['v'] = '3.0';
// 		$params['method'] = $method;
// 		$params['timestamp'] = time();
// 		$params['format'] = 'json';
// 		$params['random'] = rand(0,10000);
// 		ksort($params);
// 		$sig = '1481827';
// 		foreach($params as $k=>$v) {
// 			if($k!="sid"){
// 				$sig .= $k.'='.$v;
// 			}
// 		}
// 		$sig .= $this->api_secret;
// 		$params['sig'] = md5($sig);
// 		$query = $this->api_url.'?'.$this->params($params);
// 		print h2s($query);
// 		$res = request($query);
// 		return json_decode($res, true);
// 	}
	
// 	function params($params) {
// 		$pice = array();
// 		foreach($params as $k=>$v) {
// 			$pice[] = $k.'='.urlencode($v);
// 		}
// 		return implode('&',$pice);
// 	}
// }
?>
 