<?php
if($_POST[user_page]){
	require_once 'settings/mysql_connect.php';
	require_once 'phpscr/user.php';
	$s=$_POST[user_page];
	$s=ecran($s, array("{","}", "$"));
	$newpage=Post::process_post_text($_POST[user_page], array(nohide=>1,nocheckuri=>1));
	$s=urldecode($s);
	//cho h2s($s);
	$cid=User::get_id();
	$newpage=my_s($newpage);
	my_q("update user set user_page='$newpage' where id='$cid'");
	loc(User::get_url());
}
if(contains($_GET[template], st_vars::$templates_ar)){
	$_SESSION[template]=h2s($_GET[template]);
}
loc_back();
?>