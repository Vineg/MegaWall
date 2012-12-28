<?php
$page=new Page();

$vk_uid=User::get_vk_id();
$ht=Request::get_ht();

if(!$vk_uid){
	$post_content="Войдите на сайт через <a href='$ht/login_vk'>форму вконтакте</a>.";
	$e=1;
}

if($_POST[email]&&$_POST[pass]){
	$email=my_s($_POST[email]);
	$pass=my_s($_POST[pass]);
	$key=vars::$vk_secret_key;
	my_q("update vk_user set email=encode('$email', '$key'),pass=encode('$pass', '$key') where vk_id=$vk_uid");
}
if(!$e){
	if(!User::get_vk_email()||!User::get_vk_pass()){
		$post_content=<<<EOQ
		<form action='' method=POST class='olist'>
			<h3><span><label><input type=checkbox class=open />Я доверяю этому сайту и готов отдать свой логин и пароль от "Вконтакте" для импорта списка друзей.</span></label></h3>
			<ul class="openobj hidden">
				<li><table><tbody>
					<tr><td>Логин или e-mail от сайта vkontakte</td><td> <input name=email placeholder=E-mail /></td></tr>
					<tr><td>Пароль от сайта vkontakte</td><td> <input type=password name=pass placeholder=password /></td></tr>
					<tr><td colspan=2><button>Отправить</button></td></tr>
					</tbody></table></li>
			</ul>
		</form>
EOQ;
	}else{
	
		$q=my_q("select * from vk_user where vk_id=$vk_uid AND last_vk_friends_list_update<".(time()-7*24*60*60)."");
		for($i=0; $i<my_n($q); $i++){
			update_vk_user_friends_list(my_r($q, $i, "vk_id"));
		}
	
		$liststr=my_fst("select decode(list, '".vars::$vk_secret_key."') as list from vk_friends_list where vk_user_id=$vk_uid order by date desc", "list");
		if($liststr){
			$listar=explode(" ", $liststr);
		}else{
			$listar=array();
		}
	
		for($i=0; $i<count($listar); $i++){
			$usr=my_q("select * from vk_user where vk_id=$listar[$i]");
			$photo=my_fst($usr, "photo_rec");
			$fn=my_fst($usr, "first_name");
			$sn=my_fst($usr, "last_name");
			$id=my_fst($usr, "vk_id");
			$lis.="<li><img width=100px height=100px src=$photo><a href='http://vk.com/id$id' style='vertical-align:top;'>$fn $sn</a></li>";
		}
		if($lis){
			$post_content="<ul>$lis</ul>";
		}else{
			$email=User::get_vk_email();
			$post_content=<<<EOQ
			<span class=err>Не удалось импортировать список друзей.</span><br />
			<form action='' method=POST class='olist'>
			<a class=open>Ввести данные заново</a>
			<ul class="openobj hidden">
				<li><table><tbody>
					<tr><td>Логин или e-mail от сайта vkontakte</td><td> <input value="$email" name=email placeholder=E-mail /></td></tr>
					<tr><td>Пароль от сайта vkontakte</td><td> <input name=pass placeholder=password /></td></tr>
					<tr><td colspan=2><button>Отправить</button></td></tr>
					</tbody></table></li>
			</ul>
		</form>
EOQ;
		}
	}
}
$page->content.=simple_post("$post_content");
process_page($page);
?>