<?php 
class Profile extends common{
	public $parent=null;
	private $admin=0;
	private $message=null;
	public $info=[
		"id"=>null,
		"mine"=>0,
		"picture"=>[
			"profile"=>"",
			"cover"=>""
		],
		"bio"=>"",
		"actions"=>null,
		"posts"=>[],
		"posts_next_page"=>"profile.php",
	];
	function  __construct($parent,$id="profile.php",$admin=0){
		$this->parent=$parent;
		parent::__construct();
		
		$this->info["id"]=$id;
		$this->mine=$admin;
	}
	public function fetch(){
		$this->http($this->info["id"]);
		$html=doms($this->html,['id="root"',"<div","<div"])[0];
		//get cover picture
		$section=dom($html,"<div");
		$cover=dom($section[0],"<a",1)[0];
		preg_match_all("/fbid=.(\d)*/",$cover[1]["href"],$cover);
		$cover=intval(substr($cover[0][0],5));
		$this->info["picture"]["cover"]=new Post($cover,$this);
		//get profile picture
		$section1=dom($section[1],"<div");
		$profile=dom($section1[0],"<a",1)[0];
		preg_match_all("/fbid=.(\d)*/",$profile[1]["href"],$profile);
		$profile=intval(substr($profile[0][0],5));
		$this->info["picture"]["profile"]=new Post($profile,$this);
		//get bio
		if(isset($section1[1])){
			$bio=dom($section1[1],"<div");
			if(isset($bio[0]))
				$this->info["bio"]=$bio[0];
			else{
				$this->info["bio"]=flatContent(parseContent($section1[1]));
			}
		}
		//action buttons
		$section2=dom($section[2],"<a",1);
		$this->info["actions"]=$section2;
		var_dump($section2);
	}

	public function sendFriendRequest(){
		$this->permission(0);
		$url=findDom($this->info["actions"],"Add Friend");
		if(isset($url[1]["href"])){
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function friendAsk(){
		$this->permission(0);
		return isset(findDom($this->info["actions"],"Confirm Friend")[1]["href"]);
	}
	public function confirmFriendRequest(){
		$this->permission(0);
		if($this->friendAsk()){
			$url=findDom($this->info["actions"],"Confirm Friend");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function rejectFriendRequest(){
		$this->permission(0);
		if($this->friendAsk()){
			$url=findDom($this->info["actions"],"Delete Request");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}

	public function setProfilePhoto($url){
		$this->permission(1);
		$this->http("photos/upload/?profile_pic");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function setCoverPhoto($url){
		$this->permission(1);
		$this->http("photos/upload/?cover_photo");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function  setBio($txt){
		$this->permission(1);
		$this->http("profile/basic/intro/bio");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}

	private function splitPosts(){
		$content=$this->dom('id="structured_composer_async_container"');
		if(isset($content[0])){
			$posts=dom($content[0],["data-ft",'role="article"'],1);
			$next=findDom(dom($content[0],"<a",1),"See More Stories");
			return ["posts"=>$posts,"next"=>$next];
		}else return ["posts"=>[],"next"=>""];
	}
	public function posts($page=0){
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
	public function Message(){
		$this->permission(0);
		if($this->message)
			return $this->message;
		else {
			$this->message=new Message(["friend"=>$this->info["id"]],$this);
			return $this->message;
		}
	}
	private function permission($access){
		if($this->admin!==$access)
			throw new Exception("you haven't permission", 1);
	}

}


 ?>