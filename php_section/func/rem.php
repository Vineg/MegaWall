<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path/settings/mysql_connect.php";
require_once "$path/phpscr/user.php";
require_once "$path/sys/vars.php";
require_once "$path/phpscr/shortcuts.php";

//cho $_POST["pid"];
$id=h2s($_POST["id"]);
$from=$_POST["from"];
if($from=='post'){
	$id=pid2id($id);
}else{
	$id=h2i($id);
}
$rate_delete=st_vars::$rate_delete;
$user_rate=User::get_rate();
if($from=="post"){
	//cho "user_id:$user_id;post_id:$post_id;";
	$user_id=User::get_id();
	$author_id=get_post_author($id);
	$id=h2s($id);
	//$rem=h2i($_POST["rem"], 0, 3);
	$rem=3;
	//cho("select * from $from where id='$id' and pub>(1-$rem)");
	$np=my_qn("select * from $from where id='$id' and pub>(1-$rem)");
	if($np>0){
		if($user_id==$author_id){$pub=min(max(-3, 1-$rem), 0);}else if($user_rate>=st_vars::$rate_delete){$pub=min(max(-2, 1-$rem), 0);}else{print "Низкий рейтинг"; exit;}
		$add=(log($user_rate+1)+0.1)*0.01*$a;
		$uadd=(log($user_rate+1)+0.1)*0.0001;
		mysql_query("update post set pub=$pub where id='$id'");
		print "1";
	}else{print "(уже)Нет такого поста";}
}else if($from=="notifications"){
	$from=h2s($from);
	$uid=User::get_c_id();
	$np=my_n(my_q("select * from $from where post_id='$id' and user_id=$uid"));
	if($np>0){
		mysql_query("delete from $from where post_id='$id' and user_id=$uid");
		print "1";
	}else{print "(уже)Нет такого объекта";}
}else{
	if($user_rate>=$rate_delete){
		$alfrom=array("content", "type");
		$from=h2s($from);
		if($from=="post"){
			my_q("delete from stack_post where post_id = $id");
		}
		if(!contains($from, $alfrom)){print "Ошибка";};
		$np=my_n(my_q("select * from $from where id='$id'"));
		if($np>0){
			my_q("delete from $from where id='$id'");
			print "1";
		}else{print "(уже)Нет такого объекта";}
	}else{print "Низкий рейтинг";}
}

?>