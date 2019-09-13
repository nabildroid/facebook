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
        $this->undefined_array_index($form,0,"wall doesn't have textarea to write on it");

		//handle logic error taging friends in private post or tags is note type of profiles array
		if($param["tags"]){
            $error="";
            if(!Util::is_arrayOf($param["tags"],"Profile"))
				$error="taged users must be array of Profiles";
			elseif(($this->currentPrivacy($form[0])==="only me"&&!$param["privacy"])||$param["privacy"]==="only me")
                $error="trying to tag friend in private post";

            if($error)$this->error($error);
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
			$form=Html::dom($this->html,"<form",1);
            $this->undefined_array_index($form,[0,0],"couldn't get from for upload images");

			$this->submit_form($form[0][0],$form[0][1]["action"],$param["images"],"add_photo_done");
			$form=Html::dom($this->html,"<form",1);
            if($this->undefined_array_index($form,[0,0],"error in uploading images"))
                $form=$form[0];

			//add privacy if exist to $forceInpute
			if($privacy){
                $privacy=$this->changePrivacy($form,$param["privacy"]);
                $forceInput["privacyx"]=$privacy;
            }

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