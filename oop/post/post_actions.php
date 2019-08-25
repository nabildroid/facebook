<?php 
trait post_actions{
	//like action
	public function like(){
		$this->fetch();
		if($this->info["like_link"]&&!$this->info["likes"]["me"]){
			$this->http($this->info["like_link"]);
			$this->info["likes"]["me"]=true;
			return true;
		}
		else return false;
	}
	//deslike action
	public function unlike(){
		$this->fetch();
		if($this->info["like_link"]&&$this->info["likes"]["me"]){
			$this->http($this->info["like_link"]);
			$this->http($this->dom("<a",1)[0][1]["href"]);
			$this->info["likes"]["me"]=false;
			return true;
		}
	}
	//comment action
	public function comment($txt){
		$this->fetch();
		if(!$this->comments_info["form"])
			$this->fetch();
		$form=dom($this->comments_info["form"],"<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}
}

?>