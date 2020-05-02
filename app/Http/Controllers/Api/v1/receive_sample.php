<?php
$sms_username = 'YOUR USERNAME';
$sms_password = 'YOUR PASSWORD';

$from		= $_POST['from'];
$to			= $_POST['to'];
$message	= $_POST['message'];
$timestamp	= $_POST['timestamp'];

require_once("nusoap.php");

$client = new soapclient_nu('http://mihansmscenter.com/webservice/?wsdl', 'wsdl');
$client->decodeUTF8(false);

$result = $client->call('verifyReceive', array(
	'username'	=> $sms_username, 
	'password'	=> $sms_password, 
	'to'		=> $to, 
	'from'		=> $from, 
	'message'	=> $message, 
	'timestamp'	=> $timestamp
	));

if (@$result['status'] !== 0) exit();


$handle = fopen('inbox.txt', 'a+');
fwrite($handle, 
	"From: $from\n".
	"To: $to\n".
	"Date: ".date('Y-m-d H:i:s', $timestamp)."\n".
	"Message: $message\n".
	"--------------------------------------------\n");
?>