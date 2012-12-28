<?php
function vote($obj_id, $vote, $auto=false, $for="post"){
	$user_id=h2i($user_id);
	$obj_id=h2i($obj_id);
	$forar=array(0=>"post", 1=>"post_content");
	$forint=h2i(array_search("$for", $forar));

	$author_id=my_fst("select author_id from $for where id = '$obj_id'", "author_id");
	if($author_id==st_vars::$system_id){
		$post_content=Post::get_content($obj_id, $for==1);
		$sysparam=Post::get_system_param($post_content);
		if($sysparam[key]=="pid"){
			return vote(pid2id($sysparam[value]),$vote,$auto,"post");
		}else if($sysparam[key]=="cid"){
			return vote($sysparam[value],$vote,$auto,"post_content");
		}
	}

	require_once "settings/mysql_connect.php";
	require_once "phpscr/user.php";
	require_once "phpscr/shortcuts.php";
	require_once "sys/vars.php";

	$user_id=User::get_id();
	if(!$user_id){return "Необходимо войти";}

	//cho "user_id:$user_id;post_id:$post_id;";
	if(my_qn("select * from vote where user_id='$user_id'")>st_vars::$max_votes){
		return "Больше нельзя голосовать.";
	}
	if(my_qn("select * from vote where obj='$forint' and user_id='$user_id' and obj_id='$obj_id'")==0){
		$type=get_post_type($obj_id);

		$ua=my_fst("select * from $for where id = '$obj_id'", "a");
		$u_u_votes_num=my_qn("select * from vote where user_id=$user_id and for_user_id=$author_id");

		$a=$vote;
		//$issafe=my_f("select safe from post where id = '$obj_id'", "safe");
		//$a=($issafe=1)?$a:$a*0.5;


		$user_rate=max(User::get_rate($user_id), 0);
		$num_user=User::num_users();
		$add=(log($user_rate+1)+0.1)*0.01*$a;
		$uadd=(0.01)*$ua;
		$aadd=$add/my_sqrt_z($num_user+1.1)/(my_sqrt_z($u_u_votes_num)+1);

		if(!$auto){
			type_vote($type, $add);
			if($user_rate>st_vars::$aadd){
				$a=h2i($a);
				my_q("update $for set a=(a+$a) where id='$obj_id';");
			}
		}
		if($for=="post"){
			$post_id=$obj_id;
		}else{
			$post_id=my_fst("select post_id from post_content where id=$obj_id", "post_id");
		}
		$ppub=Post::get_pub($post_id);
		//cho("author_id$author_id num_user$num_user add$add uadd$uadd aadd$aadd user_rate$user_rate a$a vote$vote user_id$user_id post_id$post_id");
		my_q("update $for set rate=rate+($add) where id ='$obj_id'");
		my_q("update $for set daily_rate=daily_rate+($add) where id ='$obj_id'");
		if($user_id!=$author_id&&$auto!=true&&($ppub>=1||$ua!=0)){
			my_q("update user set rate=(rate+($uadd)) where id='$user_id'");
			my_q("update user set rate=rate+($aadd) where id='$author_id'");
		}
		my_q("insert into vote(user_id, obj_id, obj, for_user_id) values('$user_id', '$obj_id', '$forint', '$author_id')");
		if($forint=="post_content"){
			check_contents($post_id);
		}
		return "1";
	}else{
		return "Вы уже голосовали.";
	}
}

function type_vote($type_id, $add){
	my_q("update type set rate=sqrt(pow(rate,2)+pow($add,2)) where id='$type_id'");
}
?>