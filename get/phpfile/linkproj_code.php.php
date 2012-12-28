<?php

$mw_user=User::get_hash();
$secret_code=User::get_linkproj_secret();
$code=fr("linkproj_code.txt");
$replacement=array_merge(array(mw_parent_host=>vars::$host,mw_user=>"$mw_user", mw_secret=>User::get_linkproj_secret()), $_GET);
$code=replace_php_vars($code, $replacement);
$content.=$code;
?>