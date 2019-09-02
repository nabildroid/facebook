<?php 
use Facebook\Comment\Comment;
use Facebook\Post\Post;
use Facebook\Utils\Content;

##post from id
$post=new Post($user,103382544200115);

var_dump($post->getPicture());
exit;
##like/unlike
$alread_liked=$post->getLikes("mine");
var_dump("alread_liked:".$alread_liked);
if($alread_liked)
	$post->unlike();
else $post->like();


##comment
$post->comment("Hello world".time());

##get comments

var_dump("---------------RANDOM POST COMMNETS");

for ($i=0; $i <4 ; $i++) { 
	var_dump("-------page".$i);
	$cmts=$post->comments($i);
	foreach ($cmts as $cmt) {
		var_dump("-author: ".$cmt->getUser()->getId());
		var_dump("-------------");
		var_dump($cmt->getContent());
		var_dump("-------------");
		var_dump("---likes number: ".$cmt->getLikes("length"));
		echo "\n\n";
	}
}



##like comment
$cmts[random_int(0,count($cmts)-1)]->like();

##reply to comment
$cmts[random_int(0,count($cmts)-1)]->reply("Hello World".time());



##get fullcontent
var_dump(($post->fullcontent()));

##get user
$user=$post->getUser();
if($user)
	var_dump("author id:".$user->getId());










?>