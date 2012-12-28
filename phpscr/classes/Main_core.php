<?php
class Main_core {
	public $posts_per_page = 15, $type, $request, $type_request;
	function main_core($type_id, $user_id = false) {
		global $_vars;
		$cpage = Page::get_page ();
		$types_ar = Type::get_all_childs ( $type_id );
		$otar = array ();
		for($i = 1; $i < count ( $types_ar ); $i ++) {
			$types_ar [$i] = h2s ( $types_ar [$i] );
			$otar [] = "type_id='$types_ar[$i]'";
		}
		$ot = join ( " or ", $otar );
		$ot = $ot ? $ot : "false";
		$types_ar [0] = h2s ( $types_ar [0] );
		if (!$_vars [super]) {
			$pub1 = "and pub>=2";
			$pub = "and pub>=1";
		}
		$type_f = "and (((type_id=$type_id) $pub) or (($ot) $pub1))";
		$cpage = max ( $cpage, 1 );
		
		$this->posts_per_page = 15;
		$begin = ($cpage - 1) * $this->posts_per_page;
		if ($user_id) {
			$user_f = "AND author_id=$user_id";
		}
		$this->type_request = "select * from " . Post_const::$post_select . " where 1  and parent=0 $type_f $user_f";
		// cho h2s($pquery0);
		$sort = ($_SESSION ["sort"]) ? $_SESSION ["sort"] : "daily_rate";
		$adsort = $sort != "date" ? ", date desc" : "";
		$desc = $_SESSION ["sorta"] ? "asc" : "desc";
		$sort = h2s ( $sort );
		$this->posts_per_page = h2s ( $this->posts_per_page );
		$pquery = "$this->type_request ORDER BY $sort $desc$adsort LIMIT $begin, $this->posts_per_page";
// 		if(vars::$debug){
// 			cho $pquery;
// 		}
		$res = my_q ( $pquery );
		$this->request = $res;
	}
}
?>