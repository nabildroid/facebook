<?php 

class Messages extends common{
	public $parent=null;
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	private function fetch_info(){
		$this->http("/messages");
	}
	public function all(){
		$this->fetch_info();
		$msgs=filter($this->dom("<table"),function($table){
			return strpos($table,"/messages/read/")!==false;
		})[0];
		$msgs=array_map(function($m){
			$link=dom($m,"<a",1)[0];
			$friend_name=$link[0];
			$id=$link[1]["href"];//contain friend id and 
			$snippet=dom($m,"<span")[0];
			return new Message([
				"id"=>$id,
				"friend"=>$friend_name,
			],$this);
		},$msgs);
		return $msgs;
	}
								

}

 ?>