<?php 

class  Post extends common{
	public $parent=null;
	public $admin=0;//if this post is mine or not
	public $info=[
		"id"=>null,//id of the post
		"from"=>null,  //who publish it and where
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
		"form"=>""
	];
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
	public function checkIsMine(){
		if(isset($this->info["from"]["user"])&&$this->info["from"]["user"]){
			if($this->info["from"]["user"]==$this->root->profile->id())
				return $this->admin=true;
		}else{
			$this->fetch_info();
			return $this->checkIsMine();
		}
		return $this->admin=false;
	}
	//get Full content
	public function fullContent(){
		if($this->info["content"]){
			$content=$this->info["content"];
			$flat=flatContent($content);
			if(strpos($flat,"...More")!==false){
				$this->fetch_info();
			} 
			return $this->info["content"];
		}
	}
	//get comments
	public function comments($page=0){
		if(!$this->comments_info["next"])$this->fetch_info();
		if(is_numeric($page)){
			if(isset($this->comments_info["items"][$page]))
				return $this->comments_info["items"][$page];
			else{
				for ($i=0; $i <=$page; $i++) { 
					$this->http($this->comments_info["next"]);
					if(self::detectType($this->html))
						$data=self::spliceImageHtml($this->html);
					else
						$data=self::splicePostHtml($this->html);
					$this->parseComments($data["comment_html"]);
				}
				return $this->comments_info["items"][count($this->comments_info["items"])-1];
			}
		}else {
			return $this->comments_info["items"];
		}

	}
	//like action
	public function like(){
		if($this->info["like_link"]&&!$this->info["likes"]["me"]){
			$this->http($this->info["like_link"]);
			$this->info["likes"]["me"]=true;
			return true;
		}
		else return false;
	}
	//deslike action
	public function unlike(){
		if($this->info["like_link"]&&$this->info["likes"]["me"]){
			$this->http($this->info["like_link"]);
			$this->http($this->dom("<a",1)[0][1]["href"]);
			$this->info["likes"]["me"]=false;
			return true;
		}
	}
	//comment action
	public function comment($txt){
		if(!$this->comments_info["form"])
			$this->fetch_info();
		$form=dom($this->comments_info["form"],"<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}
	public function users_likes(){
		return self::fetch_users_likes("",$this);
	}
	//get all users who likes this post
	static public function fetch_users_likes($url="",$parent){
		//ufi/reaction/profile/browser/?ft_ent_identifier=ID_HERE
			$all_users=[];
		if($url||empty($parent->info["likes"]["users"])){
			$next=($url?$url:"/ufi/reaction/profile/browser/?ft_ent_identifier=".$parent->id());
	
			do{
				$next=is_array($next)?$next[0][1]["href"]:$next;//first next is string then it array
				$parent->http($next);
				$parent->html=$parent->dom("<ul")[0];
				$users=dom($parent->html,"<a",1);
				//get only the url of users and next page if it available
				$users=filter($users,function($user){
					return strpos($user[0],"<span")===false||strpos($user[0],"See More")!=false;
				})[0];
				//separe between next page and users
				$links=filter($users,function($link){return strpos($link[0],"<span")===false;});
				$users=$links[0];
				$next=$links[1];
				$users=array_map(function($user)use(&$parent){
					preg_match_all("/(id=\d+)|\/[\w\d.]+/",$user[1]["href"],$id);
					if(isset($id[0][1])&&instr($id[0][1],"id=1"))
						$id=intval(substr($id[0][1],3));
					else $id=substr($id[0][0],1);

					return new Profile($parent,["id"=>$id]);
				},$users);
				$all_users=array_merge($all_users,$users);

			}while(isset($next[0][1]["href"]));
			
			if(!$url)
				$parent->info["likes"]["users"]=$all_users;
			
			return $all_users;
		}
	}
	//get informaion about this post only by it id
	public function fetch_info(){
		$this->http($this->id());
		if(self::detectType($this->html))
			$data=self::spliceImageHtml($this->html);
		else
			$data=self::splicePostHtml($this->html);
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


	}

	//satic global functions get information about such post from it html
	//but this html grabbed from list of posts like in main facebook page (user wall)
	public static function GetInfoFromListedPost($post){
		$info=[];
		$data=jsondecode($post[1]["data-ft"]);
		$html=dom($post[0],"<div");

		//data [id,user,type_of_post(group or page or friend)]

		//html [content,likes_number]
		$content=dom($html[0],"<div");

		$from=dom(array_shift($content),"<a",1);
		// $from=parseFrom($content,$data);  / ! \

		$text=join($content,"");

		$actions=findDom(dom($html[1],"<div"),"Full Story");
		if(!$actions)return;
		$actions=dom($actions,"<a",1);

		//get link of like action
		$like_link=filter($actions,function($action){
			return strpos($action[0],"Like")!==false;
		});
		if(isset($like_link[0][0][1]["href"]))
			$like_link=$like_link[0][0][1]["href"];
		else $like_link="";
		
		$areadyliked=isset(filter($actions,function($action){
			return strpos($action[0],"<b>Like</b>")!==false;
		})[0][0]);

		$likes=array_shift($actions);
		if(strlen($likes[0])>4)
			$likes=intval(substr($likes[0],strpos($likes[0],"</span>")+7));
		else $likes=0;

		return [
			"from"=>self::parseFrom($from,$data),
			"data"=>$data,
			"content"=>parseContent($text),
			"likes_number"=>$likes,
			"like_link"=>$like_link,
			"aready_liked"=>$areadyliked
		];

	}
	//get the type of such post if it's normal post or image post
	public static function detectType($html){
		//0 post|0 group_post|0  page_post|1 image|
		if(count(dom($html,'id="m_story_permalink_view"')))
			return 0;
		else if(count(dom($html,'id="MPhotoContent"')))
			return 1;
		else return false;
	}
	//grab information about image post 
	public static function spliceImageHtml($html){
		$actions=dom(dom($html,'id="MPhotoActionbar"')[0],"<a",1);
		$html=doms($html,['id="MPhotoContent"',"<div"]);
		$content=$html[0];// content of the post and the owner and where it came from (group/page/profile) and full-image
		$reaction=$html[1];// section of comments likes and some other actions

		//content
		$content=dom($content,"<div");   //fisrt contain 'from' 'content' the second conatint full-size
		//NOTE:: i gonna suppose that the last div in content[0] is only the content but it's not proved			
		$content[0]=dom($content[0],["<a","<div"],1);//not filtred

		$text=array_pop($content[0])[0];
		$from=$content[0];
		$image=dom($content[1],"<a",1);//content[1] has the link of full_size
		$image=findInnerText("View Full Size",$image);

		//reaction
		$reaction=filter(doms($reaction,["<div","<div","<div"]))[0];

		

		//get link of like action
		$like_link=filter($actions,function($action){
			return strpos($action[0],"Like")!=false;
		})[0][0][1]["href"];

		$areadyliked=isset(filter($actions,function($action){
			return strpos($action[0],"presentation")!==false;
		})[0][0]);

		$likes=doms(array_shift($reaction),["<div","<div"]);
		/*
			some times the html of likes show the name of who like not one
		  and if the likes number is 1K my code will read it as 1 
		  so you show also add function that deal with likes number
		*/
		$likes=array_pop($likes);
		$likes=!intval($likes)&&$likes?1:intval($likes);

		return [
			"from"=>$from,
			"image"=>$image,
			"content"=>$text,
			"likes_number"=>$likes,
			"like_link"=>$like_link,
			"aready_liked"=>$areadyliked,
			"comment_html"=>$reaction
		];

		

	}
	//grab informaion about normal post
	public static function splicePostHtml($html){
		$html=dom($html,'id="m_story_permalink_view"')[0];
		$html=dom($html,"<div");
		$content=$html[0];// content of the post and the owner and where it came from (group/page/profile) 
		$reaction=$html[1];// section of comments likes and some other actions
		$html=dom($content,"<div",1)[0];
		$data=jsondecode($html[1]["data-ft"]);//attaribute that contain some json infomation about post [allways the user id is in this data as content_owner_id_new]
		$html=doms($html[0],["<div","<div"]);
		$html=filter($html)[0];
		$from=dom(array_shift($html),"<h3")[0];//here you can find the infomation about where 
		$from=dom($from,"<a",1);
		$text=$html;

		$reaction=filter(doms($reaction,["<div","<div"]))[0];
		$actions=dom(array_shift($reaction),"<a",1);//##### like action
		//get link of like action
		$like_link=filter($actions,function($action){
			return strpos($action[0],"Like")!=false;
		})[0][0][1]["href"];
		/**
			*@todo presentation criteria is not efficient way because it attribute and exist in picture (not tested in all situation)
		**/
		$areadyliked=isset(filter($actions,function($action){
			return strpos($action[0],"presentation")!==false;
		})[0][0]);

		//get number of likes
		$likes=doms(array_shift($reaction),["<div","<div"]);
		/*
			some times the html of likes show the name of who like not one
		  and if the likes number is 1K my code will read it as 1 
		  so you show also add function that deal with likes number
		*/
		$likes=array_pop($likes);
		$likes=!intval($likes)&&$likes?1:intval($likes);

		return [
			"from"=>$from,
			"data"=>$data,
			"content"=>$text,
			"likes_number"=>$likes,
			"like_link"=>$like_link,
			"aready_liked"=>$areadyliked,
			"comment_html"=>$reaction
		];
	}
	//grab all comments from the first page
	public function parseComments($reaction){
		$comments=Comment::parseComments($reaction,$this);
		$this->comments_info["items"]=array_merge($this->comments_info["items"],[$comments["items"]]);
		$this->comments_info["next"]=!empty($comments["next"])?$comments["next"]:"";
		
		if(!$this->comments_info["form"])		
			$this->comments_info["form"]=$comments["form"];
	}

	/**
		* create classes for info["from"] for user group page ...
	*/
	public function makeFrom(){
		if(!isset($this->info["from"]))return;
		if(isset($this->info["from"]["user"])&&$this->info["from"]["user"])
			$this->info["from"]["user"]=new Profile($this,["id"=>$this->info["from"]["user"]]);
		if(isset($this->info["from"]["page"])&&$this->info["from"]["page"])
			$this->info["from"]["page"]=new Page(["id"=>$this->info["from"]["page"]],$this);
		if(isset($this->info["from"]["group"])&&$this->info["from"]["group"])
			$this->info["from"]["group"]=new Group(["id"=>$this->info["from"]["group"]],$this);
	} 

	//grab the user who publish this post and in which section (group|share from)
	public function parseFrom($from,$data=""){
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