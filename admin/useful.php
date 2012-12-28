<?php 
encoding_fix();
?>
<form action="" method=GET><input name=pid /> pid2id:<input name=pid2id value=1 type="checkbox" /></form>
<?php
print $_GET[pid2id]?pid2id($_GET[pid]):id2pid($_GET[pid]);
if(User::get_rate()>st_vars::$rate_admin){
// 	if($_GET[mysql]){
// 		my_q()
// 	}
}
?>
<br />
<pre>
<?php 
$q = my_q("select * from log");
for($i=0; $i<my_n($q); $i++){
	$msg = my_r($q, $i, "msg");
	$date = bdate(my_r($q, $i, "date"));
	print("$date $msg\n");
}
?>
</pre>