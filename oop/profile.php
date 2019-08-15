<?php 
class Profile extends common{
	public $parent=null;
	public $info=[
		"id"=>null,
		"posts"=>[],
		"posts_next_page"=>"profile.php",
	];
	function  __construct($parent,$id="profile.php"){
		$this->parent=$parent;
		parent::__construct();
		
		$this->info["id"]=$id;
	}
	public function fetch(){
		$this->http($this->info["id"]);
	}

	private function splitPosts(){
		$content=$this->dom('id="structured_composer_async_container"');
		if(isset($content[0])){
			$posts=dom($content[0],["data-ft",'role="article"'],1);
			$next=findDom(dom($content[0],"<a",1),"See More Stories");
			return ["posts"=>$posts,"next"=>$next];
		}else return ["posts"=>[],"next"=>""];
	}
	public function posts($page=0){
		if(is_numeric($page)){
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
		}else {
			return $this->info["posts"];
		}
	}
}


 ?>