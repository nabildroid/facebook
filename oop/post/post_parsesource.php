<?php 
trait post_parsesource{
	//grab the user who publish this post and in which section (group|share from)
	static function parseSource($source,$data=""){
		$user="";		$page="";		$group="";		$origin_post=""; $id="";
		//delete any <a that does't has href
		$source=filter($source,function($a){return isset($a[1]["href"]);})[0];
		///////Get Data from JsonData
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

		///////Get Data from html
		
		//difference between user and page in header is the page name allways end with /? but the user end with only ?

		//get the group if it exist
		if(!$group){
			$temp=filter($source,function($a){
					return strpos($a[1]["href"],"/groups/")===0;
			});
			if(isset($temp[0][0]))$group=$temp[0][0][1]["href"];
		}
		//get origin post if it exist
		if(!$origin_post){
			$temp=filter($temp[1],function($a){
					return strpos($a[1]["href"],"/story.php?")===0;
			});
			if(isset($temp[0][0]))$origin_post=$temp[0][0][1]["href"];
		}
		//get pageId if it exist Note::allways the pageId has identifier "/?"
		if(!$page){
			$temp=filter($temp[1],function($a){
					return strpos($a[1]["href"],"/?")!=false;
			});
			if(isset($temp[0][0]))$page=$temp[0][0][1]["href"];
		}
		//get user
		if(!$user){
			//note: why did i add &&!$page to below condition 
			if(isset($temp[1][0][1]["href"]))
				$user=$temp[1][0][1]["href"];
		}

		return [
			"user"=>Profile::idFromUrl($user),
			"id"=>$id,
			"origin_post"=>$origin_post,
			"page"=>$page,
			"group"=>$group,
		];
	}
}


 ?>