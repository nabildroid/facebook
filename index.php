<?php 
include "config.php";


$user=new Profile;
$user->login("c_user=100009747405464; xs=20:lGaNLcxxejf-1g:2:1557504986");




// $user->wall->all();
$post=new Post(338556417070774,$user);
$post->fetch_info();
var_dump($post->comments(0)[8]->subcomments(0)[1]->info["likes"]);







function info($txt){
	echo "<h3>".$txt."</h3>";
}


?>