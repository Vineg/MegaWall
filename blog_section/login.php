<?php

session_start();
require_once "phpscr/time.php";
require_once 'phpscr/user.php';
require_once 'phpscr/login.php';
$user_id=User::get_id();
$sid=session_id();
$ilogin=$_POST['login'];
$ipass=$_POST['pass'];

$page=new Page();

if($ilogin||$ipass){
	$msg=login($ilogin, $ipass);
}
$e=1;
if($msg==1){
	$ilogin=User::get_login();
	//$msg="Вы вошли под ником $ilogin";
	$e=0;
}
if($msg!=false){
	if($e==1){
		$msgb="<span class='err'>$msg</span>";
	}else{
		$msgb="<span class='ok'>$msg</span>";
	}
}
$lloc=$_GET[loc];
$llocu=urlencode($lloc);
$ht=Request::get_ht();
$post_content=<<<EOQ
<form action=login?loc=$llocu method=post user_id=form>
		<table class="invis solid">
			<tbody style="position:relative;">
				<tr>
					<td>Ник:
					<input type='text' name='login' user_id='login' value=$ilogin></td>
					<td>Пароль: <input type='password' name='pass' user_id='pass'></td>
				</tr>
				<tr>
					<td><button type='submit' class="sub_button">Отправить</button></td>
				</tr>
			</tbody>
		</table>
		$msgb
</form>
В первый раз видите эту страничку? Воспользуйтесь более удобной <a href="$ht/login_vk">формой</a>, или <a href="$ht/reg">зарегестрируйтесь</a>.
EOQ;
$post_content=(User::get_id()==false?$post_content:"");
if(!$post_content){
	if($lloc){
		loc($lloc);
	}else{
		loc_back();
	}
}
$post_content=$post_content;
$ht=Request::get_ht();
$block_content="<div class=header><span href=$ht/reg>Вход</a></div><div class=PostContent>".build_post($post_content)."</div>";
$page->content=build_post_block($block_content);

$page->title="Вход";

$page->head=<<<EOQ
<script type="text/javascript" src="/files/jscripts/jq.js"></script>
<script type="text/javascript" src="/files/jscripts/functions.js"></script>
<script type="text/javascript" src="/files/jscripts/objectsinit.js"></script>
EOQ;

process_page($page);
?>