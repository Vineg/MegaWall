<?php
function new_vk_user(Array $user){
	$user=my_s($user);
	if(!my_qn("select * from vk_user where vk_id=$user[id]")){
		my_in("vk_user:first_name, last_name, photo_rec, photo, vk_id:$user[first_name],$user[last_name],$user[photo_rec],$user[photo],$user[id]");
	}
}