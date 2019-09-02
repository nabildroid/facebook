<?php 
namespace Facebook\Comment;
trait subcomments{
	public function subcomments($page=0){
		$this->fetch();
		if(isset($this->childs["items"][$page]))
			return $this->childs["items"][$page];
		else{
			//prepare the url
			$next=$this->childs["next_page"];
			for ($i=count($this->childs["items"]); $i <=$page; $i++) { 
				if(!$next)break;
				$this->http($next);
				
				$data=$this->splitReplys()["replys"];
				$data=self::parseCommentSection($data,$this);
				
				$next=$data["next_page"];
				$this->childs["next_page"]=$next;
				$this->childs["items"]=array_merge($this->childs["items"],[$data["items"]]);
				$this->childs["add"]=$data["add"];

			}
			if(isset($this->childs["items"][$page]))
				return $this->childs["items"][$page];
			else return [];
		}
	}
}


 ?>