<?php
function get_ttree($id) {
	$id = h2i ( $id );
	return my_fst ( "select tree from type where id=$id", "tree" );
}

function get_all_type_childs($id) {
	$res = array ();
	$ctree = get_ttree ( $id );
	if (Page::get_page_user_id ()) {
		$pguser_id = Page::get_page_user_id ();
		$ufil = "and user_id=$pguser_id";
		$pub = 0;
	} else {
		$pub = 1;
	}
	$q = my_q ( "select * from type where pub>=$pub $ufil and tree like '%$ctree' order by tree asc" );
	for($i = 0; $i < my_n ( $q ); $i ++) {
		$res [$i] = my_r ( $q, $i, "id" );
	}
	return $res;
}

function lref() {
	if ($_SERVER ['HTTP_REFERER'] != $_SERVER ['REQUEST_URI']) {
		header ( "location:" . $_SERVER ['HTTP_REFERER'] . "" );
	}
}
function get_type_author($id) {
	return Type::get_author ( $id );
}
?>
