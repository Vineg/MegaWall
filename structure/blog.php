<?php
function process_request() {
		global $_vars;
		include "structure/core.php";
		$projfold = "./blog_section";
		$core = new Include_core ( $projfold );
		$robots = $_vars[noindex]?"files/drobots.txt":"files/dummy";
		$core->fileindex = "./main";
		$core->defphpfolders = array ("st_func", "func", "ofunc", "test", "ads" );
		$core->filedef = array ("reg", "login", "login_vk",
		"set", "about", "users", "logout" );
		$core->filenew = array ("new", "newimg", "newiframe", "newsite" );
		$core->fileadmin = array ("newproj","safesites", "typead", "themead", "snew",
		 "init_main_tid", "post_type_fix", "useful", "dummypost" );
		$core->fileuser = array ("notifications", "invite", "home" );
		$core->fileset = array ("changelog", "changepass", "infoedit" );
		$core->file2file = array ("void"=>"/php_section/ads/tds","update"=>"/sys/userupdate", "lpreview" => "./post_preview", "chat" => "shortcuts/chat", "update" => "sys/userupdate", "vars" => "tools/vars_form", "robots.txt"=>$robots);
		$core->fold2file = array ("var"=>"proj/Script/var","proxy"=>"admin/proxy","linkproj" => "linkproj/index", "mysites" => "user/mysites", "post" => "./post", "posts" => "./post", "archive" => "./archive", "type" => "./main", "get" => 'get/main' );
		
		$core->process_request();
}
