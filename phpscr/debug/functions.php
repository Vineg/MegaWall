<?php
set_error_handler("error_handler");
function log_msg($s,$type="def"){
	$s=my_s($s);
	$date=time();
	my_in("log:msg,date,msg_type:$s,$date,$type");
}
function error_handler($errno, $errstr, $errfile, $errline)
{
	switch ($errno) {
		case E_USER_ERROR:
			print "<b>My ERROR</b> [$errno] $errstr<br />\n";
			print "  Fatal error on line $errline in file $errfile";
			print ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			print "Aborting...<br />\n";
			exit(1);
			break;
		case E_USER_WARNING:
			//debug_message();
			//print "<b>My WARNING</b> [$errno] $errstr<br />\n";
			break;
		case E_WARNING:
// 			debug_message();
// 			print "<b>WARNING</b> [$errno] $errstr<br />\n";
			break;
		case E_USER_NOTICE:
// 			debug_message();
// 			print "<b>My NOTICE</b> [$errno] $errstr<br />\n";
			break;
		case E_STRICT:
			debug_message();
			print "<b>STRICT</b> [$errno] $errstr<br />\n";
			break;
		case 4096:
			debug_message();
			print "Unknown error type: [$errno] $errstr<br />\n";
			break;
		case 8:
			break;
		case 2:
			break;
		default:
			print "Unknown error type: [$errno] $errstr<br />\n";
			break;
	}

	/* Don't execute PHP internal error handler */
	return true;
}

function debug_message($message=""){
	$message.=print_br(debug_backtrace(), true);
	if(vars::$debug)
		print($message);
	else
		send_message_to_admin($message);
}

function debug_end(){
	print_br(debug_backtrace());
	exit;
}