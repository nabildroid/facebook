<?php 
namespace Facebook\Wall;
use Facebook\Utils\Html;
use Facebook\Utils\Util;

trait publish{
	/**
	 * @param $param, is array(key/pair) it takes text(string),images(array),privacy(string),tags(array) and all are options
	 * @return Post
	*/
	public function publish($param){
		//prepare paramater
		$param=Util::mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
			"privacy"=>"",
			"tags"=>[]
		],$param);

		//main function
		$this->http();
		$form=Html::findDom($this->dom("<form",1),"<textarea");	

		//handle logic error taging friends in private post or tags is note type of profiles array
		if($param["tags"]){
			$error_type=0;
			foreach ($param["tags"] as $user) 
				if(!is_a($user,"Profile")){
					$error_type=1;
					break;
				}
			if($error_type)
				throw new \Exception("taged users must be array of Profiles", 1);
			elseif(($this->currentPrivacy($form[0])==="only me"&&!$param["privacy"])||$param["privacy"]==="only me")
			throw new \Exception("trying to tag friend in private post ", 1);
		}

		$forceInput=[];
		//tag friends
		if($param["tags"]){
			$param["tags"]=array_map(function($tag){return $tag->getId(1);},$param["tags"]);
			$forceInput["users_with"]=join($param["tags"],",");
		}

		if(!$param["images"]){//publish text
			//add privacy if exist to $forceInpute
			$privacy=$this->changePrivacy($form,$param["privacy"]);
			if($privacy)$forceInput["privacyx"]=$privacy;
			//publish post
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"",$forceInput);

		}else{//publish image
			//fecth upload page
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"view_photo");
			//upload images
			$form=Html::dom($this->html,"<form",1)[0];
			$this->submit_form($form[0],$form[1]["action"],$param["images"],"add_photo_done");
			$form=Html::dom($this->html,"<form",1)[0];
			//add privacy if exist to $forceInpute
			$privacy=$this->changePrivacy($form,$param["privacy"]);
			if($privacy)$forceInput["privacyx"]=$privacy;
			//publish post
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"view_post",$forceInput);
		}
		//get such new post
		$this->fixHttpResponse($this->html);
		foreach ($this->posts() as $post)
			if($post->getAdmin()){return $post;}
	}
}
?>