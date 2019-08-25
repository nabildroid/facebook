<?php 
trait post_splithtml{
	//get the type of such post if it's normal post or image post
	static function detectType($html){
		//0 post|0 group_post|0  page_post|1 image|
		if(count(dom($html,'id="m_story_permalink_view"')))
			return 0;
		else if(count(dom($html,'id="MPhotoContent"')))
			return 1;
		else return false;
	}

	/**
	 * get information about such post from it html
	 * but this html grabbed from list of posts like in main facebook page (user wall)
	 */
	static function GetInfoFromListedPost($post){
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
	//grab information about image post 
	private function splitImageHtml($html){
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
		$image=findDom($image,"View Full Size");

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
	private function splitPostHtml($html){
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
		$content=$html;

		$reaction=filter(doms($reaction,["<div","<div"]))[0];
		$actions=dom(array_shift($reaction),"<a",1);//##### like action
		//get link of like action
		$like_link=filter($actions,function($action){
			return strpos($action[0],"Like")!=false;
		})[0][0][1]["href"];
		/**
		 * @todo presentation criteria is not efficient way because it attribute and exist in picture (not tested in all situation)
		 */
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
			"content"=>$content,
			"likes_number"=>$likes,
			"like_link"=>$like_link,
			"aready_liked"=>$areadyliked,
			"comment_html"=>$reaction
		];
	}
}


 ?>