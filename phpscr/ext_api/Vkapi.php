<?php

class VKapi
{
	function __construct($api_secret=false, $api_id=false)
	{
		if(!$api_secret){
			$api_secret = vars::$vk_secret_key;
		}
		if(!$api_id){
			$api_id = vars::$vk_api_id;
		}
		$this->api_secret = $api_secret;
		$this->api_id     = $api_id;
	}

	function getProfiles ($uids)
	{
		$request['fields'] = 'uid,first_name,last_name,nickname,sex,bdate(birthdate),city,country,timezone,photo,photo_medium,photo_big';
		$request['uids']   = $uids;
		$request['method'] = 'secure.getProfiles';
		return $this->request($request);
	}

	function sendNotification ($uids, $message)
	{
		$request['uids']    = $uids;
		$request['message'] = iconv('windows-1251', 'utf-8', $message);
		$request['method']  = 'secure.sendNotification';
		return $this->request($request);
	}

	function saveAppStatus ($uid, $status)
	{
		$request['uid']    = $uid;
		$request['status'] = iconv('windows-1251', 'utf-8', $status);
		$request['method'] = 'secure.saveAppStatus';
		return $this->request($request);
	}

	function getAppStatus ($uid)
	{
		$request['uid']    = $uid;
		$request['method'] = 'secure.getAppStatus';
		return $this->request($request);
	}

	function getAppBalance ()
	{
		$request['method'] = 'secure.getAppBalance';
		return $this->request($request);
	}

	function getBalance ($uid)
	{
		$request['uid']    = $uid;
		$request['method'] = 'secure.getBalance';
		return $this->request($request);
	}

	function addVotes ($uid, $votes)
	{
		$request['uid']    = $uid;
		$request['votes']  = $votes;
		$request['method'] = 'secure.addVotes';
		return $this->request($request);
	}

	function withdrawVotes ($uid, $votes)
	{
		$request['uid']    = $uid;
		$request['votes']  = $votes;
		$request['method'] = 'secure.withdrawVotes';
		return $this->request($request);
	}

	function transferVotes ($uid_from, $uid_to, $votes)
	{
		$request['uid_from'] = $uid_from;
		$request['uid_to']   = $uid_to;
		$request['votes']    = $votes;
		$request['method']   = 'secure.transferVotes';
		return $this->request($request);
	}

	function getTransactionsHistory ()
	{
		$request['method'] = 'secure.getTransactionsHistory';
		return $this->request($request);
	}

	function addRating ($uid, $rate)
	{
		$request['uid']    = $uid;
		$request['rate']   = $rate;
		$request['method'] = 'secure.addRating';
		return $this->request($request);
	}

	function setCounter ($uid, $counter)
	{
		$request['uid']     = $uid;
		$request['counter'] = $counter;
		$request['method']  = 'secure.setCounter';
		return $this->request($request);
	}

	function request($request)
	{
		$request['random']    = rand(100000,999999);
		$request['timestamp'] = time();
		$request['format']    = 'JSON';
		$request['api_id']    = $this->api_id;

		ksort($request);

		foreach ($request as $key=>$value) {
			$str.=trim($key)."=".trim($value);
		}

		$request['sig'] = md5(trim($str.$this->api_secret));

		$q = http_build_query($request);
		$result = json_decode(file_get_contents("http://api.vkontakte.ru/api.php?".$q),TRUE);

		return $result;
	}
}

?>