<?php

error_reporting(E_ALL);

include('../../test/msn/sendMsg/sendMsg.phpp_section/test/sendMsg.php');

$tags['INFO'] = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$tags['SIGNINNAME']	= ' value="'.$_POST['sender'].'"';
	$tags['RECIPIENT']	= ' value="'.$_POST['recipient'].'"';
	$tags['MESSAGE']	= ' value="'.$_POST['message'].'"';

	$sendMsg = new sendMsg();

	$sendMsg->login($_POST['sender'], $_POST['password']);
	$sendMsg->createSession($_POST['recipient']);
	$sendMsg->sendMessage($_POST['message'], 'Times New Roman', 'FF0000');

	switch ($sendMsg->result)
	{
		case ERR_AUTHENTICATION_FAILED:
			$tags['INFO'] = 'Invalid passport and/or password.';

			break;

		case ERR_SERVER_UNAVAILABLE:
			$tags['INFO'] = 'Something went wrong trying to connect to the server.';

			break;

		case ERR_USER_OFFLINE:
			$tags['INFO'] = 'The user appears to be offline.';

			break;

		case OK:
			$tags['INFO'] = 'The message was successfully sent.';

			break;

		default:
			$tags['INFO'] = $sendMsg->error;

			break;
	}
}

// Load the template
$content = file_get_contents('template.tpl');

// Replace the template variables
foreach ($tags as $tagname => $tagvalue)
{
	$content = str_replace('{'.$tagname.'}', $tagvalue, $content);
}

print $content;

?>
