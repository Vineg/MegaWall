<?php

require_once "settings/mysql_connect.php";
require_once "phpscr/post.php";
require_once "phpscr/user.php";
require_once "phpscr/shortcuts.php";
require_once 'phpscr/functions.php';

$ctype_id=Type::get_ctype_id();
$ht=Request::get_ht();
$ftree=Request::get_ftree();
$file=Request::get_file();
//init vars
$ppp=15;
$cpage=max($cpage, 1);
//end

//function pq($val, $type, $pq){
//	$fr=($pq=="")?"archive":"($pq)date";
//	$exp=($type=="")?"1":"date_format(date, '$type')='$val'";
//	return "select * from $fr where $exp and type=0 order by date desc";
//}
$page=new Page();

//function ctype($type){
//	if($type==""){$ctype="%Y";}else if($type=="%Y"){$ctype="%M";}else if($type=="%M"){$ctype="%d";}else if($type=="%d"){return -1;}
//}


//build archive query
$ardate=array(
	0=>$ftree[1],
	1=>$ftree[2],
);
$ardate[count($ardate)]=$file;
$ppp=15;


//for($i=0; $i<count($ardate); $i++){
//	$type=ctype($type);
//	if($type==-1){break;}
//	$nq=pq($ardate[$i], $type, $nq);
//}
//end

//create pages block
//cho "select * from archive where date=STR_TO_DATE('$ardate[2].$ardate[1].$ardate[0]', '%d.%M.%Y');";
$time=strtotime($ardate[2].$ardate[1].$ardate[0]);
//cho h2s("select * from archive where date<STR_TO_DATE('$ardate[2].$ardate[1].$ardate[0]', '%d.%M.%Y');");
$posts=my_fst("select * from archive where date<STR_TO_DATE('$ardate[2].$ardate[1].$ardate[0]', '%d.%M.%Y') and type_id=$ctype_id order by date desc;", "posts");
if($posts===false){
	l404();
}
$postar=array();
$postar=explode(" ", $posts);
$crows=count($postar);
$rows=$crows;
$maxpage=ceil($rows/$ppp);
$opagesblock=page_block($maxpage, $cpage);

if($cpage!=0&&$crows<1){l404();}
$opagesblock="<div class='spage wb'>$opagesblock</div>";
//end

$page->content.=$opagesblock;
$pcnt=0;
for($i=$ppp*($cpage-1); $i<min($crows, $ppp*$cpage); $i++){
	$cid=h2i($postar[$i]);
	if($cid==""){continue;}
	$pq="select * from ".Post_const::$post_select." where id=$cid";
	$page->content.=def_wall_post($pq, 0);
	$pcnt++;
}
if(!$pcnt&&$cpage!=1){
	l404();
}

$page->content.=$opagesblock;

$page->title="Архив";

$page->head=<<<EOQ

<script type="text/javascript" src="$ht/files/jscripts/functions.js"></script>
<script type="text/javascript" src="$ht/files/jscripts/objectsinit.js"></script>
<script type="text/javascript" src="$ht/files/jscripts/main.js"></script>
EOQ;

Timer::end_time();
process_page($page);
?>