<?php 
class  Post extends common{
	public $admin=0;//if this post is mine or not
	public $info=[
		"id"=>null,//id of the post
		"from"=>null,  //who publish it and where  ######SOURCE
		"likes"=>[    //infomation about likes
			"me"=>0, //does i likes this post or not
			"length"=>0,
			"users"=>[]
		],
		"like_link"=>"",//like action
		"content"=>"" //the content of post
	];
	public $comments_info=[
		"items"=>[],
		"next"=>"",
		"form"=>"" //add
	];

	use post_actions;
	use post_comments;
	use post_fullcontent;
	use post_splithtml;
	use post_wholikes;
	
	function __construct($id,$parent,$info=[]){
		$this->parent=$parent;
		parent::__construct();
		
		if(!empty($info)){
			$this->info["from"]=$info["from"];		
			$this->info["likes"]["length"]=$info["likes_number"];		
			$this->info["likes"]["me"]=$info["aready_liked"];		
			$this->info["like_link"]=$info["like_link"];		
			$this->info["content"]=$info["content"];		

			$this->makeFrom();
		}
		$this->info["id"]=$id;
	}
	//get informaion about this post from it id
	public function fetch(){
		if($this->fetched)return;
		$this->http($this->id());
		if(self::detectType($this->html))
			$data=$this->splitImageHtml($this->html);
		else
			$data=$this->splitPostHtml($this->html);
		$this->info["from"]=$this->parseFrom($data["from"],isset($data["data"])?$data["data"]:"");
		$this->info["content"]=parseContent($data["content"]);
		$this->info["likes"]["length"]=$data["likes_number"];
		$this->info["likes"]["me"]=$data["aready_liked"];
		$this->info["like_link"]=$data["like_link"];

		if(isset($this->info["from"]["id"]))
			$this->info["id"]=$this->info["from"]["id"];
		if(isset($data["image"]))
			$this->info["image"]=$data["image"];

		$this->makeFrom();
		$this->parseComments($data["comment_html"]);
		$this->checkIsMine();
		$this->fetched=1;
	}
	//detect wether this post is for me or not
	private function checkIsMine(){
		if(isset($this->info["from"]["user"])&&$this->info["from"]["user"]){
			if($this->info["from"]["user"]==$this->root->profile->id())
				return $this->admin=true;
		}return $this->admin=false;
	}

	/**
	 * create classes for info["from"] for user group page ...
	 */
	private function makeFrom(){
		if(!isset($this->info["from"]))return;
		if(isset($this->info["from"]["user"])&&$this->info["from"]["user"])
			$this->info["from"]["user"]=new Profile($this,["id"=>$this->info["from"]["user"]]);
		if(isset($this->info["from"]["page"])&&$this->info["from"]["page"])
			$this->info["from"]["page"]=new Page(["id"=>$this->info["from"]["page"]],$this);
		if(isset($this->info["from"]["group"])&&$this->info["from"]["group"])
			$this->info["from"]["group"]=new Group(["id"=>$this->info["from"]["group"]],$this);
	} 

	//grab the user who publish this post and in which section (group|share from)
	private function parseFrom($from,$data=""){
		$user="";		$page="";		$group="";		$origin_post=""; $id="";
		//delete any <a that does't has href
		$from=filter($from,function($a){return isset($a[1]["href"]);})[0];
		///////Get Data from JsonData
		if($data){
			if(isset($data["top_level_post_id"]))
				$id=$data["top_level_post_id"];
			if(isset($data["content_owner_id_new"]))
				$user=$data["content_owner_id_new"];
			if(isset($data["page_id"]))
				$page=$data["page_id"];
			if(isset($data["group_id"]))
				$group=$data["group_id"];
			if(isset($data["original_content_id"]))
				$origin_post=$data["original_content_id"];
			if(!$group){
				//get the group if it exist
				$temp=filter($from,function($a){
						return strpos($a[1]["href"],"/groups/")===0;
				});
				if(isset($temp[0][0]))$group=$temp[0][0][1]["href"];
			}
		}else{
			///////Get Data from html
			
			//defference between user and page in header is the page name allways end with /? but  the user end with only ?

			//get the group if it exist
			$temp=filter($from,function($a){
					return strpos($a[1]["href"],"/groups/")===0;
			});
			if(isset($temp[0][0]))$group=$temp[0][0][1]["href"];
			//get origin post if it exist
			$temp=filter($temp[1],function($a){
					return strpos($a[1]["href"],"/story.php?")===0;
			});
			if(isset($temp[0][0]))$origin_post=$temp[0][0][1]["href"];
			//get pageId if it exist Note::allways the pageId has identifier "/?"
			$temp=filter($temp[1],function($a){
					return strpos($a[1]["href"],"/?")!=false;
			});
			if(isset($temp[0][0]))$page=$temp[0][0][1]["href"];
			if(isset($temp[1][0][1]["href"])&&!$page)
				$user=$temp[1][0][1]["href"];
		}
		return [
			"user"=>$user,
			"id"=>$id,
			"origin_post"=>$origin_post,
			"page"=>$page,
			"group"=>$group,
		];
	}


}



?>