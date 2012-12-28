<?php

if(User::get_rate()<st_vars::$rate_safe_sites){l404();}

$page=new Page();

//init vars
//end

$tl=new types_list();
$tl->table="theme";

$tl->selected=Type::get_ctype_id();
$types_list=$tl->get_types_list();
$tl->display_ids=true;
$tl->full_name_edit=true;
if($_GET[mine]!==null){
	$tl->pguser_id=user::get_id();
}
$post_content.=$tl->get_types_list();
$params[scroll]="scrollx";
$page->content.=simple_post($post_content, $params);

$sv=st_vars::$script_version;

$page->head=<<<EOQ
<head>
<script type="text/javascript" src="/files/jscripts/jq.js"></script>
<script type="text/javascript" src="/files/jscripts/functions.js?$sv"></script>
<script type="text/javascript" src="/files/jscripts/safesites.js?$sv"></script>
<script type="text/javascript" src="/files/jscripts/objectsinit.js?$sv"></script>
</script>
</head>

EOQ;

process_page($page);
?>