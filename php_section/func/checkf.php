<?php
session_start();
require_once 'phpscr/functions.php';
require_once 'phpscr/shortcuts.php';
$e=0;
if($_POST["captcha"]!=$_SESSION["captcha"]||$_POST["captcha"]==false){
	print "<br />Неправильно введён код.";$e=1;
}
ob_start();
$mail=$_POST['mail'];
$log=$_POST['log'];
$pass=$_POST['pass'];
$pass1=$_POST['pass1'];
$pass=hts($pass);
$pass1=hts($pass1);
if($log!=""&&$pass!=""&&$mail!=""&&$pass1!=""){
	require_once "phpscr/shortcuts.php";
	require_once("settings/mysql_connect.php");
	$log=hts($log);
	$mail=hts($mail);
	$logqry = my_q("select * from user where login='$log'");
	$shrqry = my_q("select * from shortcut where shortcut='$log'");
	if(my_n($logqry)==0&&mysql_num_rows($shrqry)==0){
		if (!preg_match("/^[a-zA-Z1-9]([\w\-]*[a-zA-Z1-9]+)*@([a-zA-Z1-9]([\w\-]*[a-zA-Z1-9]+)*\.)+[a-zA-Z]{2,4}$/",$mail)){print"<br />Неправильно введен e-mail."; $e=1;}else
		if (!preg_match("/^\w{4,25}$/",$log)){print"<br />Количество символов в логине не соответствует допустимому интервалу(от 4 до 25), или он содержит недопустимые символы."; $e=1;}else
		if (!preg_match("/^\w{6,25}$/",$pass)){print"<br />Количество символов в пароле не соответствует допустимому интервалу(от 6 до 25), или он содержит недопустимые символы."; $e=1;}else
		if ($pass!=$pass1){print("<br />Пароли не совпадают"); $e=1;};
	}else{
		print"<br />Пользователь с таким ником уже существует.";$e=1;
	}
}else{print "<br />Заполните все поля!";$e=1;}
if($e==0){
	print 1;
}
ob_end_flush();
?>