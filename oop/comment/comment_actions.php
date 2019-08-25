<?php 
trait comment_actions{
	//actions
	public function like(){
		$this->fetch();
		if($this->likes["like"]){
			$this->http($this->likes["like"]);
			return true;
		}else return false;
	}
	/**
	 * reply to comment not to subcomment!!
	 * @todo @return new Comment
	 */
	public function reply($txt){
		$this->fetch();
		if(!$this->childs["add"])
			$this->fetch(1);
		
		$form=dom($this->childs["add"],"<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}
}

 ?>