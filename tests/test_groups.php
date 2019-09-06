<?php 
use Facebook\Group\Group;
use Facebook\Profile\Profile;
use Facebook\Utils\Content;

#my groups
// $my=$user->groups->myGroups();
// var_dump("number of groups: ".count($my));
// var_dump("---------------RANDOM POST COMMNETS");
// $posts=$my[0]->posts();
// foreach ($posts as $post) {
// 	var_dump("####POST_ID: ".$post->getId());
// // 	var_dump("------------------");
// // 	var_dump(flatContent($post->getContent()));
// // 	var_dump("------------------");
// }

## actions
##leave existing group member
// $last=array_pop($my);
// $last->leave();
##publish 



// $a=$my[3]->publish([
// 	"text"=>"hello world".time(),
// 	"images"=>["https://scontent-mrs2-1.xx.fbcdn.net/v/t1.0-9/fr/cp0/e15/q65/69283767_549920142211885_4254410095917531136_n.jpg?_nc_cat=108&efg=eyJpIjoiYiJ9&_nc_oc=AQlpOh5dvD0aCwnS1ugrqYdg1Fq7S4-6qaadAdAZuxJUW5fbfaFKHtUEYaNsx4EhGSk&_nc_ht=scontent-mrs2-1.xx&oh=ca16a08389d24908bc1112dbbaacd92d&oe=5DCEDE59"]
// ]);
// if($a==="pending")
// 	echo "\n[###########   ]pending\n";
// else echo "\n####NEW__POST_ID: ".$a->getId();

#suggestion groups
// $groups=$user->groups->suggestionGroups();
// $group=$groups[0];
// var_dump($group->getid());
##actions

###join
// $group->join(["one","two","three"]);

#group from id
$group=new Group($user,2255008081177585);
##action 
###join
$group->join(["one","two","three"]);
###leave
$group->leave(); 

 ?>