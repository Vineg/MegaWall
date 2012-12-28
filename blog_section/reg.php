<?php
session_start();
require_once "phpscr/post.php";
require_once "phpscr/shortcuts.php";
require_once "settings/mysql_connect.php";
require_once "phpscr/time.php";
require_once 'phpscr/login.php';
require_once 'phpscr/functions.php';

ob_start();
//init vars
$page=new Page();
//end
$mail=$_POST['mail'];
$log=$_POST['log'];
$ipass=$_POST['pass'];
$pass1=$_POST['pass1'];
$invite=$_GET[inv]?$_GET[inv]:$_COOKIE[invite];
if($invite){
	$invite_user_id=User::get_invite_user_id($invite);
	$needrate=($num_invited_users)*$ratepu;
	if(!$invite_user_id){
		sc("invite", null);
		$notification=
<<<EOQ
<span class=err>Извините, но инвайт неверный, или умер. Попросите того, кто вам его дал ещё один.</span><br /><br />
EOQ;
	}else{
		$invite_user_link=User::get_link_id($invite_user_id);
		$ratea=st_vars::$rate_invited_user/st_vars::$rate_new_user;
		$notification=<<<EOQ
<span class=ok>Вы приглашены пользователем $invite_user_link, если вы зарегестрируетсь сейчас, ваш начальный рейтинг будет увеличен в $ratea раз.</span><br /><br />
EOQ;
	}
}

if($log!=""&&$ipass!=""&&$mail!=""&&$pass1!=""){
	require_once 'phpscr/reg.php';
	if($_POST["captcha"]!=$_SESSION["captcha"]||$_POST["captcha"]===null){
		$msg= "<br />Неправильно введён код.";$e=1;
	}
	$log=hts($log);
	//$ipass=hts($ipass);
	$mail=hts($mail);
	$pass1=hts($pass1);
	$minlog=st_vars::$minlog;
	$maxlog=st_vars::$maxlog;
	$minpass=st_vars::$minpass;
	$maxpass=st_vars::$maxpass;
	if (!preg_match("/^[a-zA-Z1-9]([\w\-]*[a-zA-Z1-9]+)*@([a-zA-Z1-9]([\w\-]*[a-zA-Z1-9]+)*\.)+[a-zA-Z]{2,4}$/",$mail)){$msg="Не верно введен e-mail."; $e=1;}else
	if (!preg_match("/^\w{".$minlog.",".$maxlog."}$/",$log)){$msg= "Количество символов в логине не соответствует допустимому интервалу(от 4 до 25), или он содержит недопустимые символы."; $e=1;}else
	if (!preg_match("/^.{".$minpass.",".$maxpass."}$/",$ipass)){$msg="Количество символов в пероле не соответствует допустимому интервалу(от 6 до 25), или он содержит недопустимые символы."; $e=1;}else
	if ($ipass!=$pass1){$msg=("Пароли не совпадают"); $e=1;};

	if($e==0){
		$reg=reg($log, $ipass, $mail);
		if($reg===true){
			$log=User::get_login();
			$msg="Добро пожаловать, <b>$log</b>! Поздравляем с успешной регистрацией. <br />";
		}else{
			$e=1;
			$msg=$reg;
		}
	}
}


$ul=User::get_u_link();
if(User::get_id()){if($msg==""){$reg_form="Вы уже зарегестрированы под ником $ul";}
}else{$reg_form=<<<EOQ
	<form class=regForm action=reg method=post>
		<table class="tablesyssoft solid">
			<tbody style="position:relative;">
				<tr>
					<td>E-mail:</td>
					<td><input type='text' name='mail' id='mail'
						value="$mail" /></td>
				</tr>
				<tr>
					<td>Ник:</td>
					<td><input type='text' name='log' id='log' value="$log" /></td>
				</tr>
				<tr>
					<td>Пароль:</td>
					<td><input type='password' name='pass' id='pass' /></td>
				</tr>
				<tr>
					<td>Повтор пароля:</td>
					<td><input type='password' name='pass1' id='pass1' /></td>
				</tr>
			</tbody>
		</table>
		<br />
		<img src="$ht/files/captcha.php" class="comp captcha" />
		<a class="reloadCaptcha right">Обновить картинку</a><br />
		<input name=captcha class=code placeholder="Текст с картинки" />
		<br />
		<input type='submit' class="sub_button" value='Отправить' /><br />
		<span class="err hidden"></span>
	</form>
EOQ;
}

if($msg){
	$msg=($e==1)?"<span class='err'>$msg</span>":"<span class='ok'>$msg</span>";
}
$post_content=<<<EOQ
<div class='PostContent NoHeader'>
$notification
$reg_form
$msg
</div>
EOQ;

$block_content=build_post($post_content);
$params[type]="sys";
$page->content=build_post_block($block_content, $params);


$page->title="Регистрация";
$sv=st_vars::$script_version;
$page->head=<<<EOQ


<script type="text/javascript" src="$ht/files/jscripts/objectsinit.js$sv"></script>
<script type="text/javascript" src="$ht/files/jscripts/reg.js?$sv"></script>
EOQ;
process_page($page);
?>