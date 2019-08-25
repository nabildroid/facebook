<?php 
class Profile extends common {
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

	use profile_friends;
	use profile_posts;
	use profile_requestFriend;
	use profile_setting;

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

	/**
	 * check if this profile sent friendResuest to such account
	 * @return boolean
   */
	private function friendAsk(){
		$this->permission(0);
		return isset(findDom($this->info["actions"],"Confirm Friend")[1]["href"]);
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
	 * @param $url string like /bla.bla.00?refid=18&__tn__=R
	 * @return either id(int) or id(string)
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