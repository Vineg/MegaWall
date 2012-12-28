<?php

require_once 'phpscr/functions.php';
require_once "phpscr/post.php";
require_once "phpscr/shortcuts.php";
require_once "phpscr/user.php";
require_once "phpscr/get_types_list.php";
require_once "settings/mysql_connect.php";

$link=s2link::translit($_POST['link']);
$name=h2s($_POST['name']);
$full_name=h2s($_POST['full_name'])?h2s($_POST['full_name']):$name;
$page_user_id=h2i($_POST[page_user_id]);
$parent=h2i($_POST['parent']);
$table=my_s($_POST[table]);

$vars = array(table=>$table, page_user_id=>$page_user_id,link=>$link, full_name=>$full_name);
if(Type::create($parent, $name, $vars)){
print "<script>window.location=window.location;</script>";
};