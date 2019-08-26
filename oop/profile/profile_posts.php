<?php 
trait profile_posts{
	public function posts($page=0){
		$this->fetch();
		if(isset($this->childs["items"][$page]))
			return $this->childs["items"][$page];
		else{

			if(!$this->childs["items"])
				$next=$this->id;
			else $next=$this->childs["next_page"];

			//prepare the url

			for ($i=count($this->childs["items"]);$i <=$page; $i++) {
				if(!$next)break;
				$this->http($next."?v=timeline");
				$content=$this->splitPosts();
				$tempPosts=[];
				foreach ($content["posts"] as $html){
					$post=new Post($this);
					$post->fixHttpResponse($html,null);
					$tempPosts[]=$post;
				}

				$this->childs["items"]=array_merge($this->childs["items"],[$tempPosts]);
				$this->childs["next_page"]=$content["next_page"];
				$next=$this->childs["next_page"];
			}
			if(isset($this->childs["items"][$page]))
				return $this->childs["items"][$page];
			else return [];
		}
	}

	private function splitPosts(){
		$posts=dom($this->html,["data-ft",'role="article"'],1);
		$next=findDom(dom($this->html,"<a",1),"See More Stories");
		if(isset($next[1]["href"]))
			$next=$next[1]["href"];
		else $next="";
		return ["posts"=>$posts,"next_page"=>$next];
	}
}


 ?>