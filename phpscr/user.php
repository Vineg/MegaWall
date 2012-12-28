<?php
class User {
	private static $get_rate=null;
	private static $get_login=null;
	private static $check=null;
	private static $get_s_id=null;
	private static $get_id=array();
	static $get_c_id=null;
	private static $num_users=null, $get_vars=null;
	
	static function set_id($id){
		$get_c_id = $id;
	}

	static function get_vk_email($uid=false){
		if(!$uid){
			$uid=User::get_c_id();
			if(!$uid){
				return false;
			}
		}
		$key=vars::$vk_secret_key;
		return my_fst("select decode(email, '$key') as email from vk_user where user_id=$uid", "email");
	}

	static function get_vk_pass($uid=false){
		if(!$uid){
			$uid=User::get_c_id();
			if(!$uid){
				return false;
			}
		}
		$key=vars::$vk_secret_key;
		return my_fst("select decode(pass, '$key')as pass from vk_user where user_id=$uid", "pass");
	}

	static function get_first_name($uid=false){
		if(!$uid){
			$uid=self::get_c_id();
		}
		if(!$uid){
			return false;
		}
		return my_fst("select first_name from vk_user where user_id=$uid", "first_name");
	}

	static function get_last_name($login=false){
		$uid=self::get_id($login);
		if(!$uid){
			return false;
		}
		return my_fst("select last_name from vk_user where user_id=$uid", "last_name");
	}

	static function get_vk_id($login=false){
		$uid=self::get_id($login);
		if(!$uid){
			return false;
		}
		return my_fst("select vk_id from vk_user where user_id=$uid", "vk_id");
	}

	static function get_parent($id=false){
		if(!$id){
			$id=User::get_c_id();
		}
		return my_fst("select parent_id from user where id='$id'", "parent_id");
	}

	static function get_invite_user_id($invite){
		$invite=my_s($invite);
		$invite_user_id=my_fst("select user_id from invite where invite='$invite'", "user_id");
		$needrate=($num_invited_users)*st_vars::$rate_per_invited_user;
		if(User::get_rate($invite_user_id)<$needrate){
			return false;
		}else{return $invite_user_id;
		}
	}

	static function get_submit_secret($id=false){
		if(!$id){
			$id=User::get_c_id();
		}
		$res = my_fst("select submit_secret from user where id='$id'", "submit_secret");
		if(!$res){
			$res = md5($id."usecret".vars::$secret);
			my_up("user:submit_secret=$res:id=$id");
		}
		return $res;
	}

	static function get_notification_post_id($id=false){
		if(!$id){
			$id=User::get_c_id();
		}
		return explode(" ", my_fst("select notification_post_id from user where id='$id'", "notification_post_id"));
	}

	static function get_remixsid($id=false){
		if(!$id){
			$id=User::get_c_id();
		}
		return my_fst("select remixsid from user where id='$id'", "remixsid");
	}

	static function get_login($user_id=-1){
		if($user_id==-1){
			return self::get_c_login();
		}
		$get_login=&self::$get_login;
		if($user_id==false){
			return false;
		}
		//cho "userid$user_id";
		if($get_login[$user_id]==null){
			require_once "settings/mysql_connect.php";
			require_once "phpscr/shortcuts.php";
			//cho "user_id $user_id";
			$user_id=h2s($user_id);
			$qry = mysql_query("select login from user where id = '$user_id'");
			$get_login[$user_id]=my_fst($qry,"login");
		}
		return $get_login[$user_id];
	}

	static function get_c_login(){
		$user_id=self::get_id();
		if(!$user_id){
			return false;
		}

		if($_SESSION["login"]==null){
			require_once "settings/mysql_connect.php";
			require_once "phpscr/shortcuts.php";
			$user_id=h2s($user_id);
			$qry = mysql_query("select login from user where id = '$user_id'");
			if(mysql_num_rows($qry)<1){
				self::logout();
			}
			$_SESSION["login"]=mysql_result($qry, 0, "login");
		}
		return $_SESSION["login"];
	}


	static function logout(){
		self::clean_cookies();
		$id=User::get_id();
		unset($_SESSION);
		save_session($id);
		sc("id",null);
		sc("remixsid",null);
		session_destroy();
		$user_id=false;
		loc_back();
	}

	static function clean_cookies(){
		//cho 333;
		$sl=".".vars::$host;
		setcookie("id",null, time()+60*60*24*365, "/", $sl);
		setcookie("remixsid",null, time()+60*60*24*365, "/", $sl);
		setcookie("id",null, time()+60*60*24*365, "/");
		setcookie("remixsid",null, time()+60*60*24*365, "/");
	}


	static function get_rate($user_id=-1){
		if($user_id==-1){
			return self::get_c_rate();
		}
		if($user_id==0){
			return -1;
		}
		$get_rate=&self::$get_rate;
		if($get_rate[$user_id]==null){
			require_once "settings/mysql_connect.php";
			require_once "phpscr/shortcuts.php";
			$user_id=h2s($user_id);
			$qry = mysql_query("select rate from user where id = '$user_id'");
			$get_rate[$user_id]=my_r($qry, 0, "rate");
		}
		return $get_rate[$user_id];
	}

