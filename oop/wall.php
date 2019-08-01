<?php 
class Wall extends common{
	public $parent=null;
	function  __construct($parent){
		$this->parent=$parent;
	}
	public function all(){
		$this->http();
		$html=$this->dom('<div id="m_newsfeed_stream"')[0];
		//get next page
		$nextPage=dom($html,'<a',1);
		$nextPage=array_pop($nextPage)[1]["href"];
		//get posts
		$posts= array_filter(dom($html,"<div"));
		$posts=dom(array_pop($posts),"<div",1);
		//create posts objects
		$tempPosts=[];
		foreach ($posts as $post){
			$info=Post::GetInfoFromListedPost($post);
			if($info)
				$tempPosts[]=new Post($info["from"]["id"],$this,$info);
		}
		return $tempPosts;
	}

	public function publish($txt="",$images=[]){
		$this->http();
		$form=$this->dom("<form",1)[1];
		$this->submit_form($form[0],$form[1]["action"],[$txt],$images?"view_photo":"");
		if($images){
			$form=dom($this->html,"<form",1)[0];
			$this->submit_form($form[0],$form[1]["action"],$images,"add_photo_done");
			$form=dom($this->html,"<form",1)[0];
			var_dump($form);
			$this->submit_form($form[0],$form[1]["action"],[$txt],"view_post");
		}
	}
}




 ?>