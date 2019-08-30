<?php 
trait page_publish{
	/**
	 * if it's my page
	 * @param $param, is array(key/pair) it takes text(string),images(array)
	 */
	public function publish($param){
		$this->permission(1);
		$this->fetch();

		//prepare paramater
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
		],$param);

		$form=$this->childs["add"];
		$forceInput=[];

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
		$this->fixHttpResponse($this->html);
		return $this->posts()[0];
	}
}


 ?>