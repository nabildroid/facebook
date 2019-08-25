<?php 
trait group_publish{
	/**
	 * @param $param, is array(key/pair) it takes text(string),images(array),privacy(string),tags(array) and all are options
	 */
	public function publish($param){
		$this->fetch();
		//prepare paramater
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
			"tags"=>[]
		],$param);

		//main function
		$this->http($this->id());
		$form=findDom($this->dom("<form",1),"<textarea");	


		$forceInput=[];
		//tag friends
		if($param["tags"])
			$forceInput["users_with"]=join($param["tags"],",");

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
		return $this->postAppeared()?"published":"pending";
	}
}

 ?>