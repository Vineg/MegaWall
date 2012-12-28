<?php
$zip= new ZipArchive();
$file = "./linkproj_code.zip";

$def_encoding="cp1251";

$uservars=array(autoupdate=>"boolean_true_Включить автообновление");

foreach ($uservars as $k=>$v){
	$tar=s2ar($v, "_");
	$type=$tar[0];
	if($_GET[$k]===null){
		if(!$_GET["final"]){
			get_file_vars($uservars);
		}else{
			$_GET[$k]=false;
		}
	}
	if($type=="boolean"){
		$_GET[$k]=h2b($_GET[$k])?"true":"false";
	}
}

if (
	$zip->open($file, ZIPARCHIVE::OVERWRITE)!==TRUE) {
	exit("cannot open <$file>\n");
}

if(!$_GET[encoding]){
	$_GET[encoding]=$def_encoding;
}

$mw_user=User::get_hash();
$secret_code=User::get_linkproj_secret();
$code=fr("linkproj_code.txt");
$code=replace_php_vars($code, array_merge($_GET,array(mw_user=>"$mw_user", mw_secret=>User::get_linkproj_secret())));
$loader_code=fr("linkproj_loader_code.txt");
$loader_code=replace_php_vars($loader_code, array_merge($_GET,array(mw_user=>"$mw_user", mw_secret=>User::get_linkproj_secret())));
$zip->addFromString("/$mw_user/megawall.php", $code);
$zip->addFromString("/$mw_user/megawall_loader.php", $loader_code);
$zip->close();
$content.=fr("/linkproj_code.zip");

function get_file_vars($array){
	$req=http_build_query(array_merge($array,array(referer=>Request::get_uri())));
	loc(Request::get_ht()."/vars?$req");
}
?>