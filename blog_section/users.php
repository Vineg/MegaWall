<?php

//init vars
$page=new Page();
//end

$sv=st_vars::$script_version;
$page->title="Пользователи";
$page->head=<<<EOQ



EOQ;
$res=my_q("select * from user order by rate desc");
for($i=0; $i<my_n($res); $i++){
	$uname=my_r($res, $i, "login");
	$rate=my_r($res, $i, "rate");
	$link=User::get_link($uname);
	$post_content.="$link rate:$rate<br />";
}

$page->content.=simple_post($post_content);

process_page($page);
?>