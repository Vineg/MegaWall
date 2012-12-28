<?php
$tl=new types_list();
Page::$location=$_SERVER["HTTP_REFERER"];
$tl->pguser_id=$_GET[pguid];
$tl->display_ids=$_GET[display_ids];
print $tl->build_childs($_GET[t], $_GET[l]);