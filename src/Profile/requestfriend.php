<?php 
namespace Facebook\Profile;
trait requestfriend{
	public function sendFriendRequest(){
		$this->permission(0);
		$this->fetch();

		$url=Util::findDom($this->actions,"Add Friend");
		if(isset($url[1]["href"])){
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function confirmUserRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=Util::findDom($this->actions,"Confirm Friend");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function rejectUserRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=Util::findDom($this->actions,"Delete Request");	
			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
}


 ?>