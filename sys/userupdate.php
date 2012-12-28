<?php 
if(!User::get_rate()>=st_vars::$rate_admin){l404();}
?>
<?php
include "./sys/update.php";
hourly_update();
?>
