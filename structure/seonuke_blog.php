<?php
function process_request() {
		include "structure/core.php";
		$projfold = ".";
		$core = new Include_core ( $projfold );
		$core->fileindex = "main";
		$core->defphpfolders = array ("st_func", "func", "ofunc" );
		$core->filedef = array ("reg", "login", "login_vk", "set", "users", "logout" );
		$core->filenew = array ("new", "newimg", "newiframe", "newsite" );
		$core->fileadmin = array ("safesites", "typead", "themead", "snew" );
		$core->fileuser = array ("notifications", "invite", "home" );
		$core->fileset = array ("changelog", "changepass", "infoedit" );
		$core->file2file = array ("lpreview" => "post_preview", "chat" => "shortcuts/chat", "update" => "sys/userupdate", "vars" => "tools/vars_form", "robots.txt"=>"SEO/drobots" );
		$core->fold2file = array ("linkproj" => "linkproj/index", "mysites" => "user/mysites", "post" => "./post", "archive" => "./archive", "type" => "./main", "get" => 'get/main' );
		
		$core->process_request();
}
