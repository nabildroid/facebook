<?php
namespace Facebook\Profile;
use Facebook\Utils\Html;
trait requestfriend{
	public function sendFriendRequest(){
		$this->permission(0);
		$this->fetch();

		$url=Html::findDom($this->actions,"Add Friend");
        if(!$url)return false;
        $this->undefined_array_index($url,[1,"href"],"send friend request link not found");

        $this->http($url[1]["href"]);

        /**sometimes facebook throw a form to confirm after trying to send friend request */
        $confirmFrom=$this->dom("<form",1);
        if(count($confirmFrom)==1&&Html::findDom($confirmFrom[0],'value="Confirm"')){
            $form=$confirmFrom[0];
            $this->submit_form($form[0],$form[1]["action"]);
        }
        return true;
	}
	public function confirmUserRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=Html::findDom($this->actions,"Confirm Friend");
            $this->undefined_array_index($url,[1,"href"],"confirm friend request link not found");

			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
	public function rejectUserRequest(){
		$this->permission(0);
		$this->fetch();

		if($this->friendAsk()){
			$url=Html::findDom($this->actions,"Delete Request");
            $this->undefined_array_index($url,[1,"href"],"reject friend request link not found");

			$this->http($url[1]["href"]);
			return true;
		}else return false;
	}
}


 ?>