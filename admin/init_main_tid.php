<?php
$q=my_q("select * from post where main_type_id=0");
for($i=0; $i< my_n($q); $i++){
	$id = my_r($q, $i, "id");
	$tid = my_r($q, $i, "type_id");
	$mtid = Type::get_main_type_id($tid);
	my_up("post:main_type_id=$mtid:id=$id");
}