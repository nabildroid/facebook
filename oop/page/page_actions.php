<?php 
trait page_actions{
	//page action
	public function like(){
		$this->permission(0);
		$this->fetch();

		if($this->info["like_link"]&&strpos($this->info["like_link"],"unfan")===false)
			$this->http($this->info["like_link"]);
		return true;
	}
	public function dislike(){
		$this->permission(0);
		$this->fetch();

		if($this->info["like_link"]&&strpos($this->info["like_link"],"unfan")!==false)
			$this->http($this->info["like_link"]);
		return true;
	}

	public function follow(){
	}
}
?>