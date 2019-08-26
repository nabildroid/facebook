<?php 
trait profile_requestFriend{
	public function sendFriendRequest(){
		$this->permission(0);
		$this->fetch();

		$url=findDom($this->actions,"Add Friend");
		if(isset($url[1]["href"])){
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function confirmUserRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=findDom($this->actions,"Confirm Friend");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function rejectUserRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=findDom($this->actions,"Delete Request");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
}


 ?>