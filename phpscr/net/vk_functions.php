<?php
function get_page($id){
	$page=is_int($id)?"id$id":$id;
	$remixsid = VK::login();
	$s = request("http://vk.com/$page", array(cookies=>array(remixchk=>5,remixsid=>$remixsid)));
	$s = mb_convert_encoding($s, "UTF8", "windows-1251");
	return $s;
}

function get_sex($page){
	$s = $page;
	$full_name = preg_match_all("#отправить ([а-яА-ЯёЁ]+) сообщение#u", $s, $matches);
	$name = $matches[1][0];
	//cho $name;
	return preg_match("#([а-яА-ЯёЁ]+)[ую]#u", $s);
}