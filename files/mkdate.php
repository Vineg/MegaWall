<?php
if(!vars::$debug){exit;}
$q=my_q("select * from archive where idate=0");
for($i=0; $i<my_n($q); $i++){
	//$date=my_r($q, $i, "date");
	//$itime=strtotime($date);
	my_up("archive:idate=$itime:date='$date'");
	//cho date2unixstamp(my_r($q, $i, "date"));
	//my_up($s)
}