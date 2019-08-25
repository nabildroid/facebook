<?php 
trait comment_wholikes{
	public function wholikes(){
		$this->fetch();
		if(!$this->likes["users"]&&$this->likes["url"]){
			$this->likes["users"]=Post::fetch_wholikes($this->likes["url"],$this);
		}
		return $this->likes["users"];
	}
}

 ?>