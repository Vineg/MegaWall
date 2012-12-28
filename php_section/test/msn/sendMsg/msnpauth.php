<?php

//-----------------------------------------
// msnpauth.php, v1.2.1
// by Daniel Winter
// http://www.msnfanatic.com/
// 
// Copyright © 2003-2005 Daniel Winter
// 
// http://www.danielwinter.info/contact.php or mailto:daniel@msnfanatic.com
// 
// Last reviewed: October 1st, 2005
// 
//-----------------------------------------
//
// Usage: 
// 
// 	$msnpauth = new MSNPAuth($passport, $password, $params[4]);
// 	$hash = $msnpauth->getKey();
// 
// $passport would be the e-mail address that is being signed in
// $password would be the password for that account
// $params[4] would be the string sent in the USR command from the notification server,
// 
// With the last argument, the notification server communication goes like this:
// 
// 	[SEND] USR 6 TWN I bob@hotmail.com\r\n
// 	[RECV] USR 6 TWN S lc=1033,id=507,tw=40,fs=1,ru=http%3A%2F%2Fmessenger%2Emsn%2Ecom,ct=1128154104,kpp=1,kv=7,ver=2.1.6000.1,rn=C6D!I5Ic,tpf=41a9cb6f69e60faa44c8570126f045ed\r\n
// 
// You use 4th argument (starting from 0) from the response (ie. lc=1033,id=507,tw=40...)
// Make sure you are actually sending the right string, if you were to split the response into an array, it should look like this:
//
// 	Array
// 	(
// 		[0] => USR
// 		[1] => 2
// 		[2] => TWN
// 		[3] => S
// 		[4] => lc=1033,id=507,tw=40,fs=1,ru=http%3A%2F%2Fmessenger%2Emsn%2Ecom,ct=1128154104,kpp=1,kv=7,ver=2.1.6000.1,rn=C6D!I5Ic,tpf=41a9cb6f69e60faa44c8570126f045ed
// 	)
//
//-----------------------------------------

class MSNPAuth
{
	var $_key;

	function MSNPAuth($passport, $password, $challenge)
	{
		$i = strpos($passport, "@");

		switch (substr($passport, $i))
		{
			case "@hotmail.com":
				$authURL = "ssl://loginnet.passport.com";
				break;

			case "@msn.com":
				$authURL = "ssl://msnialogin.passport.com";
				break;

			default:
				$authURL = "ssl://login.passport.com";
				break;
		}

		$fp = fsockopen($authURL, 443);

		$req = array();
		$data = '';

		$req[] = 'GET /login2.srf HTTP/1.1';
		$req[] = 'Authorization: Passport1.4 OrgVerb=GET,OrgURL=http%3A%2F%2Fmessenger%2Emsn%2Ecom, sign-in='.str_replace('@', '%40', $passport).',pwd='.urlencode($password).','.$challenge;
		$req[] = 'Host: login.passport.com';
		$req[] = 'Connection: Close';

		foreach ($req as $v)
		{
			fputs($fp, "$v\r\n");
		}

		fputs($fp, "\r\n");

		//-----------------------------------------
		// fgets() is error suppressed to work around this bug: http://bugs.php.net/bug.php?id=23220 
		// 
		// During development, I always got the following: 
		//  - Warning: fgets(): SSL: fatal protocol error in /home/fanatics/public_html/x/class/MSNPAuth.class.php on line 53
		//-----------------------------------------

		while (!feof($fp))
		{
			$data .= @fgets($fp);
		}

		fclose($fp);

		$headers = explode("\r\n", $data);

		foreach ($headers as $header)
		{
			if (strpos($header, ':') === false)
			{
				continue;
			}

			list($name, $value) = explode(':', $header);

			switch ($name)
			{
				case 'WWW-Authenticate':
					// Authentication failed
					$this->_key = false;

					break;

				case 'Authentication-Info':
					// Get the key between the two single quotes
					$start = (strpos($value, "'") + 1);
					$end = (strrpos($value, "'") - $start);

					$this->_key = substr($value, $start, $end);

					break;
			}
		}
	}

	function getKey()
	{
		return $this->_key;
	}
}

?>
