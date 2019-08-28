<?php 
trait post_parsesource{
	//grab the user who publish this post and in which section (group|share from)
	static function parseSource($source,$data=""){
		$values=[
			"id"=>"",
			"origin_post"=>"", //if this post is a sharing of another post
			"user"=>"", 			 //the author id
			"page"=>"",
			"group"=>""
		];

		//delete any <a that does't has href
		$source=filter($source,function($a){return isset($a[1]["href"]);})[0];
		///////Get Data from JsonData
		if(isset($data["top_level_post_id"]))
			$values["id"]=$data["top_level_post_id"];

		if(isset($data["content_owner_id_new"]))
			$values["user"]=$data["content_owner_id_new"];

		if(isset($data["page_id"]))
			$values["page"]=$data["page_id"];

		if(isset($data["group_id"]))
			$values["group"]=$data["group_id"];

		if(isset($data["original_content_id"]))
			$values["origin_post"]=$data["original_content_id"];

		///////Get Data from html
		
		//difference between user and page in header is the page name always end with /? but the user end with only ?


		$selector=[
		 	"group"=>"/groups/", //get group id
			"origin_post"=>"/story.php?", //get origin post id
			"page"=>"/?",//get page id Note::always the pageId has identifier "/?"
			"user"=>"" //any thing left is for user i guess
		];

		foreach ($selector as $key => $value) {
			//!value condition is for case of empty $selector["user"] value so that helps to take lefts
			$source=filter($source,function($a)use($value){
					return !$value||strpos($a[1]["href"],$value)===0;
			});
			if(isset($source[0][0]))
				$values[$key]=$source[0][0][1]["href"];
			$source=$source[1];
		}
		//process ids
		$values["user"]=Profile::idFromUrl($values["user"]);

		return $values;
	}
}


 ?>