<?php 
class Comment extends common{
	protected $parent=null;
	public $html="";
	public $info=[
		"user"=>"",
		"id"=>null,
		"content"=>"",
		"likes"=>[
			"length"=>0,
			"users"=>[],
			"all"=>""
		],
		"like_link"=>"",
		"reply_length"=>0
	];
	public $subcomments_info=[
		"items"=>[],
		"next"=>"",
		"form"=>""
	];
	private $fetched=0;
	function __construct($id,$html="",$parent){
		$this->parent=$parent;
		parent::__construct();
		
		$this->info["id"]=$id;
		$this->html=$html;
		$this->parse();
	}
	public function __get($name){
		//when needed access to parent must first fetch
		if($name=="parent"){
			$this->fetch();
			return $this->parent;
		}
	}
	/**
		* this works only if only the id is provided
	*/
	public function fetch(){
		if($this->html||$this->fetched)return;
		var_dump("bad");
		$this->http($this->id());
		$type=Post::detectType($this->html);
		$comments=[];
		if($type!==false){
			$post=new Post($this->id(),$this->root);
			$post->fixHttpResponse($this->html,$this->id());
			$post->fetch();
			$comments=$post->comments();
		}		
		else{
			$info=$this->splitComments($this->html);
			preg_match_all("/story_fbid=\d+/",$info["origin_post"],$postId);
			$postId=intval(substr($postId[0][0],11));
			$post=new Post($postId,$this->root);
			$data=self::parseComments($info["comments"],$post);
			$form=$data["form"];
			$comments=$data["items"];
		} 

		foreach ($comments as $comment)
			if($comment->id()==$this->id()){
				$this->copyFrom($comment);break;
			}
		//case of this is reply comment so it has form to reply
		if(isset($form)&&$form)
			$this->subcomments_info["form"]=$form;
		$this->fetched=1;
	}

	/**
		* this works only if html(section of comments with publish form) is provided
	*/
	private function parse(){
		if(!$this->html)return;
		$this->html=dom($this->html,"<div")[0];
		$user=dom(dom($this->html,"<h3")[0],"<a",1)[0][1]["href"];
		
		/////
		$html=dom($this->html,"<div",1);
		//delete any div with empty content
		$html=filter($html,function($div){return trim($div[0])==true;})[0];
		//separate between content and tools

		$tools=["",array_pop($html)];//[reaction tool , reply section]
		if(strlen($tools[1][0])<600)//generally the reply section has short html content
			{$tools[0]=array_pop($html);}
		else{$tools=[$tools[1],""];}

		//Note::html contain the content 

		
		$reaction=dom($tools[0][0],"<a",1);
		//get like action and likes_number		
		$likes=array_shift($reaction);
		$likes_users_link="";//like that hold all users who likes this comments
		if($likes[0]=="Like"){
			$like_link=$likes[1]["href"];
			$likes=0;
		}
		else{
			$likes_users_link=$likes[1]["href"];
			$likes=$likes[0];
			$likes=intval(substr($likes,strpos($likes,"</span>")+7));
		}
		//if the first <a is for likes_number
		if(!isset($like_link)){
			$like_link=filter($reaction,function($tool){
				return $tool[0]=="Like";
			});
			if(isset($like_link[0][0][1]["href"]))
				$like_link=$like_link[0][0][1]["href"];
			else $like_link="";
		}
		//get reply like
		$reply_link=filter($reaction,function($tool){
			return $tool[0]=="Reply";
		})[0];
		if(isset($reply_link[0][1]["href"]))$reply_link=$reply_link[0][1]["href"];
		else $reply_link="";

		//get reply_number
		$reply_number=0;
		if($tools[1]){
			$reply=dom($tools[1][0],"<a",1)[0];
			preg_match_all("/\d+/",$reply[0],$reply_number);
			$reply_number=intval($reply_number[0][0]);
		}
		$this->info["content"]=parseContent($html);
		$this->info["user"]=$user;
		$this->info["likes"]["length"]=$likes;
		$this->info["likes"]["all"]=$likes_users_link;
		$this->info["like_link"]=$like_link;
		$this->info["reply_length"]=$reply_number;
		$this->subcomments_info["next"]=$reply_link;
		$this->fetched=1;
	}

