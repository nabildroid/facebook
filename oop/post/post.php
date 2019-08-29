<?php 
class Post extends common{
	use post_actions;
	use post_comments;
	use post_fullcontent;
	use post_splithtml;
	use post_parsesource;
	use post_wholikes;

	//basic info
	public $id;     //id of comment 
	public $user;   //the auther of this comment [Profile]
	public $content;
	public $image;  //url of full size image
	public $admin=0;//if this post is mine or not

	//likes information including link to make a like 
	public $likes=[
		"length"=>0,  //number of likes
		"users"=>[],  //users who likes this comment [array of profile]
		"mine"=>0,    //if this account has been liked this post
		"url"=>"",    //url of page contain all users who likes
		"like"=>""    //url for make a new like to such comment
	];

	//comment inforamtion
	public $childs=[  
		/* @todo "length"=>0, //number of replys */
		"items"=>[], //array contain multi arrays of comment array for each page 
		"next_page"=>null,//url lead to next page of comment
		"next_page_indicator"=>"",//sometimes initiale caption of next reply page is "View previous replies" and othertime is "View previous replies" 
		"add"=>""    //html form for create a reply 
	];

	//source of such post
	public $source=[
		"origin"=>"", //if such post is shared from another one 
		"page"=>"",   //post cames from a page
		"group"=>""   //post cames from a agoup
	];


	
	function __construct($parent,$id=null){
		$this->parent=$parent;
		parent::__construct();
		
		$this->id=$id;
	}
	//get informaion about this post from it id
	public function fetch($force=0){
		if(!$force&&$this->fetched)return;
		$this->http($this->id);
		if(is_array($this->html))
			$data=$this->parseInlinePost();
		else{
			$type=self::detectType($this->html);
			if($type)
				$data=$this->splitImageHtml();
			else
				$data=$this->splitPostHtml();
		}
		////common property
		$this->content=parseContent($data["content"]);
		$this->likes["length"]=$data["likes_length"];
		$this->likes["like"]=$data["like_link"];
		$this->likes["mine"]=$data["already_liked"];
		//source
		$source=self::parseSource($data["source"]["html"],$data["source"]["attribute"]);
		//source basic
		if($source["user"])
			$this->user=$this->checkIsMine($source["user"])
										?$this->root->profile
										:new Profile($this,$source["user"]);						
		$this->id=$source["id"];

		//source options
		if($source["origin_post"])
			$this->source["origin"]=new Post($this,$source["origin_post"]);
		//todo: source must be a object
		if($source["page"])
			$this->source["page"]=$source["page"];
		if($source["group"])
			$this->source["group"]=$source["group"];


		//image
		if(isset($data["image"]))
			$this->image=$data["image"];
		//comments
		if(isset($data["comments_html"]))
			$this->parseCommentSection($data["comments_html"]);

		

		$this->fetched=1;
	}
	//detect wether this post is for me or not
	private function checkIsMine($user){
		if($user==$this->root->profile->id)
			return $this->admin=true;
	  else return $this->admin=false;
	}


}



?>