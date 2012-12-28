<?php
$q=my_q("select * from post where 1");
for($i=0; $i< my_n($q); $i++){
	$id = my_r($q, $i, "id");
	$tid = my_r($q, $i, "type_id");
	if(!my_qn("select * from type where id=$tid")){
		my_q("delete from post where id = $id");
	}
}