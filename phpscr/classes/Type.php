<?php
class Type {
	static $get_author, $get_pub, $get_all_childs, $get_start, $start_id = 1;
	static $table = "type";
	
	static function get_author($id) {
		$table = self::$table;
		$get_author = &self::$get_author [$id];
		if ($get_author == null) {
			$get_author = my_fst ( "select user_id from $table where id=$id", "user_id" );
		}
		return $get_author;
	}
	static function get_pub($id) {
		$table = self::$table;
		$get_pub = &self::$get_pub [$id];
		if ($get_pub == null) {
			$get_pub = my_fst ( "select pub from $table where id=$id", "pub" );
		}
		return $get_pub;
	}
	
	static function get_start($id) {
		$table = self::$table;
		$id = h2i ( $id );
		$get_start = &self::$get_start [$id];
		if ($get_start == null) {
			$get_start = my_fst ( "select start from $table where id=$id", "start" );
		}
		return $get_start;
	}
	
	//	static function get_main_type_id($type_id){
	//		$table = self::$table;
	//		$id = h2i ( $id );
	//		$get_start = &self::$get_start [$id];
	//		if ($get_start == null) {
	//			$get_start = my_f ( "select start from $table where id=$id", "start" );
	//		}
	//		return $get_start;
	//	}
	

	/**
	 * 
	 * @return Array childs without current value
	 * @param unknown_type $id
	 */
	static function get_all_childs($id = false) {
		$table = self::$table;
		if (! $id) {
			$id = self::get_ctype_id ();
		}
		$res = &self::$get_all_childs [$id];
		if (! $res) {
			$res = array ();
			$ctree = get_ttree ( $id );
			if (Page::get_page_user_id ()) {
				$pguser_id = Page::get_page_user_id ();
				$ufil = "and user_id=$pguser_id";
				$pub = 0;
			} else {
				$pub = 1;
			}
			//cho h2s("select * from type where pub>=$pub $ufil and tree like '%$ctree' order by tree asc");
			$qs = my_s ( "%@$ctree" );
			$q = my_q ( "select * from $table where pub>=$pub $ufil and tree like '$qs' order by tree asc;" );
			for($i = 0; $i < my_n ( $q ); $i ++) {
				$res [$i] = my_r ( $q, $i, "id" );
			}
			//rint_r($res);
			return $res;
		}
	}
	
	static function get_ctype_id() {
		global $_vars;
		if ($_vars[type_id]) {
			return $_vars[type_id];
		}else{
			if ($_GET [type]) {
				$res =  h2i ( $_GET [type] );
			}
			$table = self::$table;
			$ftree = Page::get_ftree ();
			if ($ftree [0] != "type") {
				return $_vars [main_type_id];
			}
			$parent = 0;
			$ftree = get_ftree ();
			$file = get_file ();
			$ftree [count ( $ftree )] = $file;
			$cid = $_vars [main_type] ? $_vars [main_type] : $_vars [main_type_id];
			for($i = 1; $i < count ( $ftree ); $i ++) {
				//cho $cid."q";
				$cname = $ftree [$i];
				$cname = h2s ( $cname );
				if (! $cname) {
					continue;
				}
				$pub = 1;
				if (Page::get_page_user_id ()) {
					$pub = 0;
					$ufil = "and user_id=" . Page::get_page_user_id ();
				}
				$cid = my_fst ( "select id from $table where pub>=$pub $ufil and link='$cname' AND parent=$cid", "id" );
				if (! $cid) {
					l404 ();
				}
			}
			$file = h2s ( $file );
			$cid = h2s ( $cid );
			if (! $cid) {
				$res = $_vars [main_type_id] ? $_vars [main_type_id] : vars::$type_main;
			} else {
				$res = $cid;
			}
			$_vars[type_id] = $res;
			return $res;
		}
	
		//		$tree=get_ttree($cid);
	//		$tres=my_q("select * from type where pub>=$pub and tree like '%$tree'");
	//
	//		for($i=0; $i<my_n($tres); $i++){
	//			$types_ar[$i]=my_r($tres, $i, "id");
	//		}
	}
	
	public static function get_main_type_id($type_id = false, $table = false) {
		if ($type_id) {
			while ( Type::get_start ( $type_id ) != true ) {
				$ptype_id = $type_id;
				$type_id = Type::get_parent ( $type_id );
			}
			// 			if($type_id==vars::$type_super){
			// 				return $ptype_id;
			// 			}
			return $type_id;
		} else {
			if (! $table || $table == "type") {
				global $_vars;
				//print_r($_vars);
				return $_vars [main_type_id];
			} else {
				return 1;
			}
		}
	}
	public static function get_parent($id) {
		return my_fst ( "select * from type where id=$id", "parent" );
	}
	public static function create($parent, $name,  $vars=array()) {
		$link = $vars[link];
		if (! $link) {
			$link = s2link::translit ( $name );
		}
		
		
		$link = s2link::translit ( $link );
		$name = h2s ( $name );
		$full_name = h2s ( $vars[full_name] ) ? h2s ( $vars[full_name] ) : $name;
		$page_user_id = h2i ( $vars [page_user_id] );
		$user_id = User::get_id ();
		$parent = h2i ( $parent );
		$table = my_s ( $vars[table] );
		if (! $table) {
			$table = "type";
		}
		
		if (! $link) {
			print "Введите ссылку!";
			exit ();
		}
		if (! $name) {
			print "Введите название!";
			exit ();
		}
		$pub = 1;
		if ($link && $name) {
			if ($page_user_id != $user_id && User::get_rate () < st_vars::$rate_add_type) {
				print "Ошибка13";
				slog ( "newtype parent:$parent name:$name, pguserid!=userid $page_user_id!=$user_id" );
				exit ();
			}
			if (! $page_user_id && User::get_rate () < st_vars::$rate_add_type) {
				print "Слишком низкий рейтинг";
				slog ( "newtype слишком низкий рейтинг" );
				exit ();
			}
			if ($page_user_id) {
				$pub = 0;
			}
			if (my_qn ( "select * from $table where id='$parent'" ) < 1) {
				print "родителя с id $parent не существует.";
			}
			
			if (my_qn ( "select * from type where parent='$parent' AND link='$link'" ) > 0 && $parent >= 0) {
				print "Раздел с такой ссылкой уже есть";
				exit ();
			}
			
			$start = $vars[start]?1:0;
			
			$r = my_q ( "insert into $table(start,name, link, parent, tree, full_name, pub
			, user_id) values($start,'$name', '$link', '$parent', '', '$full_name', '$pub', '$user_id')" );
			$iid = mysql_insert_id ();
			
			$ptree = get_ttree ( $parent );
			$tree = "$iid@$ptree";
			
			
			my_q ( "update $table set tree='$tree' where id=$iid" );
			return $iid;
		}
	}
}