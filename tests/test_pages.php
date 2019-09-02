<?php 
include "../index.php";
#my pages
// $myPages=$user->pages->myPages();
// $page=$myPages[0];
##posts
// var_dump("---------------RANDOM POST COMMNETS");
// for ($i=0; $i <2 ; $i++) { 
// 	$posts=$page->posts($i);
// 	var_dump("|[page:".$i." ]|");
// 	foreach ($posts as $post) {
// 		var_dump("####POST_ID: ".$post->getId());
// 		var_dump("------------------");
// 		var_dump(flatContent($post->getContent()));
// 		var_dump("------------------");
// 	}
// }
##publish
// $post=$page->publish([
// 	"text"=>"hello world".time(),
// ]);

// var_dump($post->getSource("page")->getId());

#invitedPages
// $pages=$user->pages->invitedPages();
// $page=$pages[0];

##info
// var_dump("Page ID: ".$page->getId());

##action
// $page->like();

##posts
// var_dump("---------------RANDOM POST COMMNETS");
// for ($i=0; $i <2 ; $i++) { 
// 	$posts=$page->posts($i);
// 	var_dump("|[page:".$i." ]|");
// 	foreach ($posts as $post) {
// 		var_dump("####POST_ID: ".$post->getId());
// 		var_dump("------------------");
// 		var_dump(flatContent($post->getContent()));
// 		var_dump("------------------");
// 	}
// }

#suggestionPages
// $pages=$user->pages->suggestionPages();
// $page=$pages[0];

##info
// var_dump("Page ID: ".$page->getId());



#page from it id
// $page=new Page($user,262588213843476);
##action
// $page->unlike();

##posts
// var_dump("---------------RANDOM POST COMMNETS");
// for ($i=0; $i <2 ; $i++) { 
// 	$posts=$page->posts($i);
// 	var_dump("|[page:".$i." ]|");
// 	foreach ($posts as $post) {
// 		var_dump("####POST_ID: ".$post->getId());
// 		var_dump("------------------");
// 		var_dump(flatContent($post->getContent()));
// 		var_dump("------------------");
// 	}
// }
 ?>