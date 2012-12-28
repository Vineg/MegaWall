<?php

//-----------------------------------------
// MSNPAuth, v1.1.3
// by Daniel Winter
// http://www.fanatic.net.nz/
// 
// Copyright © 2003-2007 Daniel Winter (daniel@fanatic.net.nz)
// 
// Last reviewed: July 29th, 2007
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
	var $_cURL = NULL;
	var $_key = NULL;
	var $_authURL = NULL;
	var $_passport = NULL;
	var $_password = NULL;
	var $_challenge = NULL;

	function MSNPAuth($passport, $password, $challenge)
	{
		$strpos = strpos($passport, '@');
		$domain = substr($passport, $strpos);

		switch ($domain)
		{
			case '@hotmail.com':
				$this->_authURL = 'https://loginnet.passport.com/login2.srf';
				break;

			case '@msn.com':
				$this->_authURL = 'https://msnialogin.passport.com/login2.srf';
				break;

			default:
				$this->_authURL = 'https://login.passport.com/login2.srf';
				break;
		}

		$this->_cURL = curl_init();
		$this->_passport = $passport;
		$this->_password = $password;
		$this->_challenge = $challenge;
	}

	function read_header($cURL, $header)
	{
		if (strpos($header, ':') === false)
		{
			return strlen($header);
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

		return strlen($header);
	}

	function getKey()
	{
		// Set some options.
		// I've moved this from the constuctor so CURLOPT_HEADERFUNCTION can be set properly.
		// See the comments at http://www.php.net/register_shutdown_function for more information on using objects in callbacks
		curl_setopt($this->_cURL, CURLOPT_URL, $this->_authURL);
		curl_setopt($this->_cURL, CURLOPT_HEADERFUNCTION, array(&$this, 'read_header'));
		curl_setopt($this->_cURL, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($this->_cURL, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->_cURL, CURLOPT_HTTPHEADER, Array('Authorization: Passport1.4 OrgVerb=GET,OrgURL=http%3A%2F%2Fmessenger%2Emsn%2Ecom, sign-in='.str_replace('@', '%40', $this->_passport).',pwd='.urlencode($this->_password).','.$this->_challenge));
		curl_setopt($this->_cURL, CURLOPT_HEADER, 0);

		// This is to get around times when the auth server has a bad SSL cert and will not authenticate.
		curl_setopt ($this->_cURL, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_exec($this->_cURL);

		curl_close($this->_cURL);

		return $this->_key;
	}
}

?>
