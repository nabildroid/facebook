<?php 
use Facebook\Comment\Comment;
use Facebook\Post\Post;
use Facebook\Utils\Content;
#comment from id

// $cmt=new Comment($user,3015289408542907);
// #info
// var_dump("---------------INFO");
// var_dump("id: ".$cmt->getId());
// var_dump("likes number: ".$cmt->getLikes("length"));
// var_dump("writer: ".$cmt->getUser()->getId());
// var_dump("parent id: ".$cmt->parent->getId());
// #action
// var_dump("---------------ACTION");
// $cmt->like();
// $cmt->reply("صحيح");
// #subcomments
// var_dump("---------------SUBCOMMENTS");
// $sub=$cmt->subcomments();
// foreach ($sub as $s) {
// 	var_dump("-author: ".$s->getUser()->getId());
// 	var_dump("---content: ".flatContent($s->getContent()));
// 	var_dump("---likes number: ".$s->getLikes("length"));
// }

#comment from post
$post=new Post($user,10162304307155652);
var_dump($post->comment("hello world"));
// $cmt=$post->comments()[2];
// ##info
// var_dump("---------------INFO");
// var_dump("id: ".$cmt->getId());
// var_dump("likes number: ".$cmt->getLikes("length"));
// var_dump("writer: ".$cmt->getUser()->getId());
// var_dump("parent id: ".$cmt->parent->getId());
// ##action
// // var_dump("---------------ACTION");
// // $cmt->like();
// // $cmt->reply("good");
// ##subcomments
// var_dump("---------------SUBCOMMENTS");
// for ($i=0; $i <3 ; $i++) { 
// 	var_dump("-------page".$i);
// 	$sub=$cmt->subcomments($i);
// 	foreach ($sub as $s) {
// 		var_dump("-author: ".$s->getUser()->getId());
// 		var_dump("-----------");
// 		var_dump($s->getContent());
// 		var_dump("-----------");
// 		var_dump("---likes number: ".$s->getLikes("length"));
// 		echo "\n\n";
// 	}
// }


 ?>