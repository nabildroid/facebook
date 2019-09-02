<?php 
namespace Facebook\Comment;
use Facebook\Utils\Html;
use Facebook\Utils\Util;
use Facebook\Utils\Content;

use Facebook\Profile\Profile;
use Facebook\Post\Post;

trait parse{
	/**
	 * this works only if html(section of comments with publish form) is provided
	 * parse single comment html 
	 */
	private function parseSingleComment(){
		$this->html=Html::dom($this->html,"<div")[0];
		$user=Html::dom(Html::dom($this->html,"<h3")[0],"<a",1)[0][1]["href"];
		
		$html=Html::dom($this->html,"<div",1);
		//delete any div with empty content
		$html=Util::filter($html,function($div){return trim($div[0])==true;})[0];
		//separate between content and tools (likes reply..)

		$tools=["",array_pop($html)];//[reaction tool , reply section]
		if(strlen($tools[1][0])<600)//generally the reply section has short html content
			{$tools[0]=array_pop($html);}
		else{$tools=[$tools[1],""];}

		//Note::html contain the content 

		$reaction=Html::dom($tools[0][0],"<a",1);
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
			$like_link=Util::filter($reaction,function($tool){
				return $tool[0]=="Like";
			});
			if(isset($like_link[0][0][1]["href"]))
				$like_link=$like_link[0][0][1]["href"];
			else $like_link="";
		}
		//get reply like
		$reply_link=Util::filter($reaction,function($tool){
			return $tool[0]=="Reply";
		})[0];
		if(isset($reply_link[0][1]["href"]))$reply_link=$reply_link[0][1]["href"];
		else $reply_link="";

		//get reply_number
		$reply_number=0;
		if($tools[1]){
			$reply=Html::dom($tools[1][0],"<a",1)[0];
			preg_match_all("/\d+/",$reply[0],$reply_number);
			$reply_number=intval($reply_number[0][0]);
		}

		//join all content;
		$content="";
		foreach ($html as $h)
			$content.=$h[0];

		$this->content=Content::parse($content);
		$this->user=new Profile($this,Profile::idFromUrl($user));
		$this->likes["length"]=$likes;
		$this->likes["url"]=$likes_users_link;
		$this->likes["like"]=$like_link;
		$this->childs["length"]=$reply_number;
		$this->childs["next_page"]=$reply_link;
		//note: parse doesn't provide add form (html) to reply (add)
	}

	/**
	 * sometimes facebook return page that contain all comments (replys)
	 * ( like when you click to reply ), but not the main post
	 * @param html content of comment page
	 * @return array [replys=>["form","replys"],origin_post=>"origin_post"]
	 */
	private function splitReplys(){
		$data=Html::doms($this->html,['<div','<div','<div']);
		$origin_post="";
		if(strpos($data[0],"<a")===0){
			$origin_post=Html::dom($data[0],"<a",1)[0];
			$origin_post=$origin_post[1]["href"];
		}
		//get only replys and form for submit new reply
		//delete before last div if it's not replys because the formal div number is 4 divs 
		if(count($data)<4)
			$data[count($data)-2]="";


		$data=[$data[count($data)-2],$data[count($data)-1]];
		return ["replys"=>$data,"origin_post"=>$origin_post];
	}

	//grab all comments from the post page
	static function parseCommentSection($reaction,$parent){
		if(isset($reaction[0])){

			//indicators of pagination
			$indicatorsOfNextPage=["View more comments…","<span>View more replies</span>","<span>View next replies</span>"];
			$indicatorsOfPrevPage=["View previous comments…","<span>View previous replies</span>"];
			$indicators=array_merge($indicatorsOfNextPage,$indicatorsOfPrevPage);

			//get form of reply (add reply html form) if the post allowing to write comment/reply
			$reaction=Util::filter($reaction,function($part){
				return strpos($part,"<form")===0;
			});
			$add=$reaction[0];
			$comments=$reaction[1];
			
			if($comments){
				$comments=Html::dom(array_shift($comments),"<div",1);
				//split pagination links
				$comments=Util::filter($comments,function($str) use(&$indicators){
					return !Util::instr($str[0],$indicators);
				});

				//get filtred url for next page
				if($comments[1]){
					/*
					 * sometimes facebook return as indicator of next page "View more comments…"
					 * or "View previous comments…" or both so we need stor the initiale indicator
					 * sepcialy when getting replys 
					 */
					if(isset($parent->childs["next_page_indicator"])){
						if(!$parent->childs["next_page_indicator"]){
							$pagination_caption=Util::instr($comments[1][0][0],$indicators);
							$parent->childs["next_page_indicator"]=$pagination_caption;
						}
						$next=Html::findDom($comments[1],$parent->childs["next_page_indicator"]);
					}else{
						//get next page with inducator 'View more...'
						$next=Html::findDom($comments[1],"View more");
					}

					if(isset($next[0])&&$next[0])
						$next=Html::dom($next[0],"<a",1)[0][1]["href"];
					else $next="";
				}
				//create comments
				$comments=$comments[0];
				$comments=array_map(function ($cmt_html) use (&$parent){
					$id=intval($cmt_html[1]["id"]);
					if(!$id)return;
					$cmt=new Comment($parent,$id);
					$cmt->fixHttpResponse($cmt_html[0],$id);
					return $cmt;
				},$comments);
				$comments=Util::filter($comments)[0];
			}
		}
		return [
			"items"=>isset($comments)?$comments:[],
			"next_page"=>isset($next)?$next:"",
			"add"=>isset($add[0])?$add[0]:[]
		];
	}
}

 ?>