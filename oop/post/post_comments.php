<?php 
trait post_comments{
	//get comments
	public function comments($page=0){
		$this->fetch();
		if(isset($this->childs["items"][$page]))
			return $this->childs["items"][$page];
		else{
			//prepare the url
			$next=$this->id;
			if($this->childs["next_page"])
				$next=$this->childs["next_page"];
			//note: a desester here
			
			for ($i=count($this->childs["items"]); $i <=$page; $i++) { 
				$this->http($next);
				if(self::detectType($this->html))
					$data=$this->splitImageHtml();
				else
					$data=$this->splitPostHtml();
				$this->parseComments($data["comments_html"]);
				$next=$this->childs["next_page"];
			}
			return $this->childs["items"][count($this->childs["items"])-1];
		}
  }
  
  //grab all comments from the first page
  private function parseComments($reaction){
  	$comments=Comment::parseComments($reaction,$this);
  	
  	$this->childs["items"]=array_merge($this->childs["items"],[$comments["items"]]);
  	$this->childs["next_page"]=$comments["next_page"];	
  	$this->childs["add"]=$comments["add"];
  }


}


 ?>