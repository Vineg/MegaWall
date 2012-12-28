<?php
post_yandextop();
update($cdate);
function update($cdate){
	$ucnt=get_users_cnt_rc();
	$quer="update post set daily_rate=daily_rate/(".(1+sqrt(1+$ucnt)).")";
	mysql_query($quer);
	$quer="TRUNCATE TABLE vote";
	mysql_query($quer);
	$quer="update post set a=0 where 1;";
	mysql_query($quer);
	$quer="update var set value='$ucnt' where name='users_cnt'";
	mysql_query($quer);
	$quer="update var set value='$cdate' where name='last_update'";
	mysql_query($quer);
	update_archive();
}

function update_archive(){
	$tres=my_q("select id from type where pub>=1");
	for($i=0; $i<my_n($tres); $i++){
		$arc=array();
		$types_ar=null;
		$ot="";
		$arcs="";
		$tid=my_r($tres, $i, "id");
		$types_ar=Type::get_all_childs($tid);
		$types_ar[0]=h2s($types_ar[0]);
		for($j=1; $j<count($types_ar); $j++){
			$types_ar[$j]=h2s($types_ar[$j]);
			$ot.="or type_id='$types_ar[$j]'";
		}
		$type_f=$types_ar[0]!==null?"and (type_id='$types_ar[0]' $ot)":"";
		$pres=my_q("select id from post where pub=1 and parent=0 $type_f order by daily_rate desc limit 0,100");
		for($j=0; $j<my_n($pres); $j++){
			$arc[$j]=my_r($pres, $j, "id");
		}
		if(count($arc)<1){
			continue;
		}
		$arcs=implode(" ", $arc);
		my_q("insert into archive(date, type_id, posts) values(CURDATE(), '$tid', '$arcs');");
		print "insert into archive(date, type_id, posts) values(CURDATE(), '$tid', '$arcs');";
	}
	$svars["archanged"]=true;
	update_svars($svars);
}

function get_users_cnt_rc(){
	$q=my_q("select id from user");
	return mysql_num_rows($q);
}

function check_users(){
	$minurate=st_vars::$rate_user_posts_remove;
	$rate_posts_remove=st_vars::$rate_post_auto_remove;
	$q=my_q("select * from user where rate<=$minurate");
	for($i=0; $i<my_n($q); $i++){
		$cuid=my_r($q, $i, "id");
		my_q("update post set pub=-2 where author_id=$cuid and rate<=$rate_posts_remove;");
	}
}