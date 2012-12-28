<?php
if(User::get_rate()<st_vars::$rate_delete){l404();}

$page->title="SafeSites";

require_once "settings/mysql_connect.php";
require_once "phpscr/post.php";
require_once "phpscr/user.php";
require_once "phpscr/shortcuts.php";
require_once 'phpscr/functions.php';
require_once 'phpscr/get_archive_list.php';

if($_POST["addr"]){
	my_q("insert into safe_sites(host, issafe) values('$_POST[addr]', $_POST[issafe])");
}

//init vars
for($i=1; $i<count($types_ar); $i++){
	$ot.="or type_id=$types_ar[$i]";
}
$type_f=$types_ar[0]?"and (type_id=$types_ar[0] $ot)":"";

$ppp=15;
$begin=($cpage-1)*$ppp;

$pquery="select * from safe_sites where 1 order by host asc";

$res=my_q($pquery);
$crows=my_n($res);
for($i=0; $i<$crows;$i++){
	$class=my_r($res, $i, "issafe")==1?"ok":"err";
	$cid=my_r($res, $i, "id");
	$post_content.="<p class=$class sid=$cid><button class=delete type=button></button> ".htmlspecialchars(my_r($res, $i, "host"))."<span class=vmsg><span></p>";
}
//end

$post_content.="<p><form method=POST><input name=addr placeholder='новый сайт'></iput><input class=tiny name=issafe value=1></input><input type=submit class=but></input></form></p>";
$page->content.=simple_post($post_content);

$sv=st_vars::$script_version;

$page->head=<<<EOQ
<head>


<script type="text/javascript" src="$ht/files/jscripts/safesites.js?$sv"></script>

</script>
</head>
EOQ;

process_page($page);
?>