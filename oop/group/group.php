<?php 
class Group extends common{
	public $info=[
		"id"=>null,
		"name"=>null,
		"join"=>null,
		"posts"=>[],
		"posts_next_page"=>null,
		"form"=>""
	];
	public $member=0;//0=>visitor 1=>member 2=>pending memberchip request

	use group_actions;
	use group_posts;
	use group_publish;

	function  __construct($info,$parent,$member=0){
		$this->parent=$parent;
		parent::__construct();
		
		$this->member=$member;
		$this->info=mergeAssociativeArray($this->info,$info);
		$this->info["posts_next_page"]=$this->id();
	}
	private function fetch(){
		if($this->fetched)return;
		$this->http($this->id());
		$this->detectMembership();
		if($this->member===1){
			$form=findDom($this->dom("<form",1),"<textarea");
			$this->info["form"]=$form;
		}
		$this->fetched=1;
	}
	private function detectMembership(){
		$join=findDom($this->dom("<form",1),"Join Group");
		$leave=findDom($this->dom("<form",1),"Cancel Request");
		if($join){
			$this->member=0;	
			$this->info["join"]=$join;
		}elseif($leave){
		 $this->member=2;
			$this->info["join"]=$leave;
		}
		else $this->member=1;
	}

	private function postAppeared(){
		$posts=$this->splitPosts()["posts"];
		$appeared=filter($posts,function($post){
			return !count(findDom(dom($post[0],"<a"),"More"));
		});
		return count($appeared[0]);
	}

}
