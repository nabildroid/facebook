<?php 
trait profile_requestFriend{
	public function sendFriendRequest(){
		$this->permission(0);
		$this->fetch();

		$url=findDom($this->info["actions"],"Add Friend");
		if(isset($url[1]["href"])){
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function confirmFriendRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=findDom($this->info["actions"],"Confirm Friend");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function rejectFriendRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=findDom($this->info["actions"],"Delete Request");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
}


 ?>