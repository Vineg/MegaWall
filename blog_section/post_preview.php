<?php

require_once "settings/mysql_connect.php";
require_once "phpscr/post.php";
require_once "phpscr/user.php";
require_once 'phpscr/functions.php';

$page = new Page ();
$post = unserialize ( $_SESSION [lpost] );
$newlink = $post->params [newlink];
$post->params [preview] = false;
$ht = Request::get_ht ();

$tl = new types_list ();
$types_list = $tl->get_types_list ();

//build main post
if (! $post || (! $post->text && ! $post->name)) {
	l404 ();
}

$page->content .= dif_wall_post_r ( $post, array (full => 1 ) );
$_SESSION [lpost] = serialize ( $post );
if ($newlink == "post_edit") {
	$handler = "$newlink";
	$newlink = $post->params ["form"];
} else {
	$handler = "new/$newlink";
	$newlink = "/$newlink";
}
$newlink = Request::addGETparams ( array ("cont" => false ), $newlink );
$ar = array ();
Request::addGETparams ( $ar );
$post_content = <<<EOQ
<form class=async method=POST action = "/func/$handler.php">
<input type=hidden name=lpost value=1 />

	<div class=center>
	
		<button class="submit">Отправить</button><button class="def" onClick="window.location='$newlink'; return false;">Вернуться</button>
		<div class=msg><span class="body err"></span></div>
	</div>
</form>
EOQ;
//cho "<pre>".h2s(simple_post($post_content))."</pre>";
$page->content .= simple_post ( $post_content );
//end


$title = $post->name;
$title = ($title == "") ? "Без названия" : $title;
$page->title = "Предпросмотр: $title";
$sv = st_vars::$script_version;
$page->head = <<<EOQ
<script type="text/javascript" src="$ht/files/jscripts/preview.js?$sv"></script>
EOQ;

process_page ( $page );
?>