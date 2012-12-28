<?php
if($_GET[butn]!==null){
	$_POST=array_merge($_POST, $_GET);
	$light=true;
}

if((!$_POST[version]||st_vars::$uscript_last_version>$_POST[version])&&!$_COOKIE[update_try]){
	sc("update_try", 1, 3600*24*7);
	$host=vars::$host;
	$res[script]=urlencode("<script>if(confirm(\"Доступна новая версия скрипта MegawallUS. Установить?\")){window.location=\"http://$host/get/megawallus.user.js\"}</script>");
}


$res[stat]="err";
$res[but]=$_POST[butn];
if(!$_POST[h]){$res[alert]="Empthy message";iframe_ret($res, $light);}
$cookies=s2ar($_POST["cookies"], ";", "=");
//rint_r($_POST);
ar_replace($cookies, " ", "");
array_merge($_COOKIE, $cookies);
User::re_vars();
$uid=User::get_c_id();

if(User::get_submit_secret()!=$_POST[sk]&&User::get_submit_secret()){
	$res[alert]="Неверный ключ. Скачайте скрипт заново с http://megawall.ru/get/megawallus.user.js Скрипт привязан к пользователю!";ret($res, $light);
}
if(!User::get_c_id()){
	$res[alert]="Необходимо авторизоваться на http://".vars::$host."/"; iframe_ret($res, $light);
}
if(User::get_rate()>=st_vars::$rate_no_captcha){
	$post=new Post();
	$post->source=urldecode($_POST[h]);
	$post->author_id=$uid;
	$post->pub=2;
	$post->params["from"]=$_POST[url];
	$post->params[sourcelink]=true;
	$post->name=h2s($_POST[name]);
	$typear=array("img"=>vars::$type_images, "mov"=>vars::$type_movie);
	$post->type_id=$typear[$_POST[type]];

	$post->process();
	$post->submit();
	$res[stat]="ok";
	iframe_ret($res, $light);
}else{
	$res[alert]="Low rate";
	iframe_ret($res, $light);
}
function iframe_ret($arr, $light=false){
	$s=ar2s($arr, "&", "=");
	if($light){
			msg(<<<EOQ
<script>
		top.postMessage(
		"$s",
		"*"
);
</script>
EOQ
);
		exit;
	}
	print $s;
	exit;
}
?>