<?php 
class Comment extends common{
	public $parent=null;
	public $html="";
	public $info=[
		"user"=>"",
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
	function __construct($html,$parent){
		$this->parent=$parent;
		$this->html=$html;
		$this->parse();
	}
	public function parse(){
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
		$this->info["content"]=$this->makeContentFromHtmlContent($html);
		$this->info["user"]=$user;
		$this->info["likes"]["length"]=$likes;
		$this->info["likes"]["all"]=$likes_users_link;
		$this->info["like_link"]=$like_link;
		$this->info["reply_length"]=$reply_number;
		$this->subcomments_info["next"]=$reply_link;
	}

	public function makeContentFromHtmlContent($html){
		$content="";
		foreach ($html as $div) {
			$content.=$div[0];
		}
		return $content;

	}

	//action
	public function like(){
		if($this->info["like_link"]){
			$this->http($this->info["like_link"]);
			return true;
		}else return false;

	}
	public function users_likes(){
		if(!$this->info["likes"]["users"]&&$this->info["likes"]["all"]){
			$this->info["likes"]["users"]=Post::fetch_users_likes($this->info["likes"]["all"],$this);
		}
		return $this->info["likes"]["users"];
	}
	public function subcomments($page){
		if(is_numeric($page)){
			if(isset($this->subcomments_info["items"][$page]))
				return $this->subcomments_info["items"][$page];
			else{
				for ($i=0; $i <=$page; $i++) { 
					if(!$this->subcomments_info["next"])continue;
					$this->http($this->subcomments_info["next"]);
					$data=doms($this->html,['id="objects_container"','<div','<div','<div']);
					//get only replys and form for submit new reply
					$data=[$data[count($data)-2],$data[count($data)-1] ];
					$data=$this->parseComments($data,$this);

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
	//staic global funcitons
	//grab all comments from the first page
	static public function parseComments($reaction,$parent){
		if(strpos($reaction[0],"<form")===0)
			$form=array_shift($reaction);
		else $form=array_pop($reaction);
		$comments=dom(array_shift($reaction),"<div");
		$comments=filter($comments,function($str){
			return strpos($str,"View more comments…")===false&&
						 strpos($str,"View previous comments…")===false&&
						 strpos($str,"<span>View previous replies</span>")===false&&
						 strpos($str,"<span>View more replies</span>")===false;
		});
		if($comments[1])
			$next=dom($comments[1][0],"<a",1)[0][1]["href"];

		$comments=$comments[0];
		$comments=array_map(function ($cmt_html) use (&$parent){
			return new Comment($cmt_html,$parent);
		},$comments);

		return [
			"items"=>$comments,
			"form"=>$form,
			"next"=>isset($next)?$next:""
		];
	}

}


 ?>