<?php 
//notification trigger
$user->notification->setNotificationTrigger(function($param){
	echo "\n\n\nNotification: ";
	var_dump($param);
});


$user->notification->setMessageTrigger(function($param){
	echo "\n\n\nMessage: ";
	var_dump($param);
});


$user->profile->getName(1);


?>