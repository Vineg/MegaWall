<?php
$text=$_POST["text"];
$uid=User::get_id();
if(!User::get_id()){
	ret("Необходимо войти.");
}
$host=$_POST["host"];
if(Host::get_author_id(Host::get_id($host))!=User::get_id()){
	ret("Это не ваш сайт.");
}
$uri=$_POST["uri"];
$urihost=get_host($uri);
if(!$host||($urihost&&$urihost!=$host)){
	ret("Неправильная ссылка.");
}
$uriid=Link::get_id($uri);
if($_POST["button_name"]=="add"){
	ret(Link::add_text($uriid, $text));
}elseif ($_POST["button_name"]=="rem"){
	ret(Link::rem_text($uriid, $text));
}

//cho http_build_query(array(stat=>"ok", msg=>"64e6", hide=>1));