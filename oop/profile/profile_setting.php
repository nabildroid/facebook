<?php 
trait profile_setting{
	public function setProfilePhoto($url){
		$this->permission(1);
		$this->fetch();

		$this->http("photos/upload/?profile_pic");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function setCoverPhoto($url){
		$this->permission(1);
		$this->fetch();

		$this->http("photos/upload/?cover_photo");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
	}
	public function setBio($txt){
		$this->permission(1);
		$this->fetch();

		$this->http("profile/basic/intro/bio");
		$form=$this->dom("<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}
}

 ?>