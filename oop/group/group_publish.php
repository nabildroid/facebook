<?php 
trait group_publish{
	/**
	 * @param $param, is array(key/pair) it takes text(string),images(array),privacy(string),tags(array) and all are options
	 * @return a post or string "pending"
	 */
	public function publish($param){
		$this->fetch();
		if($this->admin!=1)
			throw new Exception("user didn't have the permission to post in group");	
		//prepare paramater
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
			"tags"=>[]
		],$param);

		//main function
		$this->http($this->id);
		$form=findDom($this->dom("<form",1),"<textarea");	


		$forceInput=[];
		//tag friends
		if($param["tags"]){
			//check if all tagged users are Profile type
			$error_type=0;
			foreach ($param["tags"] as $user) 
				if(!is_a($user,"Profile")){
					$error_type=1;
					break;
				}
			if($error_type)
				throw new Exception("taged users must be array of Profiles", 1);
			else
				$param["tags"]=array_map(function($tag){return $tag->getId(1);},$param["tags"]);
			$forceInput["users_with"]=join($param["tags"],",");
			
		}

		if(!$param["images"]){//publish text
			//publish post
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"",$forceInput);

		}else{//publish image
			//fecth upload page
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"view_photo");
			//upload images
			$form=dom($this->html,"<form",1)[0];
			$this->submit_form($form[0],$form[1]["action"],$param["images"],"add_photo_done");
			$form=dom($this->html,"<form",1)[0];
			//publish post
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"view_post",$forceInput);
		}
		//check if post has been submitted or pending 
		$status=$this->postAppeared();
		if($status){
			foreach ($this->posts() as $post){
				$group_source=$post->getSource("group");
				//note: this is not general, sometimes return wrong post, specailly 
				if($post->getAdmin()&&$group_source&&$group_source->getId()==$this->id)
					{return $post;}			
			}
		}
		//the status is not efficient, exactly when publish image facebook derectly will redirect to main facebook page (Wall)
		return "pending";
	}
}

 ?>