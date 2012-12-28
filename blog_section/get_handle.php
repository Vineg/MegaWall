<?php
if($_GET["sort"]){
	//	if($_GET["sort"]==$_SESSION["sort"]){
	//		$_SESSION["sorta"]=$_SESSION["sorta"];
	//	}else{
	//		$_SESSION["sorta"]=false;
	//	}
	$_SESSION[sorta]=$_GET[inc]!==null?true:false;
	if($_GET["sort"]=="rate"||$_GET["sort"]=="date"||$_GET["sort"]=="daily_rate"){
		$_SESSION["sort"]=$_GET["sort"];
	};
	//if($_SERVER['REQUEST_URI']==$_SERVER["HTTP_REFERER"]){loc("/");}
	//loc($_SERVER["HTTP_REFERER"]);
}
$ftree=Page::get_ftree();
if(!$ftree[0]&&!Page::get_file()){
	if($_GET[r]!=false){
		$ip=$_SERVER["REMOTE_ADDR"];
		if(!my_qn("select * from guest where ip='$ip'")){
			my_q("insert into guest(ip) values ('$ip')");
			$ratead=st_vars::$rate_ref;
			my_q("update user set rate=rate+$ratead");
		}
	}
}

if($_GET["inv"]){
	$parent_id=User::get_invite_user_id($_GET["inv"]);
	if(User::get_id()&&!User::get_parent()&&$parent_id){
		if(User::get_rate()<st_vars::$rate_invited_user){
			$nrate=st_vars::$rate_invited_user;
			$uid=User::get_id();
			my_q("update user set rate=$nrate where id=$uid");
			my_q("update user set parent_id=$parent_id where id=$uid");
		}
	}else{
		$sl=vars::$lt==1?".".vars::$host:null;
		setcookie("invite", $_GET["inv"], time()+60*60*24*365, "/", $sl);
	}
}
?>