<?php
//register_shutdown_function('shutdown');
// function shutdown(){
// 	print "\nend;\n";
// }
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
ini_set("max_execution_time", "-1");
date_default_timezone_set("Europe/Moscow");
$ps=realpath(__FILE__);
if(stripos($ps, "\\")){
	$ps=substr($ps, 0, strrpos($ps, "\\"));
}else{
	$ps=substr($ps, 0, strrpos($ps, "/"));
}
set_include_path("$ps/../");
chdir("$ps/../");
include_once "phpscr/require_all.php";
vars::$debug=true;
st_vars::$console=true;

links_bot();

User::$get_c_id=st_vars::$system_id;
if(get_hour(get_svar("last_hourly_update"))!=get_hour(curdate())){
	hourly_update();
	log_msg("hourly update finished");
	set_svar("last_hourly_update", curdate());
}
if(get_day(get_svar("last_update"))!=get_day(curdate())){
	check_users();
	post_yandextop();
	daily_update();
	set_svar("last_update", curdate());
	log_msg("daily update finished");
}else{
	print "Already updated today.\n";
}
?>
