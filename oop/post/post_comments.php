<?php 
trait post_comments{
	//get comments
	public function comments($page=0){
		$this->fetch();
		if(isset($this->comments_info["items"][$page]))
			return $this->comments_info["items"][$page];
		else{
			for ($i=0; $i <=$page; $i++) { 
				$this->http($this->comments_info["next"]);
				if(self::detectType($this->html))
					$data=self::splitImageHtml($this->html);
				else
					$data=self::splitPostHtml($this->html);
				$this->parseComments($data["comment_html"]);
			}
			return $this->comments_info["items"][count($this->comments_info["items"])-1];
		}
  }
  
  //grab all comments from the first page
  private function parseComments($reaction){
  	$comments=Comment::parseComments($reaction,$this);
  	$this->comments_info["items"]=array_merge($this->comments_info["items"],[$comments["items"]]);
  	$this->comments_info["next"]=!empty($comments["next_page"])?$comments["next_page"]:"";
  	
  	if(!$this->comments_info["form"])		
  		$this->comments_info["form"]=$comments["add"];
  }


}


 ?>