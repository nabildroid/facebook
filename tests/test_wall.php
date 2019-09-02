<?php 

use Facebook\Comment\Comment;
use Facebook\Wall\Wall;
use Facebook\Post\Post;
use Facebook\Utils\Content;

$wall=$user->wall;
##get posts from account wall
// $posts=$wall->posts();
// var_dump("---------------RANDOM POST COMMNETS");
// foreach ($posts as $post) {
// 	// echo "\n\n\n\n";
// 	var_dump("####POST_ID: ".$post->getId());
// 	print_r($post->getContent());
// 	echo "\n\n\n";
// 	// for ($i=0; $i <1 ; $i++) { 
// 	// 	var_dump("-------page".$i);
// 	// 	$cmts=$post->comments($i);
// 	// 	foreach ($cmts as $cmt) {
// 	// 		var_dump("-author: ".$cmt->getUser()->getId());
// 	// 		var_dump("--------------");
// 	// 		var_dump(flatContent($cmt->getContent()));
// 	// 		var_dump("--------------");
// 	// 		var_dump("likes number: ".$cmt->getLikes("length"));
// 	// 	}
// 	// }
// }

#publish
##text
// $myPost=$wall->publish([
// 	"text"=>time()
// ]);
##image
####one image
// $myPost=$wall->publish([
	// "images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59"]
// ]);
####two image
// $myPost=$wall->publish([
// 	"images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59","https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69584199_370439857184399_5708920372938670080_n.jpg?_nc_cat=109&efg=eyJpIjoiYiJ9&_nc_eui2=AeGxaIWOMqDzgc2VVkB5H_Udk7j4KbesAzV8OtYYnKvDDNLMeA6XnUU8ke147fYp32MVZdwnum8rTsmdiQymcUPxsVr83xRFf2QKgDSS8igbAw&_nc_oc=AQk6XQzWTCa7BpE-0r0RG2xOdYKr2l4UZ-yusGnDxQfIF0pXjSexHjtIJbUBGjDk3YQ&_nc_ht=scontent-mrs2-1.xx&oh=10500937c0484d734b5c946114860aef&oe=5DCE7910"]
// ]);
##text with privacy
// $myPost=$wall->publish([
// 	"text"=>time(),
// 	"privacy"=>"only me"
// ]);
##tag friend
// $myPost=$wall->publish([
// 	"text"=>time(),
// 	"tags"=>[new Profile($user,100011210498274),new Profile($user,"kutibo.alkino.7")],
// 	"privacy"=>"friends"
// ]);

##everythings
$myPost=$wall->publish([
	"text"=>"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
	"images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59","https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69584199_370439857184399_5708920372938670080_n.jpg?_nc_cat=109&efg=eyJpIjoiYiJ9&_nc_eui2=AeGxaIWOMqDzgc2VVkB5H_Udk7j4KbesAzV8OtYYnKvDDNLMeA6XnUU8ke147fYp32MVZdwnum8rTsmdiQymcUPxsVr83xRFf2QKgDSS8igbAw&_nc_oc=AQk6XQzWTCa7BpE-0r0RG2xOdYKr2l4UZ-yusGnDxQfIF0pXjSexHjtIJbUBGjDk3YQ&_nc_ht=scontent-mrs2-1.xx&oh=10500937c0484d734b5c946114860aef&oe=5DCE7910"],
	"privacy"=>"public"
]);
var_dump($myPost->getId());




 ?>	
</pre>