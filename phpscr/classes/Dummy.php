<?php
class Dummy{
	static function get_users_query($type_id){
		$post_sel = Post_const::$post_select;
		return my_q("select * from $post_sel where type_id=$type_id and pub>=0 and vars like 'user'");
	}
}