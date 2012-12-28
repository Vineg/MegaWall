<?php

require_once "settings/mysql_connect.php";
require_once "phpscr/post.php";
require_once "phpscr/user.php";
require_once "phpscr/shortcuts.php";
require_once 'phpscr/functions.php';
require_once "phpscr/user.php";

//init vars
$ctype = Type::get_ctype_id();
$cpage = Page::get_page();
$type_id = $ctype;
$pguser=Page::get_pguser();
$pguser_id=User::get_id($pguser);
$pguser=User::get_login($pguser_id);
$user_vars[posts]=my_qn("select * from post where author_id=$pguser_id and rate>=1");
$user_vars[username]=$pguser;
if(!$pguser_id){l404();}
$crate=User::get_rate($pguser_id);
$user_vars[rate]=$crate;

$type_f=$type_id?"and type=$type_id":"";

$pguser_id=h2i($pguser_id);

// $ppp=15;
// $begin=($cpage-1)*$ppp;

// $pquery0="select * from ".Post_const::$post_select." where pub>=0 and author_id=$pguser_id $type_f";
// $sort=($_SESSION["sort"])?$_SESSION["sort"]:"daily_rate";
// $desc=$_SESSION["sorta"]?"asc":"desc";
// $sort=h2s($sort);

//$ppp=h2s($ppp);
// $pquery="$pquery0 ORDER BY $sort $desc LIMIT $begin, $ppp";
// $res=my_q($pquery);
// $crows=my_n($res);
// $rows=my_qn($pquery0);
// $user_vars[posts]=$rows;
$regdate=my_fst("select * from user where id=$pguser_id","date");
$intime=time()-$regdate;
$indate=sec_to_date($intime);

$intime=date_to_string($indate);

$user_vars[intime]=$intime;

//$maxpage=ceil($rows/$ppp);

$tl=new types_list(User::get_id());
$types_list=$tl->get_types_list($ctype);
//end
//create pages block
//$opagesblock=page_block($maxpage, $cpage);

// if($cpage>1&&$crows<1){
// 	l404();
// }
//$opagesblock="<div class='spage wb'>$opagesblock</div>";
//end


$user_content=my_fst("select user_page from user where id='$pguser_id'", "user_page");

$user_content=replace_php_vars($user_content, $user_vars);
//cho h2s($user_content);
$page = new Page();

$page->content.=simple_post(
<<<EOQ
<div class=olist><a class=open>Информация о пользователе</a><div class='openobj hidden'>
$user_content
</div>
</div>

EOQ
);

$main_core = new Main_core($ctype, $pguser_id);
$crows = my_n ( $main_core->request );
$rows = my_qn ( $main_core->type_request );
$maxpage = ceil ( $rows / $main_core->posts_per_page );
$title = get_type_name ( $type_id );

$opagesblock = page_block ( $maxpage, $cpage );

if ($cpage > 1 && $crows < 1) {
	l404 ();
}
$opagesblock = "<div class='spage wb'>$opagesblock</div>";
//end
$page->content .= $opagesblock;

for($i = 0; $i < $crows; $i ++) {
	$page->content .= def_wall_post ( $main_core->request, $i );
}
$page->content .= $opagesblock;
$sv=st_vars::$script_version;
$page->title=$pguser;
$ht = Request::get_ht();
$page->head=<<<EOQ


<script type="text/javascript" src="$ht/files/jscripts/user.js?$sv"></script>

EOQ;

process_page($page);

function sec_to_date($sec){
	$sec0=$sec;
	$years=floor($sec/86400/365);
	$sec-=$years*86400*365;
	$ayears=floor($sec0/86400/365);
	$days=floor($sec/86400);
	$adays=floor($sec0/86400);
	$sec-=$days*86400;
	$hours=floor($sec/3600);
	$ahours=floor($sec0/3600);
	$sec-=$hours*3600;
	$minutes=floor($sec/60);
	$aminutes=floor($sec0/60);
	$sec-=$minutes*60;
	$asec=$sec0;
	$res=array(seconds=>$sec, minutes=>$minutes, hours=>$hours, days=>$days, aseconds=>$asec, aminutes=>$aminutes, ahours=>$ahours, adays=>$adays, years=>$years, ayears=>$ayears);
	return $res;
}

function human_plural_form($number, $titles=array('комментарий','комментария','комментариев')){
	$cases = array (2, 0, 1, 1, 1, 2);
	return $number." ".$titles[ ($number%100 >4 && $number%100< 20)? 2 : $cases[min($number%10, 5)] ];
}

function date_to_string($datear){
	if($datear[ayears]){
		$res=human_plural_form($datear[ayears], array('год','года', 'лет')).".";
	}else if($datear[adays]){
		$res=human_plural_form($datear[adays], array('день','дня', 'дней')).".";
	}else if($datear[ahours]){
		$res=human_plural_form($datear[ahours], array('час', 'часа', 'часов')).".";
	}else
	if($datear[aminutes]){
		$res=human_plural_form($datear[aminutes], array('минуту', 'минуты', 'минут')).".";
	}else
	if($datear[aseconds]){
		$res=human_plural_form($datear[aseconds], array('секунду', 'секунды', 'секунд')).".";
	}
	return $res;
}
?>