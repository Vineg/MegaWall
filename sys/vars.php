<?php
class vars{
	static $mysql_host="127.0.0.1", $mysql_user="root", $mysql_pass="123455", $mysql_database="blog";
	static $debug=false;
	static $lt=false;
	static $host="megawall.ru", $parent_host=false;
	static $host2type = array("niwow.ru"=>23, "minewow.ru"=>22, "kulok.ru"=>24, "megawall.ru"=>1, "super.megawall.ru"=>25);
	static $host_level=2;
	static $type_images=11;
	static $type_yandextop=1001;
	static $type_movie=9, $type_main=1, $type_seonuke=1005, $type_super=25;
	static $secret="fvbh539a154fase";
	static $vk_api_id=2379536;
	static $vk_secret_key="ZxLKeT4Igb6Ucm4IITUp";
	static $path="/home/vineg/blog";
	static $no_redirect=false;
	static $seonuke_host="seonuke.ru", $seonuke_blog_host="blog.seonuke.ru";
	static $javaproj_host="javaproj.megawall.ru",$shop_host="shop.megawall.ru";
	static $no_index=array(25);
	static $metrika_id=array("megawall.ru"=>3792241, "seonuke.ru"=>13605623);
}
if ($_SERVER['REMOTE_ADDR']=="93.100.152.70"||$_COOKIE['debug']==true){
	vars::$debug=true;
}
