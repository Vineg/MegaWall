<?php
$ftree=Request::get_ftree();
$file=Request::get_file();
if($ftree[0]!="mysites"){
	$ftree=array_merge(array("mysites"), $ftree);
}
Request::$ftree[0]=$ftree;
//if(!$ftree[1]){
//	if($file){
//		if($file=="yandex_4546a87d27a25d8f.txt"){exit;}else{l404();}
//	}
//	include "user/mysites/index.php";
//}else if(!$ftree[2]){
//	include "user/mysites/site/index.php";
//}else if($ftree[2]=="links"){
//	if(!$file){
//		include "user/mysites/site/links/index.php";
//	}else{
//		include "user/mysites/site/links/link.php";
//	}
//}else{
//	l404;
//}
// else{
// 	include "/mysites/mylink.php";
// }
?>