<?php 
namespace Facebook\Page;
use Facebook\Utils\Html;

class Page extends \Facebook\Common{
	use actions;
	use posts;
	use publish;

	public $admin=0;
	public $id;

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

	private function fetch($force=0){
		if(!$force&&$this->fetched)return;

		$this->http($this->id);
		$tool=Html::findDom(Html::dom($this->html,"<table"),"More");//contain the like/dislike button and messaging and follow
		$tool=Html::dom($tool,"<a",1);
		//like_like whether it like or dislike like
		$like_link=Html::findDom($tool,"Like");
		if(isset($like_link[1]["href"]))
			$this->likes["like"]=$like_link[1]["href"];
		else{
			$like_link=Html::findDom($tool,"Unlike");
			if(isset($like_link[1]["href"]))
				$this->likes["like"]=$like_link[1]["href"];
		}

		if($this->admin){
			$form=Html::findDom($this->dom("<form",1),"<textarea");
			$this->childs["add"]=$form;
		}

		$this->fetched=1;
	}

	private function permission($access){
		if($this->admin!==$access)
			throw new Exception("you haven't permission", 1);
	}
}
