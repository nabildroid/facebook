<?php 
namespace Facebook\Comment;
use Facebook\Utils\Html;
trait actions{
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

		// if add doesn't exist we should get comment subcomments in other word 
		// we must follow link $this->childs["next_pge"] which's link of reply page
		if(!$this->childs["add"])
			$this->subcomments();
		$form=Html::dom($this->childs["add"],"<form",1)[0];
		$this->submit_form($form[0],$form[1]["action"],[$txt]);
	}
}

 ?>