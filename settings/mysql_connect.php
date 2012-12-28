<?php
global $mylink;
$mylink=mysql_connect(vars::$mysql_host, vars::$mysql_user, vars::$mysql_pass);
mysql_select_db(vars::$mysql_database, $mylink);
mysql_set_charset('utf8',$mylink);
?>