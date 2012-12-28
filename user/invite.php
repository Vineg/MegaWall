<?php
$user_id=User::get_c_id();
if(!$user_id){l404();}
$num_invited_users=User::get_invited_users_num($user_id);
$ratepu=st_vars::$rate_per_invited_user;
$needrate=(1+$num_invited_users)*$ratepu;
$user_rate=User::get_rate();
if($user_id&&$user_rate>=$needrate){
	$inv=md5(rand(0, 10000000000)."fadg");
	if(my_qn("select * from invite where user_id=$user_id")>=100){
		my_q("delete from invite where user_id=$user_id");
	}
	my_q("insert into invite(invite, user_id) values('$inv', '$user_id')");
	msg(Request::get_ht()."/login_vk?inv=".$inv);
}else{
	msg("Чтобы пригласить следующего пользователя нужен рейтинг больше $needrate, ваш текущий: $user_rate");
}