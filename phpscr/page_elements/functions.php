<?php
function encoding_fix($encoding="utf-8"){
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=$encoding\" />";
}
function page($i){
	global $_GET;
	global $adr;
	$i=max(1, $i);
	$nget=$_GET;
	if($i>1){
		$nget["p"]=$i;
	}else{
		unset($nget["p"]);
	}
	return $adr.form_get($nget);
}

function page_block($pages, $cpage=1){
	$maxpage=$pages;
	global $getex;
	$ht=get_url_path();
	$req=$_SERVER["REQUEST_URI"];
	$req=($getex==false)?"$req?":"$req&";
	if($maxpage<2){return false;}

	if($cpage>=3){
		$opagesblock.="<a href='$ht".page(1)."'>1</a>";
	}
	if($cpage>=4){
		$opagesblock.="<a href='$ht".page($cpage-2)."'>...</a>";
	}

	for($i=max($cpage-1, 1); $i<=min($cpage+1, $maxpage); $i++){
		$ac=($i==$cpage)?"<b>$i</b>":$i;
		$opagesblock.="<a href='$ht".page($i)."'>$ac</a>";
	}
	if($maxpage-$cpage>=3){
		$opagesblock.="<a href='$ht".page($cpage+2)."'>...</a>";
	}
	if($maxpage-$cpage>=2){
		$opagesblock.="<a href='$ht".page($maxpage)."'>$maxpage</a>";
	}
	return "$opagesblock";
}

function def_page($s, $title="", $stylenscripts=false){
	if($stylenscripts){
		$sv=st_vars::$script_version;
		$stv=st_vars::$style_version;
		$ht=Request::get_ht();
		$template=Page::get_template_name();
		$had=<<<EOQ
<link rel="stylesheet" href="$ht/files/templates/$template/style.css?$stv" type="text/css" media="screen" />
<script type="text/javascript" src="$ht/files/jscripts/jq.js?$sv"></script>
<script type="text/javascript" src="$ht/files/jscripts/functions.js?$sv"></script>
<script type="text/javascript" src="$ht/files/jscripts/objectsinit.js?$sv"></script>
EOQ;
	}
	print <<<EOQ
	<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>$title</title>
<meta name='robots' content='noindex'/>
$had
</head>
<body style="width:100%;">
<html>
<body>
	$s
</body>
</html>
EOQ;
}
?>