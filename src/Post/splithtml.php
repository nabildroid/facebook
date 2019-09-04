<?php 
namespace Facebook\Post;
use Facebook\Utils\Html;
use Facebook\Utils\Util;

trait splithtml{
	//get the type of such post if it's normal post or image post
	static function detectType($html){
		//0 post|0 group_post|0  page_post|1 image|
		if(count(Html::dom($html,'id="m_story_permalink_view"')))
			return 0;
		else if(count(Html::dom($html,'id="MPhotoContent"')))
			return 1;
		else return false;
	}

	/**
	 * get information about such post from it html
	 * but this html grabbed from list of posts in main facebook page (user wall)
	 * the hidden @param $this->html must be a array contain [attribute,html] of post
	 */
	private function parseInlinePost(){
		$info=[];
		$attributes=Util::jsondecode($this->html[1]["data-ft"]);
		$html=Html::dom($this->html[0],"<div");

		//data [id,user,parentType]

		//html [content,likes_length]
		$content=Html::dom($html[0],"<div");

		$source=Html::dom(array_shift($content),"<a",1);

		$text=join($content,"");
		if(!isset($html[1])){
			var_dump("_______ERROR______issue#14");
			var_dump($this->html);
			exit;
		}
		$actions=Html::findDom(Html::dom($html[1],"<div"),"Full Story");
		if(!$actions)return;
		$actions=Html::dom($actions,"<a",1);

		//get link of like action
		$like_link=Html::findDom($actions,[0=>"Like"]);
		if(isset($like_link[1]["href"]))
			$like_link=$like_link[1]["href"];
		else $like_link="";
		
		$alreadyliked=isset(Html::findDom($actions,"<b>Like</b>")[0]);

		$likes=Util::filter($actions,function($ra){
			return Util::instr($ra[0],"<img");
		})[0];
		if(isset($likes[0][0])){
			$likes=substr($likes[0][0],strpos($likes[0][0],"</span>")+7);
			if($likes)$likes=self::LikesStringToInt($likes);
			else $likes=0;
		}else $likes=0;

		/**
		 * @todo get the number of total comments
		 */
		return [
			"source"=>["html"=>$source,"attribute"=>$attributes],
			"content"=>$text,
			"likes_length"=>$likes,
			"like_link"=>$like_link,
			"already_liked"=>$alreadyliked
		];

	}
	//grab information about image post note:doesn't provide attributes(data-ft)
	private function splitImageHtml(){
		$actions=Html::dom(Html::dom($this->html,'id="MPhotoActionbar"')[0],"<a",1);
		$html=Html::doms($this->html,['id="MPhotoContent"',"<div"]);
		$content=$html[0];// content of the post and the owner and where it came from (group/page/profile) and full-image
		$reaction=$html[1];// section of comments likes and some other actions

		//content
		$content=Html::dom($content,"<div");   //fisrt contain 'from' 'content' the second conatint full-size
		//NOTE:: i gonna suppose that the last div in content[0] is only the content but it's not proved			
		$content[0]=Html::dom($content[0],["<a","<div"],1);//not filtred

		$text=array_pop($content[0])[0];
		$source=$content[0];
		$image=Html::dom($content[1],"<a",1);//content[1] has the link of full_size
		$image=Html::findDom($image,"View Full Size");

		//reaction
		$reaction=Util::filter(Html::doms($reaction,["<div","<div","<div"]))[0];



		//get link of like action
		$like_link=Html::findDom($actions,"Like");
		if(isset($like_link[1]["href"]))
			$like_link=$like_link[1]["href"];
		else $like_link="";

		$alreadyliked=isset(Html::findDom($actions,"presentation")[0]);
		$likes=Html::doms(array_shift($reaction),["<div","<div"]);
		/*
			some times the html of likes show the name of who like not one
		  and if the likes number is 1K my code will read it as 1 
		  so you show also add function that deal with likes number
		*/
		if(isset($likes[0]))
			$likes=Post::LikesStringToInt($likes[0]);
		else $likes=0;

		//get id of such image from his like_link
		$id="";
		preg_match_all("/(identifier=\d+)|\/\d+/",urldecode($like_link),$id);
		if(isset($id[0][0])){
			preg_match_all("/\d+/",$id[0][0],$id);
			if(isset($id[0][0]))
				$id=intval($id[0][0]);
		}
		
		return [
			"source"=>["html"=>$source,"attribute"=>["top_level_post_id"=>$id]],
			"image"=>$image,
			"content"=>$text,
			"likes_length"=>$likes,
			"like_link"=>$like_link,
			"already_liked"=>$alreadyliked,
			"comments_html"=>$reaction
		];
	}
	//grab informaion about normal post
	private function splitPostHtml(){
		$html=html::dom($this->html,'id="m_story_permalink_view"');
		$html=html::dom($html[0],"<div");
		$content=$html[0];// content of the post and the owner and where it came from (group/page/profile) [html with attributes]
		$reaction=$html[1];// section of comments likes and some other actions
		$html=html::dom($content,"<div",1)[0];
		$attributes=Util::jsondecode($html[1]["data-ft"]);//attaribute that contain some json infomation about post [allways the user id is in this data as content_owner_id_new]
		$html=Html::doms($html[0],["<div","<div"]);
		$html=Util::filter($html)[0];
		$source=html::dom(array_shift($html),"<h3")[0];//here you can find the infomation about where 
		$source=html::dom($source,"<a",1);
		$content=$html;

		$reaction=Util::filter(Html::doms($reaction,["<div","<div"]))[0];
		$actions=html::dom(array_shift($reaction),"<a",1);//##### like action
		//get link of like action
		$like_link=Html::findDom($actions,"Like");
		if(isset($like_link[1]["href"]))
			$like_link=$like_link[1]["href"];
		else $like_link="";
		/**
		 * @todo presentation criteria is not efficient way because it attribute and exist in picture (not tested in all situation)
		 */
		$alreadyliked=isset(Html::findDom($actions,"presentation")[0]);

		//get number of likes
		$likes=Html::doms(array_shift($reaction),["<div","<div"]);
		/*
			some times the html of likes show the name of who like not one
		  and if the likes number is 1K my code will read it as 1 
		  so you show also add function that deal with likes number
		*/
		if(isset($likes[0]))
			$likes=Post::LikesStringToInt($likes[0]);
		else $likes=0;

		return [
			"source"=>["html"=>$source,"attribute"=>$attributes],
			"content"=>$content,
			"likes_length"=>$likes,
			"like_link"=>$like_link,
			"already_liked"=>$alreadyliked,
			"comments_html"=>$reaction
		];
	}
}


 ?>