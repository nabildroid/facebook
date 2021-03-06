<?php 
namespace Facebook\Profile;
use Facebook\Utils\Html;
use Facebook\Utils\Util;
use Facebook\Utils\Content;

use Facebook\Post\Post;

class Profile extends \Facebook\Common {
	use friends;
	use posts;
	use requestfriend;
	use setter;

	private $admin=0;

	public $id;
	public $name;
	public $bio;
	public $picture=[
		"profile"=>"",
		"cover"=>""
	];
	public $childs=[
		"items"=>[],
		"next_page"=>""
	];
	public $actions; // array of possible actions like send friend request
	

	function  __construct($parent,$id,$admin=0){
		$this->parent=$parent;
		parent::__construct();

		$this->id=$id;
		$this->admin=$admin;
	}
	protected function fetch($force=0){
		if(!$force&&$this->fetched)return;
		/**
		 * facebook return about page when fetching profile with only user id
		 * so ?v=timeline is important for getting also the posts of user 
		 */
		$this->http($this->id."?v=timeline");
		$html=Html::doms($this->html,['id="root"',"<div","<div"])[0];
		//get cover picture
		$section=Html::dom($html,"<div");
		$cover=Html::dom($section[0],"<a",1);
		if(isset($cover[0])){
			preg_match_all("/fbid=.(\d)*/",$cover[0][1]["href"],$cover);
			if(isset($cover[0][0])){
				$cover=intval(substr($cover[0][0],5));
				$this->picture["cover"]=new Post($this,$cover);
			}
		}
		//get profile picture
		$section1=Html::dom($section[1],"<div");
		$profile=Html::dom($section1[0],"<a",1);
		if(isset($profile[0])){
			preg_match_all("/fbid=.(\d)*/",$profile[0][1]["href"],$profile);
			if(isset($profile[0][0])){
				$profile=intval(substr($profile[0][0],5));
				$this->picture["profile"]=new Post($this,$profile);
			}
		}
		//get user name
		$name=Html::dom($section1[0],"<strong");
		if(isset($name[0])){
			$this->name=trim($name[0]);
		}

		//get bio
		if(isset($section1[1])){
			$bio=Html::dom($section1[1],"<div");
			if(isset($bio[0]))
				$this->bio=$bio[0];
			else
				$this->bio=Content::parse($section1[1]);
		}
		//action buttons
		$section2=Html::dom($section[2],"<a",1);
		$this->actions=$section2;

		//get user id(integer) from actions in more button
		if(!$this->admin){
			$id=Html::findDom($section2,"owner_id");
			if(isset($id[1]["href"])){
				preg_match_all("/owner_id=\d+/",$id[1]["href"],$id);
				$id=intval(substr($id[0][0],9));
				$this->id=$id;
			}
		}

		//get posts from main page (page index 0)
		$this->fetched=1;
		$this->posts(0);
	}

	/**
	 * check if this profile sent friendResuest to such account
	 * @return boolean
   */
	private function friendAsk(){
		$this->permission(0);
		return isset(Html::findDom($this->actions,"Confirm Friend")[1]["href"]);
	}



	/**
	 * @param $url string like /bla.bla.00?refid=18&__tn__=R
	 * @return either id(int) or id(string)
	 */
	static function idFromUrl($url){
		if(intval($url))return intval($url);
		preg_match_all("/^\/profile\.php\?id\=\d+|^\/(?!\w+\.php)[\d\w.]+?(?=\?)/",$url,$id);
		if(isset($id[0][0])){
			$id=$id[0][0];
			if(Util::instr($id,"profile.php")){
				preg_match_all("/\d+/",$id,$id);
				$id=intval($id[0][0]);
			}else $id=substr($id,1);
		}else $id="";

		return $id;
	}
	private function permission($access){
		if($this->admin!==$access)
			throw new \Exception("you haven't permission", 1);
	}

}


 ?>