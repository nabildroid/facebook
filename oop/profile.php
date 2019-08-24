<?php 
class Profile extends common{
	public $parent=null;
	private $admin=0;
	public $info=[
		"id"=>"profile",
		"picture"=>[
			"profile"=>"",
			"cover"=>""
		],
		"bio"=>"",
		"actions"=>null,
		"posts"=>[],
		"posts_next_page"=>"profile.php",
	];
	function  __construct($parent,$info,$admin=0){
		$this->parent=$parent;
		parent::__construct();

		if($info)$this->info=mergeAssociativeArray($this->info,$info);
		$this->admin=$admin;
	}
	private function fetch(){
		if($this->fetched)return;
		$this->http($this->id());
		$html=doms($this->html,['id="root"',"<div","<div"])[0];
		//get cover picture
		$section=dom($html,"<div");
		$cover=dom($section[0],"<a",1);
		if(isset($cover[0])){
			preg_match_all("/fbid=.(\d)*/",$cover[0][1]["href"],$cover);
			$cover=intval(substr($cover[0][0],5));
			$this->info["picture"]["cover"]=new Post($cover,$this);
		}
		//get profile picture
		$section1=dom($section[1],"<div");
		$profile=dom($section1[0],"<a",1);
		if(isset($profile[0])){
			preg_match_all("/fbid=.(\d)*/",$profile[0][1]["href"],$profile);
			$profile=intval(substr($profile[0][0],5));
			$this->info["picture"]["profile"]=new Post($profile,$this);
		}
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

		//get user id(integer) from actions in more button
		if(!$this->admin){
			$id=findDom($section2,"owner_id");
			if(isset($id[1]["href"])){
				preg_match_all("/owner_id=\d+/",$id[1]["href"],$id);
				$id=intval(substr($id[0][0],9));
				$this->info["id"]=$id;
			}
		}

		$this->fetched=1;
	}
	public function posts($page=0){
		$this->fetch();
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
	private function splitPosts(){
		$content=$this->dom('id="structured_composer_async_container"');
		if(isset($content[0])){
			$posts=dom($content[0],["data-ft",'role="article"'],1);
			$next=findDom(dom($content[0],"<a",1),"See More Stories");
			return ["posts"=>$posts,"next"=>$next];
		}else return ["posts"=>[],"next"=>""];
	}

	/**
		* check if this profile sent friendResuest to such account
		* @return boolean
	*/
	private function friendAsk(){
		$this->permission(0);
		return isset(findDom($this->info["actions"],"Confirm Friend")[1]["href"]);
	}
	public function friends(){
		$this->fetch();

		$all=[];
		$next=$this->id()."?v=friends";
		while ($next) {
			$this->http($next);
			$html=doms($this->html,["<div","<div","<div"])[1];
			$users=dom($html,"<div");
			if($this->admin){
				$users=array_pop($users);
				$users=dom($users,"<div");
			}

			$friends=array_map(function($friend){

				$a=dom($friend,"<a",1)[0];
				preg_match_all("/[a-z0-9.=]+/", $a[1]["href"],$id);
				if($id[0][0]=="profile.php")
					$id=intval(substr($id[0][1],3));
				else $id=$id[0][0];
				//@note: the id could be a string !!!!!
				return new Profile($this,["id"=>$id]);
			},$users);
			$all=array_merge($all,$friends);

			//get next page 
			$next=findDom($this->dom("<a",1),"See More");
			if(isset($next[1]["href"]))
				$next=$next[1]["href"];
			else $next="";
		}
		return $all;
	}
	public function pendingRequests(){
		$this->permission(1);
		$this->fetch();

		$this->http("friends/center/requests?seemore");
		$users=filter($this->dom("<td"),function($td){return strpos($td,"<img")!==0;})[0];
		$users=array_map(function($user){
			$a=dom($user,"<a",1);
			preg_match_all("/uid=.(\d)*/",$a[0][1]["href"],$id);
			$id=intval(substr($id[0][0],4));
			$confirm=findDom($a,"Confirm");
			$confirm[0]="Confirm Friend";
			$reject=findDom($a,"Delete Request");
			$reject[0]="Delete Request";
			return new Profile($this,[
				"id"=>$id,
				"actions"=>[$confirm,$reject]
			]);
		},$users);	
		return $users;
	}

	public function sendFriendRequest(){
		$this->permission(0);
		$this->fetch();

		$url=findDom($this->info["actions"],"Add Friend");
		if(isset($url[1]["href"])){
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function confirmFriendRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=findDom($this->info["actions"],"Confirm Friend");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function rejectFriendRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=findDom($this->info["actions"],"Delete Request");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}

	public function setProfilePhoto($url){
		$this->permission(1);
		$this->fetch();

		$this->http("photos/upload/?profile_pic");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function setCoverPhoto($url){
		$this->permission(1);
		$this->fetch();

		$this->http("photos/upload/?cover_photo");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function  setBio($txt){
		$this->permission(1);
		$this->fetch();

		$this->http("profile/basic/intro/bio");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}

	/**
		* @return new Message object associate between this profile and account profile
	*/
	public function message(){
		$this->permission(0);
		if(!intval($this->id()))
			$this->fetch();

		return new Message(["friend"=>$this->id()],$this);
	}
	/**
		*@param $url string like /bla.bla.00?refid=18&__tn__=R
		*@return either id(int) or id(string)
	*/
	static function idFromUrl($url){
		preg_match_all("/(id=\d+)|\/[\w\d.]+/",$url,$id);
		if(isset($id[0][1])&&instr($id[0][1],"id=10"))
			$id=intval(substr($id[0][1],3));
		else $id=substr($id[0][0],1);
		return $id;
	}
	private function permission($access){
		if($this->admin!==$access)
			throw new Exception("you haven't permission", 1);
	}

}


 ?>