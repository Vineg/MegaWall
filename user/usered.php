<?php
require_once "settings/mysql_connect.php";
require_once "phpscr/post.php";
require_once "phpscr/user.php";
require_once "phpscr/shortcuts.php";
require_once 'phpscr/functions.php';
require_once "phpscr/user.php";
require_once 'sys/st_vars.php';

//init vars
$page=new Page();

$pguser_id=Page::get_page_user_id();
$pguser=User::get_login($pguser_id);
if(!$pguser_id){l404();}
$crate=User::get_rate($pguser_id);

$pguser_id=h2i($pguser_id);
$user_content=my_fst("select user_page from user where id='$pguser_id'", "user_page");
//$user_content=replace_vars($user_content, $user_vars);

$defval=st_vars::$def_user_page;
$defval=h2s($defval);
$ht=Request::get_ht();
$page->content.=simple_post(
<<<EOQ
<form action=$ht/set method=POST>
<textarea id=dv class=hidden>$defval</textarea>
<textarea id=mainArea style="width: 100%; " placeholder="Дополнительная информация." name="user_page" id="texted" class="big-textarea">$user_content</textarea>
<button class=def onClick="defval(); return false;">По умолчанию</button><button class=submit>Отправить</button>
</form>
EOQ
);
//cho htmlspecialchars($page->content);

$sv=st_vars::$script_version;

$page->title=$pguser;
$page->head=<<<EOQ
<script type="text/javascript" src="$ht/files/jscripts/jq.js?$sv"></script>

<script type="text/javascript" src="$ht/files/jscripts/usered.js?$sv"></script>
EOQ;

process_page($page);
?>