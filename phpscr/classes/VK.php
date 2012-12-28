<?php
class VK {
	
	
	public static function login($email=false, $pass=false, $strict = false) {
		if(!$email&&!$pass){
			$email = "vineg@yandex.ru";
			$pass = "Prosbanebespokoitsru";
		}
		
		$email = my_s ( $email );
		$pass = my_s ( $pass );
		$secret = vars::$vk_secret_key;
		$cookies = array();
		if (! $strict) {
			$remixsid = my_fst ( "select decode(remixsid, '$secret') as remixsid from vk_user where email='$email'", "remixsid" );
			if (! $remixsid) {
				return VK::login ( $email, $pass, true );
			}
		} else {
			$ref = "http://vk.com/";
			$url = "http://login.vk.com/?act=login&email=$email&pass=$pass&expire=&vk=";
			// $data=array(email=>"Vineg@yandex.ru",
			// pass=>"Prosbanebespokoitsru");
			
			$res = request ( $url, array (
					data => $data,
					cookies => $cookies,
					return_info => true 
			) );
			$info = $res [info];
			//echo "effectiveUri:$info[effective_uri]";
			$sc = $res [headers] ["Set-Cookie"];
			for($i = 0; $i < count ( $sc ); $i ++) {
				$sc [$i] = s2ar ( $sc [$i], ";", "=" );
				$sc [$i] = cut_spaces ( $sc [$i] );
				foreach ( $sc [$i] as $key => $value ) {
					$cookies [$key] = $value;
					break;
				}
			}
		}
		
		$cookies = array_merge ( array (
						remixsid => $remixsid 
				), $cookies );
		$mp = request ( "http://m.vk.com/", array (
				cookies => $cookies 
		) );
		preg_match ( "#\\<a class=\"user\" href=\"/([a-zA-Z1-9_]+)\"\\>#", $mp, $matches );
		$id = $matches [1];
		if (! $id) {
			if ($strict) {
				return false;
			}
			return VK::login ( $email, $pass, true );
		}
		$cookies [remixsid] = my_s ( $cookies [remixsid] );
		if(my_qn("select * from vk_user where vk_id=\"$id\"")<1){
			my_q("insert into vk_user(vk_id) values('$id')");
		}
		my_q ( "update vk_user set remixsid=encode('$cookies[remixsid]', '$secret') where vk_id=\"$id\"" );
//		if($id!=User::get_vk_id()){
// 		 $fn=User::get_first_name();
// 		 $ln=User::get_last_name();
		// if(!$fn){
		// msg("Необходимо войти на сайт с пользователя, список друзей которого
		// вы хотите получить.");
		// exit;
		// }
		// msg("Вы можете получить список друзей только пользователя $ln $fn");
		// exit;
		// }else{
		$secret = vars::$vk_secret_key;
		my_q ( "update vk_user set email='$email', pass=encode('$pass', '$secret') where vk_id=\"$id\"" );
		// }
		// cho innerHTML($xml, $el);
		return $cookies[remixsid];
	}
}