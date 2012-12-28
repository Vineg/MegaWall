<?php
$host=vars::$host;
$uri=$_POST[newsite];
$ctype=0;
while($_POST["s$ctype"]>0){
	if($ctype==$_POST["s".$ctype]){
		exit;
	}
	$ctype=$_POST["s".$ctype];
}
$res=newsite($uri, $ctype);
if($res===true){
	Page::reload(true);
}else{
	if($_GET["direct"]===null){
		print($res);
	}else{
		def_page($res);
	}
}

