<?php 
class Wall extends common{
	public $parent=null;
	function  __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	/**
		* get all suggestion posts that facebook provide in main page 
		* each time fb main page will show new set of posts so it's
		* not necessary to make pagination in such function
		* @return array of posts
	*/
	public function all(){
		$this->http();
		//get posts
		$posts=$this->dom('role="article"',1);
		//create posts objects
		$tempPosts=[];
		foreach ($posts as $post){
			$info=Post::GetInfoFromListedPost($post);
			if($info)
				$tempPosts[]=new Post($info["from"]["id"],$this,$info);
		}
		return $tempPosts;
	}
	

	private function currentPrivacy($formHtml){
		$current_privacy=dom($formHtml,'name="view_privacy"',1)[0];
		if(isset($current_privacy[1]["value"]))
			return strtolower(trim($current_privacy[1]["value"]));
		else return false;
	}
	private function changePrivacy($form,$privacy){
		if($privacy){
			if($this->currentPrivacy($form[0])!=$privacy){

				/*if form contain csid(as hidden input), so that easy to get the code of any privacy by only one request then force such value to publish (submit last form).
				but if the form doesn't contain it we must submit the privacy input then we can get the csid and do the half part of the first condition*/
				$csid=dom($form[0],'name="csid"');
				if(!isset($csid[0])){
					$this->submit_form($form[0],$form[1]["action"],[],"view_privacy");
					preg_match_all("/csid=.+?(?=&)/",$this->html,$csid);
					$csid=substr($csid[0][0],5);
				}else $csid=$csid[0][1]["value"];

				$this->http("/composer/mbasic/?csid=".$csid."&errcode=0&cwevent=composer_entry&filter_type=0&priv_expand=see_all&view_privacy");
				$html=$this->dom("<table")[0];
				$html=doms($html,["<td","<tr"]);

				foreach ($html as $tr) {
					$name=dom($tr,"<div")[1];
					if(isset(dom($name,"<strong")[0]))$name=dom($name,"<strong")[0];
					if(strtolower($name)==$privacy){
						$privacy=dom($tr,"<a",1);
            $privacy=array_pop($privacy)[1]["href"];
            preg_match_all("/privacyx=.+?(?=&)/",$privacy,$privacy);
            $privacy=substr($privacy[0][0],9);
            return $privacy;
					}
				}
			}
		}
		return false;
	}
	/**
	 * @param $param, is array(key/pair) it takes text(string),images(array),privacy(string),tags(array) and all are options
	 * @return Post
	*/
	public function publish($param){
		//prepare paramater
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[],
			"privacy"=>"",
			"tags"=>[]
		],$param);

		//main function
		$this->http();
		$form=findDom($this->dom("<form",1),"<textarea");	

		//handle logic error taging friends in private post
		if($param["tags"]&&($this->currentPrivacy($form[0])==="only me"||$param["privacy"]==="only me"))
			throw new Exception("trying to tag friend in private post ", 1);

		$forceInput=[];
		//tag friends
		if($param["tags"])
			$forceInput["users_with"]=join($param["tags"],",");

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
			$form=dom($this->html,"<form",1)[0];
			$this->submit_form($form[0],$form[1]["action"],$param["images"],"add_photo_done");
			$form=dom($this->html,"<form",1)[0];
			//add privacy if exist to $forceInpute
			$privacy=$this->changePrivacy($form,$privacy);
			if($privacy)$forceInput["privacyx"]=$privacy;
			//publish post
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"view_post",$forceInput);
		}
		//get such new post
		$this->fixHttpResponse($this->html,"");
		return $this->all()[0];
	}
}




 ?>