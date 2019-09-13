<?php
namespace Facebook\Profile;
trait setter{
	public function setProfilePicture($url){
		$this->permission(1);
		$this->fetch();

		$this->http("photos/upload/?profile_pic");
		$form=$this->dom("<form",1);
        if($this->undefined_array_index($form,[0,1,"action"],"form for uploading the profile picture doesn't exist"))
            $form=$form[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
        //return such new picture post
        $this->refetchFromOldHtml();
        return $this->getPicture("profile");
	}
	public function setCoverPicture($url){
		$this->permission(1);
		$this->fetch();

		$this->http("photos/upload/?cover_photo");
		$form=$this->dom("<form",1);
        if($this->undefined_array_index($form,[0,1,"action"],"form for uploading the cover picture doesn't exist"))
            $form=$form[0];
		$this->submit_form($form[0],$form[1]["action"],[$url]);
        //return such new picture post
        $this->refetchFromOldHtml();
        return $this->getPicture("cover");
	}
	public function setBio($txt){
		$this->permission(1);
		$this->fetch();

		$this->http("profile/basic/intro/bio");
		$form=$this->dom("<form",1);
        if($this->undefined_array_index($form,[0,1,"action"],"form of bio submition doesn't exist"))
            $form=$form[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
        //return such new bio
        $this->refetchFromOldHtml();
        return $this->getBio();
	}
}

 ?>