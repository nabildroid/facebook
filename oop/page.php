<?php 
class Page extends common{
	public $parent=null;
	public $admin=0;
	public $info=[
		"id"=>null,
		"name"=>null,
		"likes_length"=>null,
		"like_link"=>null,
		"follow_link"=>null,
		"posts"=>[],
		"posts_next_page"=>null,
		"form"=>""
	];
	function  __construct($info=[],$parent,$admin=0){
		$this->parent=$parent;
		$this->admin=$admin;
		$this->info=mergeAssociativeArray($this->info,$info);
		$this->info["posts_next_page"]=$this->info["id"];
	}
	public function fetch_info(){
		$this->http($this->info["id"]);
		$tool=findDom(dom($this->html,"<table"),"More");//contain the like/dislike button and messaging and follow
		$tool=dom($tool,"<a",1);
		//like_like whether it like or dislike like
		$like_link=findDom($tool,"Like");
		if(isset($like_link[1]["href"]))
			$this->info["like_link"]=$like_link[1]["href"];
		else{
			$like_link=findDom($tool,"Unlike");
			if(isset($like_link[1]["href"]))
				$this->info["like_link"]=$like_link[1]["href"];
		}
		if($this->admin){
			$form=findDom($this->dom("<form",1),"<textarea");
			$this->info["form"]=$form;
		}
	}
	public function posts($page=""){
		if(is_numeric($page)){
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
		}else {
			return $this->info["posts"];
		}

	}
	private function permission($access){
		if($this->admin!==$access)
			throw new Exception("you haven't permission", 1);
	}
	//page action
	public function like(){
		$this->permission(0);
		if($this->info["like_link"]&&strpos($this->info["like_link"],"unfan")===false)
			$this->http($this->info["like_link"]);
		return true;
	}
	public function dislike(){
		$this->permission(0);
		if($this->info["like_link"]&&strpos($this->info["like_link"],"unfan")!==false)
			$this->http($this->info["like_link"]);
		return true;
	}

	public function follow(){
	}

	/**
	 * if it's my page
	 * @param $param, is array(key/pair) it takes text(string),images(array)
	*/
	public function publish($param){
		$this->permission(1);
		//prepare paramater
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
		],$param);

		$form=$this->info["form"];
		$forceInput=[];

		if(!$param["images"]){//publish text
			//publish post
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"",$forceInput);
		}else{//publish image
			//fecth upload page
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"view_photo");
			//upload images
			$form=dom($this->html,"<form",1)[0];
			$this->submit_form($form[0],$form[1]["action"],$param["images"],"add_photo_done");
			$form=dom($this->html,"<form",1)[0];
			//publish post
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"view_post",$forceInput);
		}
	}


}
