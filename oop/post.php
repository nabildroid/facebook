<?php 

class  Post extends common{
	public $parent=null;
	public $commonHtml="";
	public $info=[
		"id"=>null,//id of the post
		"from"=>null,  //who publish it and where
		"likes"=>[    //infomation about likes
			"length"=>0,
			"users"=>[]
		],
		"like_link"=>"",//like action
		"content"=>"" //the content of post
	];
	public $comments=[
		"items"=>[],
		"next"=>"",
		"form"=>""
	];
	function __construct($id,$parent,$info=[]){
		$this->parent=$parent;
		if(!empty($info))$this->info=$info;
		$this->info["id"]=$id;
	}
	//post information
	public function data($prop){
		if(empty($info->user))
			$this->fetch_info();
		return $this->info[$prop];
	}
	//like action
	public function like(){
		info("post ".$this->id." has been liked");
	}
	//comment action
	public function comment($txt){
		info("post ".$this->id." has been commented with '".$txt."'");
	}
	//get all users who likes this post
	public function users_likes($url=""){
		//ufi/reaction/profile/browser/?ft_ent_identifier=ID_HERE
			$all_users=[];

		if($url||empty($this->info["likes"]["users"])){
			$next=($url?$url:"/ufi/reaction/profile/browser/?ft_ent_identifier=".$this->info["id"]);
	
			do{
				$next=is_array($next)?$next[0][1]["href"]:$next;//first next is string then it array
				$this->http($next);
				$this->html=$this->dom("<ul")[0];
				$users=dom($this->html,"<a",1);
				//get only the url of users and next page if it available
				$users=filter($users,function($user){
					return strpos($user[0],"<span")===false||strpos($user[0],"See More")!=false;
				})[0];
				//separe between next page and users
				$links=filter($users,function($link){return strpos($link[0],"<span")===false;});
				$users=$links[0];
				$next=$links[1];

				$users=array_map(function($user){return $user[1]["href"];},$users);
				$all_users=array_merge($all_users,$users);

			}while(isset($next[0][1]["href"]));
			
			if(!$url)
				$this->info["likes"]["users"]=$all_users;
			
			return $all_users;
		}
	}
	//get informaion about this post only by it id
	public function fetch_info(){
		$this->http("/".$this->info["id"]);
		if($this->detectType($this->html))
			$this->spliceImageHtml($this->html);
		else $this->splicePostHtml($this->html);
	}

