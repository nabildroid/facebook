<?php 
use Facebook\Message\Message;
use Facebook\Profile\Profile;
use Facebook\Utils\Content;
# read messages
$recent=$user->messages->recent();
$latest=$recent[0];
##friend info
// $friend=$latest->friend;
// var_dump("Fiend ID: ".$friend->getId());

##send message
###text
// $latest->send(["text"=>"Hi"]);
###image
// $latest->send([
	// "images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59","https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69584199_370439857184399_5708920372938670080_n.jpg?_nc_cat=109&efg=eyJpIjoiYiJ9&_nc_eui2=AeGxaIWOMqDzgc2VVkB5H_Udk7j4KbesAzV8OtYYnKvDDNLMeA6XnUU8ke147fYp32MVZdwnum8rTsmdiQymcUPxsVr83xRFf2QKgDSS8igbAw&_nc_oc=AQk6XQzWTCa7BpE-0r0RG2xOdYKr2l4UZ-yusGnDxQfIF0pXjSexHjtIJbUBGjDk3YQ&_nc_ht=scontent-mrs2-1.xx&oh=10500937c0484d734b5c946114860aef&oe=5DCE7910"]
// ]);
###text and image
// $latest->send([
// 	"text"=>time(),
// 	"images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59","https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69584199_370439857184399_5708920372938670080_n.jpg?_nc_cat=109&efg=eyJpIjoiYiJ9&_nc_eui2=AeGxaIWOMqDzgc2VVkB5H_Udk7j4KbesAzV8OtYYnKvDDNLMeA6XnUU8ke147fYp32MVZdwnum8rTsmdiQymcUPxsVr83xRFf2QKgDSS8igbAw&_nc_oc=AQk6XQzWTCa7BpE-0r0RG2xOdYKr2l4UZ-yusGnDxQfIF0pXjSexHjtIJbUBGjDk3YQ&_nc_ht=scontent-mrs2-1.xx&oh=10500937c0484d734b5c946114860aef&oe=5DCE7910"]
// ]);


## chat 

// for ($i=0; $i <2 ; $i++) {
// 	var_dump("##PAGE: ".$i);
// 	$chats=$latest->chat($i);
// 	foreach ($chats as $msg){
// 		var_dump($msg["content"]);
// 	}
// }

#fist conversation
$msg=new Message($user,new Profile($user,100035851089336));
# chat 
for ($i=0; $i <2 ; $i++) {
	var_dump("##PAGE: ".$i);
	$chats=$msg->chat($i);
	foreach ($chats as $msg1){
		var_dump($msg1["content"]);
	}
}
## send text
// $msg->send(["text"=>"Hi"]);
###image
// $msg->send([
// 	"images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59","https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69584199_370439857184399_5708920372938670080_n.jpg?_nc_cat=109&efg=eyJpIjoiYiJ9&_nc_eui2=AeGxaIWOMqDzgc2VVkB5H_Udk7j4KbesAzV8OtYYnKvDDNLMeA6XnUU8ke147fYp32MVZdwnum8rTsmdiQymcUPxsVr83xRFf2QKgDSS8igbAw&_nc_oc=AQk6XQzWTCa7BpE-0r0RG2xOdYKr2l4UZ-yusGnDxQfIF0pXjSexHjtIJbUBGjDk3YQ&_nc_ht=scontent-mrs2-1.xx&oh=10500937c0484d734b5c946114860aef&oe=5DCE7910"]
// ]);

###text and image
// $msg->send([
// 	"text"=>time(),
// 	"images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59","https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69584199_370439857184399_5708920372938670080_n.jpg?_nc_cat=109&efg=eyJpIjoiYiJ9&_nc_eui2=AeGxaIWOMqDzgc2VVkB5H_Udk7j4KbesAzV8OtYYnKvDDNLMeA6XnUU8ke147fYp32MVZdwnum8rTsmdiQymcUPxsVr83xRFf2QKgDSS8igbAw&_nc_oc=AQk6XQzWTCa7BpE-0r0RG2xOdYKr2l4UZ-yusGnDxQfIF0pXjSexHjtIJbUBGjDk3YQ&_nc_ht=scontent-mrs2-1.xx&oh=10500937c0484d734b5c946114860aef&oe=5DCE7910"]
// ]);



 ?>