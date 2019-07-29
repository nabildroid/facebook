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
		$posts=array_map(function ($post){
			$info=Post::GetInfoFromListedPost($post);
			return new Post($info["from"]["id"],$this,$info);
		},$posts);
		return $posts;
	}
}




 ?>