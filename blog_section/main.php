<?php
$cpage = $_GET [p];
$type_id = Type::get_ctype_id ();


//init vars
$main_core=new Main_core($type_id);
$crows = my_n ( $main_core->request );
$rows = my_qn ( $main_core->type_request );
$maxpage = ceil ( $rows / $main_core->posts_per_page );
$title = get_type_name ( $type_id );
//end


$page = new Page ();

//create pages block
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

$sv = st_vars::$script_version;
$page->title = $title;
$ht = Request::get_ht ();
$page->head = <<<EOQ
<script type="text/javascript" src="$ht/files/jscripts/main.js?$sv"></script>
EOQ;

process_page ( $page );
?>