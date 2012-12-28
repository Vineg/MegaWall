<?php
require_once 'phpscr/functions.php';
require_once "phpscr/post.php";
require_once "phpscr/shortcuts.php";
require_once "phpscr/user.php";
require_once "phpscr/get_types_list.php";
require_once "settings/mysql_connect.php";

$link=s2link::translit($_POST['link']);
$name=h2s($_POST['name']);
$full_name=h2s($_POST['full_name']);
$parent=h2i($_POST['parent']);
$id=h2i($_POST['id']);
$table=my_s($_POST[table]);
if(!$table){
	$table="type";
}


if($link&&$name&&$parent!==null){
	if(Type::get_author($id)==User::get_c_id()||User::get_rate()>=st_vars::$rate_edit_type){
		$childs=get_all_type_childs($id);
		if(array_search($parent, $childs)){
			print("Ошибка1");
			//rint_r($childs);
			exit;
		}
		if($parent==$id){
			print "Ошибка3"; exit;
		}
		if(!$parent){
			$parent=0;
		}
		if(my_qn("select * from $table where id='$parent'")<1){
			print "родителя с id $parent не существует."; exit;
		}
		if($parent>=0&&(get_type_parent($id)!=$parent&&my_qn("select * from types where parent='$parent' AND link='$link' and id!=$id")>0&&$parent>=0)){
			print "Раздел с такой ссылкой уже есть";
			exit;
		}

		$r=my_q("update $table set name='$name', link='$link', parent='$parent', full_name='$full_name' where id=$id");

		for($i=0; $i<count($childs); $i++){
			//cho $childs[$i];
			update_tree($childs[$i]);
		}

		print "<script>window.location=window.location;</script>";
	}else{
		if($cid==false){
			print "Ошибка2";
			exit;
		}
	}
}

function update_tree($id){
	$pid=get_type_parent($id);
	$ptree=get_ttree($pid);
	$tree="$id@$ptree";
	//cho $tree;
	my_q("update $table set tree='$tree' where id=$id");
}


?>