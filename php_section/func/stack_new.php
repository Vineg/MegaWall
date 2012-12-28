<?php
if($_POST[button_name]==="set"){
	sc("stack_new", "1");
	ret("stat=ok&msg=Stack new was successfully set");
}elseif ($_POST[button_name]==="unset"){
	sc("stack_new", "0");
	ret("stat=ok&msg=Stack new was successfully unset");
}else{
	ret("stat=err&msg=Wrong POST");
}