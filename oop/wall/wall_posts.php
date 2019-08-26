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
		foreach ($posts as $html){
			$post=new Post($this);
			$post->fixHttpResponse($html,null);
			$tempPosts[]=$post;
		}
		return $tempPosts;
	}
}


?>