<?php 
trait wall_posts{
	/**
	 * get all suggestion posts that facebook provide in main page 
	 * each time fb main page will show new set of posts so it's
	 * not necessary to make pagination in such function
	 * @return array of posts
	 */
	public function posts(){
		$this->http();
		//get posts
		$posts=$this->dom('role="article"',1);
		//create posts objects
		$tempPosts=[];
		foreach ($posts as $post){
			$info=Post::GetInfoFromListedPost($post);
			if($info)
				$tempPosts[]=new Post($info["from"]["id"],$this,$info);
		}
		return $tempPosts;
	}
}


?>