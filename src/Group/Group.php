<?php 
namespace Facebook\Group;
use Facebook\Utils\Html;
use Facebook\Utils\Util;

class Group extends \Facebook\Common{
	use actions;
	use posts;
	use publish;

	public $admin=0;//0=>visitor 1=>member 2=>pending memberchip request
	public $id;

	public $childs=[
		"items"=>[],
		"next_page"=>"",
		"add"=>""
	];
	public $actions; // array of possible actions like join and leave

	function  __construct($parent,$id,$admin=0){
		$this->parent=$parent;
		parent::__construct();
		
		$this->id=$id;
		$this->admin=$admin;
	}

	protected function fetch($force=0){
		if(!$force&&$this->fetched)return;
		$this->http($this->id);
		$this->detectMembership();

		if($this->admin===1){
			$form=Html::findDom($this->dom("<form",1),"<textarea");
			$this->childs["add"]=$form;
		}
		$this->fetched=1;
	}

	private function detectMembership(){
		$forms=$this->dom("<form",1);
		$join=Html::findDom($forms,"Join Group");
		$leave=Html::findDom($forms,"Cancel Request");
		if($join){
			$this->admin=0;	
			$this->actions=$join;
		}elseif($leave){
		 $this->admin=2;
			$this->actions=$leave;
		}
		else $this->admin=1;
	}

	private function postAppeared(){
		//get all posts and if exist any one hasn't More action so this mine
		$posts=$this->splitPosts()["posts"];
		$appeared=Util::filter($posts,function($post){
			return !count(Html::findDom(Html::dom($post[0],"<a"),"More"));
		});
		return count($appeared[0]);
	}
	static function IdFromUrl($url){
		if(intval($url))return $url;
		preg_match_all("/\/\d+/",$url,$id);
		if(isset($id[0][0]))$id=intval(substr($id[0][0],1));
		else $id="";
		return $id;
	}

}
