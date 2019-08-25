<?php 
trait page_posts{
	public function posts($page=0){
		$this->fetch();
		
		if(isset($this->info["posts"][$page]))
			return $this->info["posts"][$page];
		else{
			for ($i=count($this->info["posts"]);$i <=$page; $i++) {
				if(!$this->info["posts_next_page"])break;
				
				$this->http($this->info["posts_next_page"]);
				$posts=dom($this->html,["data-ft",'role="article"'],1);
				$tempPosts=[];
				foreach ($posts as $post){
					$info=Post::GetInfoFromListedPost($post);
					if($info)
						$tempPosts[]=new Post($info["from"]["id"],$this,$info);
				}
				$this->info["posts"]=array_merge($this->info["posts"],[$tempPosts]);

				$next=findDom(dom($this->html,"<a",1),"Show more");
				if(isset($next[1]["href"]))
					$this->info["posts_next_page"]=$next[1]["href"];

			}
			if(isset($this->info["posts"][count($this->info["posts"])-1]))
				return $this->info["posts"][count($this->info["posts"])-1];
			else return [];
		}
	}
}

?>