<?php
print "<head><meta name='robots' content='noindex'/></head><body>";
require_once 'phpscr/reg.php';
require_once 'phpscr/login.php';
$app_id=vars::$vk_api_id;
$user_id=h2i($_GET[uid]);


if(md5($app_id.$user_id.vars::$vk_secret_key)==$_GET[hash]){
	$uid=my_fst("select user_id from vk_user where vk_id=$user_id", "user_id");
	if(!$uid){
		reg_vk_user($_GET);
	}else{
		login_fast($uid);
	}
}else{
	print("wrong hash");
	exit;
}

print "</body>";
if($_GET[ref]){
	lloc($_GET[ref]);
}else{
	loc_back();
}
?>