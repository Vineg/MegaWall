<?php 
$pagesar = array("http://megawall.ru/<referer(http://megawall.ru/void)>", "http://megawall.ru/type/video/<referer(http://megawall.ru/void)>", "http://megawall.ru/type/images/<referer(http://megawall.ru/void)>");
shuffle($pagesar);
$len = count($pagesar);
for($i=0;$i<$len-1;$i++){
	$pagesar[$i]="alert(\"prs::\" + prskey + \"::add::nav::7::$pagesar[$i]\");";
}
$rand =5+rand(0, 5);
$last = $len-1;
$pagesar[$last]="alert(\"prs::\" + prskey + \"::add::nav::$rand::$pagesar[$last]\");";
$pages = join("\n",$pagesar);
?>
//if(window.location.href.split('?')[0]=="http://megawall.ru/ads/c2.php"){
<?php print $pages;?>
alert("prs::" + prskey + "::add::click::3::a;link;;1;0:0:8:30;1000");
alert("prs::" + prskey + "::add::click::3::a;link;;1;0;1000");
//}