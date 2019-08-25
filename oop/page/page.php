<?php 
class Page extends common{
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

	use page_actions;
	use page_posts;
	use page_publish;

	function  __construct($info=[],$parent,$admin=0){
		$this->parent=$parent;
		parent::__construct();
		
		$this->admin=$admin;
		$this->info=mergeAssociativeArray($this->info,$info);
		$this->info["posts_next_page"]=$this->id();
	}

	private function fetch(){
		if($this->fetched)return;
		$this->http($this->id());
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
		$this->fetched=1;
	}

	private function permission($access){
		if($this->admin!==$access)
			throw new Exception("you haven't permission", 1);
	}
}
