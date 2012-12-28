<?php
$link_id=h2i($_POST[link_id]);
$link=new Link($link_id);
$texts=my_s(explode("\n", $_POST[texts]));
if(Host::get_author_id($link->host_id)==User::get_c_id()){
	my_q("DELETE from link_text where link_id=$link_id");
	for($i=0; $i<count($texts); $i++){
		if($texts[$i]){
			my_in("link_text:text=$texts[$i],link_id=$link_id");
		}
	}
	print "msg=OK&stat=ok";
}else{
	print "Это не ваша ссылка.";
}