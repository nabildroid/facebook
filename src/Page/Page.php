<?php 
namespace Facebook\Page;
use Facebook\Utils\Html;

class Page extends \Facebook\Common{
	use actions;
	use posts;
	use publish;

	public $admin=0;
	public $id;
	public $name;

	//likes information including link to make a like 
	public $likes=[
		"length"=>0,  //number of likes
		"mine"=>0,    //if this account has been liked this post
		"like"=>""    //url for make a new like to such comment
	];

	public $childs=[
		"items"=>[],
		"next_page"=>"",
		"add"=>""
	];


	function  __construct($parent,$id,$admin=0){
		$this->parent=$parent;
		parent::__construct();
		
		$this->id=$id;
		$this->admin=$admin;
	}

	public function fetch($force=0){
		if(!$force&&$this->fetched)return;

		$this->http($this->id);

		//get name
		$name=Html::doms($this->html,["<h1","<div","<span"]);
		if(isset($name[0])&&$name[0])
			$this->name=trim($name[0]);

		$tool=Html::findDom(Html::dom($this->html,"<table"),"More");//contain the like/dislike button and messaging and follow
		$tool=Html::dom($tool,"<a",1);
		//like_like whether it like or dislike link
		$like_link=Html::findDom($tool,"Like");
		if(isset($like_link[1]["href"]))
			$this->likes["like"]=$like_link[1]["href"];
		else{
			$like_link=Html::findDom($tool,"Unlike");
			if(isset($like_link[1]["href"]))
				$this->likes["like"]=$like_link[1]["href"];
		}
		//check if this page owned by Account
		$this->admin=Html::findDom($tool,"Follow")!=true;

		if($this->admin){
			$form=Html::findDom($this->dom("<form",1),"<textarea");
			$this->childs["add"]=$form;
		}

		$this->fetched=1;
	}

	private function permission($access){
		if($this->admin!=$access)
			throw new \Exception("you haven't permission", 1);
	}

	static function IdFromUrl($url){
		if(intval($url))return $url;
		preg_match_all("/^\/[\w+\d+.]+?(?=\/\?)/",$url,$id);
		if(isset($id[0][0])){
			$id=substr($id[0][0],1);
		}else $id="";
		return $id;
	}
}
