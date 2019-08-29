<?php 
include "../index.php";


##post from id
$post=new Post($user,2376163555834069);


##like/unlike
$alread_liked=$post->getLikes("mine");
// var_dump("alread_liked:".$alread_liked);
// if($alread_liked)
// 	$post->unlike();
// else $post->like();


##comment
// $post->comment("Hello world".time());

##get comments

var_dump("---------------RANDOM POST COMMNETS");

for ($i=0; $i <4 ; $i++) { 
	var_dump("-------page".$i);
	$cmts=$post->comments($i);
	foreach ($cmts as $cmt) {
		var_dump("-author: ".$cmt->getUser()->getId());
		var_dump("---content: ".flatContent($cmt->getContent()));
		var_dump("---likes number: ".$cmt->getLikes("length"));
	}
}



##like comment
//$cmts[random_int(0,count($cmts)-1)]->like();

##reply to comment
// $cmts[random_int(0,count($cmts)-1)]->reply("Hello World".time());



##get fullcontent
var_dump(flatContent($post->fullcontent()));

##get user
$user=$post->getUser();
if($user)
	var_dump("author id:".$user->getId());










?>