	//satic global functions get information about such post from it html
	//but this html grabbed from list of posts like in main facebook page (user wall)
	public static function GetInfoFromListedPost($post){
		$info=[];
		$data=jsondecode($post[1]["data-ft"]);
		$html=dom($post[0],"<div");
		//data [id,user,type_of_post(group or page or friend)]
		$id=$data["top_level_post_id"];
		$user=$data["content_owner_id_new"];
		if(isset($data["group_id"]))
			$from=$data["group_id"];
		else $from=null;
		//html [content,likes_number]
		$content=dom($html[0],"<div");
		array_shift($content);
		$content=join($content,"");

		$likes=dom($html[1],"<div");
		$likes=array_pop($likes);
		$likes=dom(dom($likes,"<span")[0],"<a")[0];
		$likes=intval(substr($likes,strpos($likes,"</span>")+7));

		$info["id"]=$id;
		$info["info"]["user"]=$user;
		$info["info"]["from"]=$from;
		$info["info"]["likes"]["length"]=$likes;
		$info["info"]["content"]=$content;
		Post::parseFrom($from);
		return $info;
	}
	//get the type of such post if it's normal post or image post
	public function detectType($html){
		//0 post|0 group_post|0  page_post|1 image|
		if(count(dom($html,'id="m_story_permalink_view"')))
			return 0;
		else if(count(dom($html,'id="MPhotoContent"')))
			return 1;
	}
	//grab information about image post 
	public function spliceImageHtml($html){
		$actions=dom(dom($html,'id="MPhotoActionbar"')[0],"<a",1);
		$html=doms($html,['id="MPhotoContent"',"<div"]);
		$content=$html[0];// content of the post and the owner and where it came from (group/page/profile) and full-image
		$reaction=$html[1];// section of comments likes and some other actions

		//content
		$content=dom($content,"<div");   //fisrt contain 'from' 'content' the second conatint full-size
		//NOTE:: i gonna suppose that the last div in content[0] is only the content but it's not proved	
		// $content[0]=
		/*
			TODO
			try to figuerout a way to splice the last div that hold content, from the above elements that hold any links to the owner of such post
		*/
		$content[0]=dom($content[0],["<a","<div"],1);//not filtred

		$text=array_pop($content[0])[0];
		$from=$content[0];
		$image=dom($content[1],"<a",1);//content[1] has the link of full_size
		$image=findInnerText("View Full Size",$image);

		//reaction
		$reaction=filter(doms($reaction,["<div","<div","<div"]))[0];
		

		//get reply like
		$like_link=filter($actions,function($action){
			return strpos($action[0],"Like")!=false;
		})[0][0][1]["href"];

		$likes=doms(array_shift($reaction),["<div","<div"]);
		/*
			some times the html of likes show the name of who like not one
		  and if the likes number is 1K my code will read it as 1 
		  so you show also add function that deal with likes number
		*/
		$likes=array_pop($likes);
		$likes=!intval($likes)&&$likes?1:intval($likes);
		$this->info["from"]=Post::parseFrom($from);
		$this->info["image"]=$image;
		$this->info["content"]=$content;
		$this->info["likes"]["length"]=$likes;
		$this->info["like_link"]=$like_link;
		$this->parseComments($reaction);

		

	}
	//grab informaion about normal post
	public function splicePostHtml($html){
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
		//get reply like
		$like_link=filter($actions,function($action){
			return strpos($action[0],"Like")!=false;
		})[0][0][1]["href"];

		//get number of likes
		$likes=doms(array_shift($reaction),["<div","<div"]);
		/*
			some times the html of likes show the name of who like not one
		  and if the likes number is 1K my code will read it as 1 
		  so you show also add function that deal with likes number
		*/
		$likes=array_pop($likes);
		$likes=!intval($likes)&&$likes?1:intval($likes);
		$this->info["from"]=Post::parseFrom($from,$data);
		$this->info["content"]=$content;
		$this->info["likes"]["length"]=$likes;
		$this->info["like_link"]=$like_link;
		$this->parseComments($reaction);

	}
	//grab all comments from the first page
	public function parseComments($reaction){
		if(strpos($reaction[0],"<form")===0)
			$form=array_shift($reaction);
		else $form=array_pop($reaction);
		$comments=dom(array_shift($reaction),"<div");
		$comments=filter($comments,function($str){
			return strpos($str,"View more comments…")===false&&
						 strpos($str,"View previous comments…")===false;
		});
		if($comments[1])
			$next=dom($comments[1][0],"<a",1)[0][1]["href"];

		$comments=$comments[0];
		$comments=array_map(function ($cmt_html){return new Comment($cmt_html,$this);},$comments);
		$this->comments["items"]=array_merge($this->comments["items"],$comments);
		$this->comments["next"]=isset($next)?$next:"";		
		$this->comments["form"]=$form;
	}
	//grab the user who publish this post and in which section (group|share from)
	public function parseFrom($from,$data=""){
		$user="";		$page="";		$group="";		$origin_post="";
		//delete any <a that does't has href
		$from=filter($from,function($a){return isset($a[1]["href"]);})[0];

		///////Get Data from JsonData
		if($data){
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
		return[
			"user"=>$user,
			"origin_post"=>$origin_post,
			"page_id"=>$page,
			"group_id"=>$group,
		];
	}


}


 ?>