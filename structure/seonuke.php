<?php
function process_request() {
	include "structure/core.php";
	$projfold = "proj/mysites";
	$core = new Include_core ( $projfold );
	$core->defphpfolders = array ("st_func", "func", "ofunc" );
	//$core->fileindex = User::get_id()?"./index":"./blog/index";
	$core->fileindex = "./index";
	$core->filedef = array ("about");
	//$core->fileset=array("changelog", "changepass", "infoedit");
	$core->file2file = array ("lpreview" => "post_preview", "chat" => "shortcuts/chat",
			 "update" => "sys/userupdate", "yandex_4546a87d27a25d8f.txt" => "files/dummy",
			 "login_vk"=>"blog_section/login_vk", "login"=>"blog_section/login", "logout"=>"blog_section/logout",
			 "yandex_6e4355c8cd33b6fa.txt"=>'files/dummy' );
	$core->fold2file = array ("sites" => "./sites/index", "blog" => "./blog/index", "post" => "blog_section/post", "posts" => "blog_section/post" );
	$core->process_request ();
}