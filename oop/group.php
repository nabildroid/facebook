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
		$this->member=$member;
		$this->info=mergeAssociativeArray($this->info,$info);
		$this->info["posts_next_page"]=$this->info["id"];
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
	public function fetch_info(){
		$this->http($this->info["id"]);
		$this->detectMembership();
		if($this->member===1){
			$form=findDom($this->dom("<form",1),"<textarea");
			$this->info["form"]=$form;
		}
	}
	//Group action
	public function join($questions=[]){
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
		if($this->member===1){
			$this->http("/group/leave/?group_id=".$this->info["id"]);
			$this->member=0;
			return true;
		}elseif($this->member===2&&$this->info["join"]){
			$form=$this->info["join"];
			$this->submit_form($form[0],$form[1]["action"]);
			$this->member=0;
		}else throw new Exception("user didn't have the permission to leave such group");
	}
	public function posts($page=0){
		if($this->member!==1)
			throw new Exception("user didn't have the permission to read group posts");
		if(is_numeric($page)){
			if(isset($this->info["posts"][$page]))
				return $this->info["posts"][$page];
			else{
				for ($i=count($this->info["posts"]);$i <=$page; $i++) {
					if(!$this->info["posts_next_page"])break;
					
					$this->http($this->info["posts_next_page"]);
					$content=$this->dom('id="m_group_stories_container"')[0];

					$posts=dom($content,["data-ft",'role="article"'],1);

					$tempPosts=[];
					foreach ($posts as $post){
						$info=Post::GetInfoFromListedPost($post);
						if($info)
							$tempPosts[]=new Post($info["from"]["id"],$this,$info);
					}
					$this->info["posts"]=array_merge($this->info["posts"],[$tempPosts]);

					$next=findDom(dom($content,"<a",1),"See More Posts");
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
	/**
	 * @param $param, is array(key/pair) it takes text(string),images(array),privacy(string),tags(array) and all are options
	*/
	public function publish($param){
		//prepare paramater
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
			"tags"=>[]
		],$param);

		//main function
		$this->http($this->info["id"]);
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
	}

}
