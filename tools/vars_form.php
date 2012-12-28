<?php
require_once "phpscr/post.php";
require_once "phpscr/shortcuts.php";
require_once "phpscr/user.php";
require_once "phpscr/get_types_list.php";
require_once 'phpscr/functions.php';
require_once "phpscr/html_filter.php";
require_once "phpscr/kses.php";
require_once 'settings/mysql_connect.php';
require_once 'sys/vars.php';
require_once "phpscr/time.php";

$page=new Page(PAGE_WIDE);

$user_id=User::get_id();
if(!$user_id){l404();}

$sv=st_vars::$script_version;
$ht=Request::get_ht();

$page->title="Переменные";
$page->head=<<<EOQ
<script type="text/javascript" src="$ht/files/jscripts/jq.js?$sv"></script>
<script type="text/javascript" src="$ht/files/jscripts/vars.js?$sv"></script>
EOQ;

$ref=$_GET[referer];
unset($_GET[referer]);
$var2type=array("boolean"=>FORM_CHECKBOX);
foreach ($_GET as $k=>$v){
	$tar=s2ar($v, "_");
	$type=$var2type[$tar[0]];
	$defval=h2s($tar[1]);
	$descr=h2s($tar[2]);
	$formar[]="<li>".get_form_part($k,$type,$descr,$defval)."</li>";
}
$formcont=join("",$formar);

$post_content=<<<EOQ
<form action="$ref" method=GET>
<ul class=invis>
$formcont
<li><input type=hidden name=final value=1><button class=submit>Скачать</button></li>
</ul>
</form>
EOQ;

$page->content=simple_post($msg.$post_content);

process_page($page);

function get_form_part($name, $type, $description, $defval=false){
	if($type==FORM_CHECKBOX){
		$defval=b2hcb($defval);
		return "$description <input name='$name' type=checkbox $defval>";
	}
}
?>