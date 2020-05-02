<?php
$sms_username	= 'YOUR USERNAME';
$sms_password	= 'YOUR PASSWORD';
$sms_number		= 'YOUR SMS NUMBER';

require_once('nusoap.php'); 

$client = new soapclient_nu('http://mihansmscenter.com/webservice/?wsdl', 'wsdl');
$client->decodeUTF8(false);

//send a message to a number
$res = $client->call('send', array(
	'username'	=> $sms_username, 
	'password'	=> $sms_password, 
	'to'		=> '09xxxxxxxxx', 
	'from'		=> $sms_number, 
	'message'	=> 'MESSAGE CONTENT GOES HERE',
	'send_time'	=> strtotime('2009-09-17 15:50') // set this parameter to null if you dont want to schedule message
	));

if (is_array($res) && isset($res['status']) && $res['status'] === 0) {
	echo "message successfully sent.";
} else echo "Error :".@$res['status_message'];
	
//send a message to several numbers
$res = $client->call('multiSend', array(
	'username'	=> $sms_username, 
	'password'	=> $sms_password, 
	'to'		=> array('09xxxxxxxxx', '09xxxxxxxxx', '09xxxxxxxxx'), //array of numbers
	'from'		=> $sms_number, 
	'message'	=> 'MESSAGE CONTENT GOES HERE'
	));

if (is_array($res) && isset($res['status']) && $res['status'] === 0) {
	echo "message successfully sent.";
} else echo "Error :".@$res['status_message'];

?>