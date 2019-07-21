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
		"like"=>"",
		"reply"=>[
			"length"=>0,
			"url"=>""
		]
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
			})[0][0][1]["href"];
		}
		//get reply like
		$reply_link=filter($reaction,function($tool){
			return $tool[0]=="Reply";
		})[0][0][1]["href"];

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
		$this->info["like"]=$like_link;
		$this->info["reply"]["length"]=$reply_number;
		$this->info["reply"]["url"]=$reply_link;
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
		if($this->info["like"])
			$this->http($this->info["like"]);
	}
	public function users_likes(){
		if(!$this->info["likes"]["users"]&&$this->info["likes"]["all"]){
			$this->info["likes"]["users"]=$this->parent->likes($this->info["likes"]["all"]);
		}
		return $this->info["likes"]["users"];
	}
}


 ?>