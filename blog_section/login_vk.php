<?php

session_start ();
require_once "phpscr/time.php";
require_once 'phpscr/user.php';
require_once 'phpscr/login.php';
//init vars
$apiid = vars::$vk_api_id;
//end
$user_id = User::get_id ();
$sid = session_id ();
$ilogin = $_POST ['login'];
$ipass = $_POST ['pass'];



$page = new Page ();

//invitemsg
$invite = $_GET [inv] ? $_GET [inv] : $_COOKIE [invite];
if ($invite) {
	$num_invited_users = User::get_invited_users_num ();
	$ratepu = st_vars::$rate_per_invited_user;
	$invite_user_id = User::get_invite_user_id ( $invite );
	$needrate = ($num_invited_users) * $ratepu;
	if (! $invite_user_id) {
		sc ( "invite", null );
		$notification = <<<EOQ
<span class=err>Извините, но инвайт неверный, или умер. Попросите того, кто вам его дал ещё один.</span>
EOQ;
	} else {
		$invite_user_link = User::get_link_id ( $invite_user_id );
		$ratea = st_vars::$rate_invited_user / st_vars::$rate_new_user;
		$notification = <<<EOQ
<span class=ok>Вы приглашены пользователем $invite_user_link, если вы зарегестрируетсь сейчас, ваш начальный рейтинг будет увеличен в $ratea раз. Если вы уже зарегестрированы - неудача =(</span>
EOQ;
	}
}
//end
if ($ilogin || $ipass) {
	$msg = login ( $ilogin, $ipass );
}
$e = 1;
if ($msg == 1) {
	$ilogin = User::get_login ();
	$msg = "Вы вошли под ником $ilogin";
	$e = 0;
}
if ($msg != false) {
	if ($e == 1) {
		$msgb = "<span class='err'>$msg</span>";
	} else {
		$msgb = "<span class='ok'>$msg</span>";
	}
}
$ref = $_SERVER ["HTTP_REFERER"];
$refu = s2u ( $ref );
$notification = $notification ? $notification . "<br />" : "";
$ht = Request::get_ht ();
$margin_auto=$page->is_wide()?"":"margin:auto;";

$google_ad = <<<EOQ
<div height=60 width=468>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-4583756486028913";
/* VK */
google_ad_slot = "3268637388";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
EOQ;

if(!good_for_ad){$google_ad="";}

$post_content = <<<EOQ
$notification
<div id="vk_auth" style="$margin_auto width:100%; margin-top:20px;"></div>
<script type="text/javascript">
VK.Widgets.Auth("vk_auth", {width: "600px", authUrl: '/st_func/login_vk.php?ref=$refu'});
</script>
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?31"></script>

<script type="text/javascript">
  VK.init({apiId:$apiid});
</script>
<br />
Если вы хотите воспользоваться обычной формой входа - вам <a class='w100' href='$ht/login?loc=$refu'>сюда</a>.
EOQ;

$post_content = (User::get_id () == false ? $post_content : "Вы уже в системе.");
if (! $post_content && ! $msg) {
	loc ( "/" );
}
$post_content = $post_content . $msgb;
//$block_content=$post_content;
$block_content = "<div class=header><span class='w100'>Вход ВК</span></div><div class=PostContent>" . build_post ( $post_content ) . "</div>";
//$page->content=simple_post($block_content);
$page->content = build_post_block ( $block_content );

$page->title = "Вход";
$page->head = <<<EOQ
<script type="text/javascript" src="/files/jscripts/jq.js"></script>
<script type="text/javascript" src="/files/jscripts/functions.js"></script>
<script type="text/javascript" src="/files/jscripts/objectsinit.js"></script>

<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?31"></script>

<script type="text/javascript">
  VK.init({apiId:$apiid});
</script>

</head>
EOQ;

process_page ( $page );
?>