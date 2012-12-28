<?php

require_once('msnpauth.php');

define('ERR_AUTHENTICATION_FAILED', 911);
define('ERR_SERVER_UNAVAILABLE', 600);
define('ERR_USER_OFFLINE', 217);
define('ERR_TOO_MANY_SESSIONS', 800);
define('OK', 1);

class sendMsg
{
	var $_passport;
	var $_password;
	var $_account;
	var $_sockets;
	var $_msg;
	var $result;
	var $error;

	function simpleSend($passport, $password, $recipient, $message)
	{
		$this->login($passport, $password);

		if ($this->result > 1)
		{
			return;
		}

		$this->createSession($recipient);
		$this->sendMessage($message);
	}

	function login($passport, $password)
	{
		$this->_passport = $passport;
		$this->_password = $password;

		$this->_connect('NS');

		$this->_send_data('NS', 'VER 0 MSNP8');

		$this->_read();
	}

	function createSession($account)
	{
		// Stop if login failed.
		if ($this->result > 1)
		{
			return;
		}

		$this->_account = $account;

		$this->_send_data('NS', 'XFR 5 SB');

		$this->_read();
	}

	function sendMessage($message, $font = NULL, $color = NULL)
	{
		// Stop if login failed.
		if ($this->result > 1)
		{
			return;
		}

		$head = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";

		if (isset($font))
		{
			$font = rawurlencode($font);

			$head .= "X-MMS-IM-Format: FN=$font; EF=; CO=$color; CS=0; PF=22\r\n";
		}

		$head .= "\r\n";

		$len = strlen($head.$message);

		$this->_send_data('SB', "MSG 3 U $len\r\n$head$message");

		$this->result = OK;
	}

	function _connect($socket, $server = 'messenger.hotmail.com')
	{
		// This part does a DNS lookup on the server, and fails with error if it is not found.
		// fsockopen doesn't return an error on DNS lookup failures, instead it outputs a warning (which we want to suppress).
		if ((gethostbyname($server) == $server) && !is_numeric(str_replace('.', '', $server)))
		{
			$this->result = ERR_SERVER_UNAVAILABLE;
			$this->error = 'Host not found'.$server;

			return false;
		}

		ini_set('default_socket_timeout', 2);

		$this->_sockets[$socket] = @fsockopen($server, 1863, $errno, $errstr, 2);

		if ($this->_sockets[$socket] == false)
		{
			$this->result = $errno;
			$this->error = $errstr;

			return false;
		}

		return true;
	}

	function _read($socket = 'NS')
	{
		$r = false;

		while ($this->_sockets[$socket] && !feof($this->_sockets[$socket]) && !$r)
		{
			$data = fgets($this->_sockets[$socket], 1024);

			if (!$data)
			{
				continue;
			}

			$data = substr($data, 0, -2);

			$r = $this->_process_data($data);

			if ($r)
			{
				return;
			}
		}
	}

	function _send_data($socket, $data)
	{
		if (substr($data, 0, 3) == 'MSG')
		{
			// Don't send appending new line if it's a payload command. (MSG)
			fputs($this->_sockets[$socket], $data);
		}
		else
		{
			fputs($this->_sockets[$socket], "$data\r\n");
		}
	}

	function _process_data($data)
	{
		$params = explode(' ', $data);

		switch ($params[0])
		{
			case 'VER':
				$this->_send_data('NS', 'CVR 1 0x0409 winnt 5.1 i386 MSNMSGR 6.0.0254 MSMSGS '.$this->_passport);

				break;

			case 'CVR':
				$this->_send_data('NS', 'USR 2 TWN I '.$this->_passport);

				break;

			case 'XFR':
				$subParams = explode(':', $params[3]);

				$r = $this->_connect($params[2], $subParams[0]);

				if (!$r)
				{
					return true;
				}

				if ($params[2] == 'NS')
				{
					$this->_send_data('NS', 'VER 0 MSNP8');
				
				}
				elseif ($params[2] == 'SB')
				{
					$this->_send_data('SB', 'USR 1 '.$this->_passport.' '.$params[5]);
				}

				$this->_read($params[2]);

				return true;

				break;

			case 'USR':
				if ($params[2] == 'TWN')
				{
					$msnpauth = new MSNPAuth($this->_passport, $this->_password, $params[4]);
					$hash = $msnpauth->getKey();

					if (!$hash)
					{
						$this->result = ERR_AUTHENTICATION_FAILED;
						$this->_send_data('NS', 'OUT');

						return false;
					}

					$this->_send_data('NS', 'USR 3 TWN S '.$hash);

				}
				elseif ($params[2] == 'OK')
				{
					if (count($params) == 7)
					{
						$this->_send_data('NS', 'CHG 4 NLN');

						return true;

					}
					else
					{
						$this->_send_data('SB', 'CAL 0 '.$this->_account);
					}
				}

				break;

			case 'JOI':
				return true;

				break;

			// Error code handling

			case '500':
			case '600':
			case '601':
			case '910':
			case '911':
			case '921':
			case '928':
				$this->result = ERR_SERVER_UNAVAILABLE;

				return true;

				break;

			case  '800':
				$this->result = ERR_TOO_MANY_SESSIONS;

				return true;

				break;

			case '217':
				$this->result = ERR_USER_OFFLINE;

				return true;

				break;
		}

		return false;
	}
}

?>
