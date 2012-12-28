<?php

error_reporting(E_ALL);

include('../../../php_section/test/sendMsg.phpmsn/sendMsg/sendMsg.php');

$sendMsg = new sendMsg();

$sendMsg->simpleSend('sender@hotmail.com', 'password', 'recipient@hotmail.com','message');

print $sendMsg->result.' '.$sendMsg->error;

?>
