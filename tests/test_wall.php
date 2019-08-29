<?php 
include "../index.php";


$posts=$user->wall->posts();

// var_dump("---------------RANDOM POST COMMNETS");
$post=$posts[random_int(0,count($posts)-1)];
for ($i=0; $i <4 ; $i++) { 
	var_dump("-------page".$i);
	$cmts=$post->comments($i);
	foreach ($cmts as $cmt) {
		var_dump("-author: ".$cmt->getUser()->getId());
		var_dump("---content: ".flatContent($cmt->getContent()));
		var_dump("---likes number: ".$cmt->getLikes("length"));
	}
}

var_dump("ID: ".$post->getId());







 ?>	