	static function get_u_link($login=false){
		if(!$login){
			$login=self::get_login();
		}
		$lk=self::get_url($login);
		{
			return "<a href='$lk'>$login</a>";
		}

	}

	static function get_u_link_id($user_id=false){
		$login=self::get_login($user_id);
		return self::get_u_link($login);
	}

	static function get_c_rate(){
		$user_id=self::get_id();
		return self::get_rate($user_id);
	}

	static function get_s_id(){
		$get_s_id=&self::$get_s_id;
		if($get_s_id===null){
			if(!$_COOKIE["id"]){
				return false;
			}
			if(self::check()==false){
				self::clean_cookies();
				return false;
			}
			$id = $_COOKIE['id'];
			$get_s_id=$id;
		}
		return $get_s_id;
	}

	static function get_id($login=""){
		if($login==""){
			return self::get_c_id();
		}
		$get_id=&self::$get_id;
		if($get_id[$login]===null){
			$login=h2s($login);
			$get_id[$login] = my_fst("select id from user where login='$login'", "id");
		}
		return $get_id[$login];
	}

	static function get_c_id(){
		global $vars;
		if($vars[uid]){
			return $vars[uid];
		}
		$get_c_id=&self::$get_c_id;
		if($get_c_id===null){
			$id =self::get_s_id();
			if($id==""){
				$get_c_id = false;
			}else{
				$get_c_id = $id;
			}
		}
		return $get_c_id;
	}

	static function re_vars(){
		self::$check=null;
		self::$get_c_id=null;
		self::$get_id=array();
		self::$get_login=null;
		self::$get_rate=null;
		self::$get_s_id=null;
		self::$num_users=null;
	}

	static function check(){
		require_once 'sys/vars.php';
		if(!$_COOKIE[id]){
			return false;
		}
		//cho $_COOKIE["remixsid"]."!=".my_f("select remixsid from user where id =$_COOKIE[id]", "remixsid");
		return my_fst("select remixsid from user where id=$_COOKIE[id]", "remixsid")==$_COOKIE["remixsid"];
	}

	static function num_users(){
		$num_users=&self::$num_users;
		if($num_users===null){
			$num_users=get_svar("users_cnt");
		}
		return $num_users;
	}

	static function get_url($uname=""){
		$h=vars::$host;
		if(!$uname){
			$uname=User::get_login();
		}
		require_once 'sys/vars.php';
		if(vars::$lt==1){
			return "http://$h/user/$uname/";
		}else{
			return "http://$h/user/$uname/";
		}
	}

	static function get_link($login=""){
		return self::get_u_link($login);
	}

	static function get_link_id($id=false){
		return self::get_u_link_id($id);
	}

	static function make_link($link, $uname=""){
		$h=vars::$host;
		$uname=s2link::translit($uname);
		if(!$uname){
			$uname=User::get_login();
		}
		require_once 'sys/vars.php';
		if(vars::$lt==1){
			if(substr($link, 0, 7)=="http://"){
				return "http://".$uname.".".substr($link, 7);
			}else if(substr($link, 0, 1)=="/"){
				return "http://$uname.$h$link";
			}else{
				return "http://$u.$link";
			}
		}else{
			return "$link?u=$uname";
		}
	}

	static function get_invited_users_num($id=false){
		if(!$id){
			$id=User::get_c_id();
		}
		return my_qn("select * from user where parent_id=$id");
	}

	static function is_page_admin($id=false){
		if(!$id){
			$id=User::get_c_id();
		}
		return ($id&&$id==Page::get_page_user_id())?true:false;
	}

	static function exists($user){
		$user=my_s($user);
		return my_qn("select * from user where login='$user'");
	}

	static function get_by_hash($hash){
		$hash=my_s($hash);
		return my_fst("select * from user where linkproj_user='$hash'", "id");
	}

	static function get_key($uid=false){
		if(!$uid){
			$uid=User::get_id();
		}
		return User::get_login($uid).md5(User::get_login($uid).vars::$secret);
	}
	
	static function get_hash($uid=false){
		return self::get_key($uid);
	}
	
	static function get_linkproj_secret($uid=false){
		if(!$uid){
			$uid=User::get_id();
		}
		return md5(User::get_hash().User::get_c_login()."gieirglqwrjlkngfsj");
	}
	
	static function get_upload_secret($uid = false){
		return self::get_submit_secret($uid);
	}
	
	static function create_anonimous(){
		require_once 'phpscr/reg.php';
		$uname = self::gen_nick_from_ip();
		$pass = md5($uname.vars::$secret);
		$id = complete_reg($uname, $pass, "", array("auto"=>true));
		return $id;
	}
	
	static function get_anonimous_id(){
		$uname = self::gen_nick_from_ip();
		$uid=User::get_id($uname);
		if($uid){
			return $uid;
		}else{
			return self::create_anonimous();
		}
	}
	
	static function gen_nick_from_ip(){
		$ip = $_SERVER["REMOTE_ADDR"];
		$ips = join("_", explode(".", $ip));
		$uname = "__$ips";
		return $uname;
	}
	
	static function is_admin(){
		return User::get_rate()>=st_vars::$rate_admin;
	}
}
?>