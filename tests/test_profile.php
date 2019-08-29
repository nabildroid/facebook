<?php 
include "../index.php";


$mine=$user->profile;

# my profile

// var_dump("ID: ".$mine->getId());
// var_dump("bio: ".$mine->getBio());
// if($mine->getPicture("profile"))
// var_dump("picture profile: ".$mine->getPicture("profile")->getId());
// if($mine->getPicture("cover"))
// var_dump("picture cover: ".$mine->getPicture("cover")->getId());
##posts
// var_dump("--------------POSTS");
// for ($i=0; $i <3 ; $i++) { 
// 	var_dump("---page:".$i);
// 	$posts=$mine->posts($i);
// 	foreach ($posts as $post) {
// 		var_dump($post->getId());
// 	}
// }

##setting
// $mine->setBio(time());
// $mine->setCoverPicture("http://images.firstcovers.com/covers/i/its_easy_if_you_try-5332.jpg");
// $mine->setProfilePicture("https://i.imgur.com/itElfV3.jpg");

##friends
// $friends=$mine->friends();
// foreach ($friends as $friend) {
// 	var_dump("friend id: ".$friend->getId());
// }

// $pendingRequests=$mine->pendingRequests();
// foreach ($pendingRequests as $user) {
// 	var_dump("pending user: ".$user->getId());
// }
##actions
// if(isset($pendingRequests)&&count($pendingRequests))
// 	$pendingRequests[0]->confirmUserRequest();
// if(isset($pendingRequests)&&count($pendingRequests))
	// $pendingRequests[0]->rejectUserRequest();

# user profile
$user=new Profile($user,100001226455561);

// var_dump("ID: ".$user->getId());
// var_dump("bio: ".$user->getBio());
// if($user->getPicture("profile"))
// var_dump("picture profile: ".$user->getPicture("profile")->getId());
// if($user->getPicture("cover"))
// var_dump("picture cover: ".$user->getPicture("cover")->getId());
##posts
// var_dump("--------------POSTS");
// for ($i=0; $i <3 ; $i++) { 
// 	var_dump("---page:".$i);
// 	$posts=$user->posts($i);
// 	foreach ($posts as $post) {
// 		var_dump($post->getId());
// 	}
// }

##one post comments
$post=$user->posts(0)[1];
var_dump($post->getId());
for ($i=0; $i <2 ; $i++) { 
	$cmts=$post->comments($i);
	var_dump("#############PAGE: ".$i);
	foreach ($cmts as $cmt) {
		var_dump("------------");
		var_dump(flatContent($cmt->getContent()));
		var_dump("------------");
	}
}




##friends
// var_dump("--------------friends");
// $friends=$user->friends();
// foreach ($friends as $friend) {
// 	var_dump("friend id: ".$friend->getId());
// }
##actions

// $user->sendFriendRequest();






 ?>