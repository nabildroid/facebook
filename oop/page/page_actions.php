<?php 
trait page_actions{
	//page action
	public function like(){
		if(!$this->likes["like"])
			$this->fetch();
		$this->permission(0);

		if($this->likes["like"]&&strpos($this->likes["like"],"unfan")===false)
			$this->http($this->likes["like"]);
		return true;
	}
	public function unlike(){
		if(!$this->likes["like"])
			$this->fetch();
		$this->permission(0);

		if($this->likes["like"]&&strpos($this->likes["like"],"unfan")!==false)
			$this->http($this->likes["like"]);
		return true;
	}

	public function follow(){
	}
}
?>