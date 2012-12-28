<?php
require_once "phpscr/vote.php";
$post_id=base_convert($_POST["pid"], 36, 10);
$post_content_id=h2i($_POST["cid"]);
$vote=$_POST["vote"];
$vote=$vote==1?1:-1;
if($post_id){
	print vote($post_id, $vote);
}else if($post_content_id){
	print vote($post_content_id, $vote, false, $for="post_content");
}

?>