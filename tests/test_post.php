<?php 
include "../index.php";


##post from id
$post=new Post($user,3015042911900890);


##like/unlike
$alread_liked=$post->getLikes("mine");
var_dump("alread_liked:".$alread_liked);
// if($alread_liked)
// 	$post->unlike();
// else $post->like();


##comment
// $post->comment("Hello world".time());

##get comments
$post->comments(0);
$cmts=$post->comments(1);
$cmt=$cmts[random_int(0,count($cmts)-1)];
## get random comment content
var_dump(flatContent($cmt->getContent()));
var_dump($cmt->getUser()->getId());
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