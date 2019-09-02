<?php 
namespace Facebook\Post;
use Facebook\Comment\Comment;

trait comments{
	//get comments
	public function comments($page=0){
		$this->fetch();
		if(isset($this->childs["items"][$page]))
			return $this->childs["items"][$page];
		else{
			//prepare the url
			$next=$this->id;
			if($this->childs["next_page"]!==null)
				$next=$this->childs["next_page"];
			
			for ($i=count($this->childs["items"]); $i <=$page; $i++) { 
				if(!$next)break;
				$this->http($next);
				$type=self::detectType($this->html);
				if($type===1)
					$data=$this->splitImageHtml();
				elseif($type===0)
					$data=$this->splitPostHtml();
				else
					continue;


				$this->parseCommentSection($data["comments_html"]);
				$next=$this->childs["next_page"];
			}
			if(isset($this->childs["items"][$page]))
				return $this->childs["items"][$page];
			else return [];
		}
  }
  
  //grab all comments from the first page
  private function parseCommentSection($reaction){
  	$comments=Comment::parseCommentSection($reaction,$this);
  	$this->childs["items"]=array_merge($this->childs["items"],[$comments["items"]]);
  	$this->childs["next_page"]=$comments["next_page"];	
  	$this->childs["add"]=$comments["add"];
  }


}


 ?>