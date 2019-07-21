<?php 
include "config.php";


$user=new Profile;
$user->login("c_user=100009747405464; xs=20:lGaNLcxxejf-1g:2:1557504986");




// $user->wall->all();
$post=new Post(2380363388887953,$user);
$post->fetch_info();
var_dump($post->info);







function info($txt){
	echo "<h3>".$txt."</h3>";
}


?>