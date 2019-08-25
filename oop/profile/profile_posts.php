<?php 
trait profile_posts{
	public function posts($page=0){
		$this->fetch();

		if(isset($this->info["posts"][$page]))
			return $this->info["posts"][$page];
		else{
			for ($i=count($this->info["posts"]);$i <=$page; $i++) {
				if(!$this->info["posts_next_page"])break;
				$this->http($this->info["posts_next_page"]);
				$content=$this->splitPosts();

				$tempPosts=[];
				foreach ($content["posts"] as $post){
					$info=Post::GetInfoFromListedPost($post);
					if($info)
						$tempPosts[]=new Post($info["from"]["id"],$this,$info);
				}
				$this->info["posts"]=array_merge($this->info["posts"],[$tempPosts]);

				if(isset($content["next"][1]["href"]))
					$this->info["posts_next_page"]=$content["next"][1]["href"];

			}
			if(isset($this->info["posts"][count($this->info["posts"])-1]))
				return $this->info["posts"][count($this->info["posts"])-1];
			else return [];
		}
	}
}


 ?>