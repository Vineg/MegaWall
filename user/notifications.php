<?php

$uid=User::get_id();
if(!$uid){l404();}
$page=new Page(PAGE_DEFAULT);
$type_id=Type::get_ctype_id();
$types_ar=Type::get_all_childs($type_id);
//init vars
for($i=1; $i<count($types_ar); $i++){
	$types_ar[$i]=h2s($types_ar[$i]);
	$ot.="or type_id='$types_ar[$i]'";
}
$types_ar[0]=h2s($types_ar[0]);
$type_f=$types_ar[0]?"and (type_id=$types_ar[0] $ot)":"";
$tname=get_type_name($types_ar[0]);
$title=array_search("1", $types_ar)?"Главная":$tname;
$cpage=max($cpage, 1);

$ppp=15;
$begin=($cpage-1)*$ppp;

//$notification_ids=User::get_notification_post_id();
//$selectar=array();
//for($i=0; $i<count($notification_ids);$i++){
//	if($notification_ids[$i]){
//		$selectar[]="id=$notification_ids[$i]";
//	}
//}

//$selects=join("||", $selectar);
//$selects=$selects?$selects:'false';
$pquery0="select * from (select * from ".Post_const::$post_select.", notification where post_id=id)notification where user_id=$uid";
$sort=$page->sort?$page->sort:"daily_rate";
$desc=$page->sorta?"asc":"desc";
$sort=h2s($sort);
$ppp=h2s($ppp);
$pquery="$pquery0 ORDER BY $sort $desc, date desc LIMIT $begin, $ppp";
//$res=my_q($pquery);
$pqueryn="select * from ($pquery)post where readed<1";
$pqueryo="select * from ($pquery)post where readed>=1";
$resn=my_q($pqueryn);
$reso=my_q($pqueryo);

$crowsn=my_n($resn);
$crowso=my_n($reso);
$rows=my_qn($pquery0);
$maxpage=ceil($rows/$ppp);
// for($i=0; $i<count($notification_ids); $i++){
// 	if($notification_ids[$i]){
// 		$nf_posts[]=new Post($notification_ids[$i]);
// 	}
// 	$nf_posts=usort($nf_posts, "Post::rate_comp");
// }
// $postnum=count($nf_posts);
//end

//create pages block
$opagesblock=page_block($maxpage, $cpage);

if($cpage>1){
	l404();
}
$opagesblock="<div class='spage wb'>$opagesblock</div>";
//end
$page->content.=$opagesblock;

for($i=0; $i<$crowsn; $i++){
	$page->content.=def_wall_post($resn, $i, array(type=>"shadow"));
}
for($i=0; $i<$crowso; $i++){
	$page->content.=def_wall_post($reso, $i);
}
$page->content.=$opagesblock;

my_q("update notification set readed=readed+1 where user_id=$uid");
$sv=st_vars::$script_version;
$page->title="Оповещения";
$ht=Request::get_ht();
$page->head=<<<EOQ
<script type="text/javascript" src="$ht/files/jscripts/notifications.js?$sv"></script>
EOQ;
$page->dtime = Timer::end_time();

process_page($page);

?>