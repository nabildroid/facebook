<?php 
namespace Facebook\Post;
use Facebook\Utils\Html;

trait actions{
	//like action
	public function like(){
		$this->fetch();
		if($this->likes["like"]&&!$this->likes["mine"]){
			$this->http($this->likes["like"]);
			$this->likes["mine"]=true;
			return true;
		}
		else return false;
	}
	//deslike action
	public function unlike(){
		$this->fetch();
		if($this->likes["like"]&&$this->likes["mine"]){
			$this->http($this->likes["like"]);
			$this->http($this->dom("<a",1)[0][1]["href"]);
			$this->likes["mine"]=false;
			return true;
		}
	}
	//comment action
	public function comment($txt){
		$this->fetch();
		if(!$this->childs["add"])
			$this->fetch(1);
		$add=Html::dom($this->childs["add"],"<form",1)[0];
		$this->submit_form($add[0],$add[1]["action"],[$txt]);
	}
}

?>