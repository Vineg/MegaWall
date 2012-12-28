<?php
class Project{
	static function get_about_post_id($host){
		$host = my_s($host);
		return my_fst("select * from project where host='$host'", "about_post_id");
	}
}