	//actions
	public function like(){
		$this->fetch();
		if($this->info["like_link"]){
			$this->http($this->info["like_link"]);
			return true;
		}else return false;
	}
	public function reply($txt){
		$this->fetch();
		if(!$this->subcomments_info["form"])
			$this->subcomments(0);
		$form=dom($this->subcomments_info["form"],"<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}

	public function users_likes(){
		$this->fetch();
		if(!$this->info["likes"]["users"]&&$this->info["likes"]["all"]){
			$this->info["likes"]["users"]=Post::fetch_users_likes($this->info["likes"]["all"],$this);
		}
		return $this->info["likes"]["users"];
	}
	public function subcomments($page){
		$this->fetch();
		if(is_numeric($page)){
			if(isset($this->subcomments_info["items"][$page]))
				return $this->subcomments_info["items"][$page];
			else{
				for ($i=count($this->subcomments_info["items"]); $i <=$page; $i++) { 
					if(!$this->subcomments_info["next"])continue;
					
					$this->http($this->subcomments_info["next"]);
	
					$data=$this->splitComments($this->html)["comments"];
					$data=self::parseComments($data,$this);
					$this->subcomments_info["next"]=$data["next"];

					$this->subcomments_info["items"]=array_merge($this->subcomments_info["items"],[$data["items"]]);

					if(!$this->subcomments_info["form"])
					$this->subcomments_info["form"]=$data["form"];

				}
				if(isset($this->subcomments_info["items"][count($this->subcomments_info["items"])-1]))
					return $this->subcomments_info["items"][count($this->subcomments_info["items"])-1];
				else return [];
			}
		}else {
			return $this->subcomments_info["items"];
		}
	}
	/**
		* sometimes facebook return page that contain all comments
		* ( like when you click to reply ), but not the main post
		* @param html content of comment page
		* @return array [comments=>["form","comments"],origin_post=>"origin_post"]
	**/
	private function splitComments($html){
		$data=doms($html,['<div','<div','<div']);
		$origin_post="";
		if(strpos($data[0],"<a")===0){
			$origin_post=dom($data[0],"<a",1)[0];
			$origin_post=$origin_post[1]["href"];
		}
		//get only replys and form for submit new reply
		//delete before last div if it's not replys because the formal div number is 4 divs 
		if(count($data)<4)
			$data[count($data)-2]="";


		$data=[$data[count($data)-2],$data[count($data)-1]];
		return ["comments"=>$data,"origin_post"=>$origin_post];
	}
	//grab all comments from the first page
	static function parseComments($reaction,$parent){
		if(isset($reaction[0])){
			if(strpos($reaction[0],"<form")===0)
				$form=array_shift($reaction);
			else $form=array_pop($reaction);
			$comments=dom(array_shift($reaction),"<div",1);
			$comments=filter($comments,function($str){
				return strpos($str[0],"View more comments…")===false&&
							 strpos($str[0],"View previous comments…")===false&&
							 strpos($str[0],"<span>View previous replies</span>")===false&&
							 strpos($str[0],"<span>View more replies</span>")===false;
			});
			if($comments[1])
				$next=dom($comments[1][0][0],"<a",1)[0][1]["href"];

			$comments=$comments[0];
			$comments=array_map(function ($cmt_html) use (&$parent){
				$id=intval($cmt_html[1]["id"]);
				return new Comment($id,$cmt_html[0],$parent);
			},$comments);
		}
		return [
			"items"=>isset($comments)?$comments:[],
			"form"=>isset($form)?$form:[],
			"next"=>isset($next)?$next:""
		];
	}
	/**
		*make this comment identical to @param $comment
	**/
	private function copyFrom(Comment $comment){
		$this->info=$comment->info;
		$this->subcomments_info=$comment->subcomments_info;
		$this->html=$comment->html;
		$this->parent=$comment->parent;
	}

}


 ?>