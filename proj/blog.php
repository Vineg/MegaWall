<?php

global $_vars;
vars::$host=$_SERVER[HTTP_HOST];
if(!vars::$parent_host){
	vars::$parent_host=vars::$host;
} 
$_vars[main_type_id]=st_vars::$type_main;

$_vars[sidebar_parts] = array("settings", "types");
 if (Type::get_main_type_id () == vars::$type_super) {
 	$_vars [super] = true;
 	$_vars[noindex]=true;
 }
 if(in_array($_vars[main_type_id], vars::$no_index)){
 	$_vars[noindex]=true;
 }
include 'structure/blog.php';
process_request();