<?php 
function main($type_id){
	$type_id=Type::get_ctype_id();
	$types_ar=Type::get_all_childs($type_id);
	//init vars
	$otar=array();
	for($i=1; $i<count($types_ar); $i++){
		$types_ar[$i]=h2s($types_ar[$i]);
		$otar[]="type_id='$types_ar[$i]'";
	}
	$ot=join(" or ", $otar);
	$ot=$ot?$ot:0;
	$types_ar[0]=h2s($types_ar[0]);
	$type_f=$types_ar[0]?"and (((type_id=$type_id) and pub>=1) or (($ot) and pub>=2))":"";
	$tname=get_type_name($type_id);
	$title=array_search("1", $types_ar)?"MegaWall":$tname;
	$cpage=max($cpage, 1);
	
	$ppp=15;
	$begin=($cpage-1)*$ppp;
	
	$pquery0="select * from ".Post_const::$post_select." where pub>=1 and parent=0 $type_f";
	//cho h2s($pquery0);
	$sort=($_SESSION["sort"])?$_SESSION["sort"]:"daily_rate";
	$adsort=$sort!="date"?", date desc":"";
	$desc=$_SESSION["sorta"]?"asc":"desc";
	$sort=h2s($sort);
	$ppp=h2s($ppp);
	$pquery="$pquery0 ORDER BY $sort $desc$adsort LIMIT $begin, $ppp";
	$res=my_q($pquery);
	$crows=my_n($res);
	$rows=my_qn($pquery0);
	$maxpage=ceil($rows/$ppp);
	//end
	
	$page=new Page();
	
	//create pages block
	$opagesblock=page_block($maxpage, $cpage);
	
	if($cpage>1&&$crows<1){
		l404();
	}
	$opagesblock="<div class='spage wb'>$opagesblock</div>";
	//end
	$page->content.=$opagesblock;
	
	for($i=0; $i<$crows; $i++){
		$page->content.=def_wall_post($res, $i);
	}
	$page->content.=$opagesblock;
	
	$sv=st_vars::$script_version;
	$page->title=$title;
	$ht=Request::get_ht();
	$page->head=<<<EOQ
	<script type="text/javascript" src="$ht/files/jscripts/main.js?$sv"></script>
EOQ;
	$page->dtime = Timer::end_time();
	
	if($_GET["d"]==1){
		//rint_r($time);
	}
	process_page($page);
}
