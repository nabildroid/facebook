<?php 
class Group extends common{
	public $parent=null;
	public $info=[
		"id"=>null,
		"name"=>null,
		"join"=>null,
		"posts"=>[],
		"posts_next_page"=>null,
		"form"=>""
	];
	public $member=0;//0=>visitor 1=>member 2=>pending memberchip request

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
	
	//Group action
	public function join($questions=[]){
		$this->fetch();
		if($this->member===0){
			if($this->info["join"]){
				$form=$this->info["join"];
				$this->submit_form($form[0],$form[1]["action"]);
				$form_questions=findDom($this->dom("<form",1),"<textarea");
				$this->submit_form($form_questions[0],$form_questions[1]["action"],$questions);
				$this->member=2;
			}
		}
	}
	public function leave(){
		$this->fetch();
		if($this->member===1){
			$this->http("/group/leave/?group_id=".$this->id());
			$this->member=0;
			return true;
		}elseif($this->member===2&&$this->info["join"]){
			$form=$this->info["join"];
			$this->submit_form($form[0],$form[1]["action"]);
			$this->member=0;
		}else throw new Exception("user didn't have the permission to leave such group");
	}

	private function splitPosts(){
		$content=$this->dom('id="m_group_stories_container"');
		if(isset($content[0])){
			$posts=dom($content[0],["data-ft",'role="article"'],1);
			$next=findDom(dom($content[0],"<a",1),"See More Posts");
			return ["posts"=>$posts,"next"=>$next];
		}else return ["posts"=>[],"next"=>""];
  }
	public function posts($page=0){
		$this->fetch();
		if($this->member!==1)
			throw new Exception("user didn't have the permission to read group posts");
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
	/**
	 * @param $param, is array(key/pair) it takes text(string),images(array),privacy(string),tags(array) and all are options
	*/
	public function publish($param){
		$this->fetch();
		//prepare paramater
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
			"tags"=>[]
		],$param);

		//main function
		$this->http($this->id());
		$form=findDom($this->dom("<form",1),"<textarea");	


		$forceInput=[];
		//tag friends
		if($param["tags"])
			$forceInput["users_with"]=join($param["tags"],",");

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
		return $this->postAppeared()?"published":"pending";
	}
	private function postAppeared(){
		$posts=$this->splitPosts()["posts"];
		$appeared=filter($posts,function($post){
			return !count(findDom(dom($post[0],"<a"),"More"));
		});
		return count($appeared[0]);
	}

}
