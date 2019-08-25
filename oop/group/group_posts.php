<?php 
trait group_posts{
	public function posts($page=0){
		if($this->member!==1)
			throw new Exception("user didn't have the permission to read group posts");
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
	
	private function splitPosts(){
		$content=$this->dom('id="m_group_stories_container"');
		if(isset($content[0])){
			$posts=dom($content[0],["data-ft",'role="article"'],1);
			$next=findDom(dom($content[0],"<a",1),"See More Posts");
			return ["posts"=>$posts,"next"=>$next];
		}else return ["posts"=>[],"next"=>""];
  }
}

 ?>