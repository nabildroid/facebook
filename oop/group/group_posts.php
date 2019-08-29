<?php 
trait group_posts{
	public function posts($page=0){
		$this->fetch();
		if($this->admin!=1)
			throw new Exception("user didn't have the permission to read group posts");	

		//prepare the url
		if(!$this->childs["items"])
			$next=$this->id;
		else $next=$this->childs["next_page"];

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

	private function splitPosts(){
		$html=$this->dom('id="m_group_stories_container"');
		if(!isset($html[0]))
			$html=$this->html;
		else $html=$html[0];
		
		$posts=dom($html,["data-ft",'role="article"'],1);
		$next=findDom(dom($html,"<a",1),"See More ");
		if(isset($next[1]["href"]))
			$next=$next[1]["href"];
		else $next="";
		return ["posts"=>$posts,"next_page"=>$next];
	}
}

 ?>