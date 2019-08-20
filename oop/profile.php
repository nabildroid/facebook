<?php 
class Profile extends common{
	public $parent=null;
	public $info=[
		"id"=>null,
		"posts"=>[],
		"picture"=>[
			"profile"=>"",
			"cover"=>""
		],
		"bio"=>"",
		"posts_next_page"=>"profile.php",
	];
	function  __construct($parent,$id="profile.php"){
		$this->parent=$parent;
		parent::__construct();
		
		$this->info["id"]=$id;
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
		$bio=dom($section1[1],"<div");
		if(isset($bio[0]))
			$this->info["bio"]=$bio[0];
		else{
			$this->info["bio"]=flatContent(parseContent($section1[1]));
		}
	}
	public function setProfilePhoto($url){
		$this->http("photos/upload/?profile_pic");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function setCoverPhoto($url){
		$this->http("photos/upload/?cover_photo");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function  setBio($txt){
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
}


 ?>