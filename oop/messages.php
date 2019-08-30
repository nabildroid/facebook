<?php 
class Messages extends common{
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}

	public function recent(){
		$this->http("messages");
		return $this->parseMessages();
	}
	public function request(){
		$this->http("messages/?folder=pending");
		return $this->parseMessages();
	}
	public function unread(){
		$this->http("messages/?folder=unread");
		return $this->parseMessages();
	}
	public function other(){
		$this->http("messages/?folder=other");
		return $this->parseMessages();
	}

	private function parseMessages(){
		$msgs=filter($this->dom("<table"),function($table){
			return strpos($table,"/messages/read/")!==false;
		})[0];
		$msgs=array_map(function($m){
			$link=dom($m,"<a",1)[0];
			$id=$link[1]["href"];//contain friend id and 
			preg_match_all("/[\d]+/",urldecode($id),$id);
			if($id[0][0]==$this->root->profile->getId())
				$id=$id[0][1];
			else $id=$id[0][0];
			//note: add the name pf user to his profile for easy to check if any message in chat is for mine or not
			$snippet=dom($m,"<span")[0];
			return new Message($this,new Profile($this,$id));
		},$msgs);
		return $msgs;
	}
								

}

 ?>