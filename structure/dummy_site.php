<?php
function process_request() {
		global $vars;
		include "structure/core.php";
		$projfold = "./proj/dummy_site";
		$core = new Include_core ( $projfold );
		$robots = $vars[noindex]?"files/drobots.txt":"files/dummy";
		$core->fileindex = "./index";
// 		$core->defphpfolders = array ("st_func", "func", "ofunc", "test" );
// 		$core->filedef = array ("reg", "login", "login_vk", "set", "about", "users", "logout" );
// 		$core->filenew = array ("new", "newimg", "newiframe", "newsite" );
// 		$core->fileadmin = array ("safesites", "typead", "themead", "snew", "init_main_tid", "post_type_fix" );
// 		$core->fileuser = array ("notifications", "invite", "home" );
// 		$core->fileset = array ("changelog", "changepass", "infoedit" );
// 		$core->file2file = array ("lpreview" => "./post_preview", "chat" => "shortcuts/chat", "update" => "sys/userupdate", "vars" => "tools/vars_form", "yandex_7c49de6b6fe11190.txt" => "files/dummy", "robots.txt"=>$robots);
 		$core->fold2file = array ("page"=>"./page", "user"=>"./page");
		
		$core->process_request();
}
