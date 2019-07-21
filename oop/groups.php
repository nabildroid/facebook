<?php 
class Groups extends Common{
	public $parent=null;
	function __construct($parent){
		$this->parent=$parent;
	}
	public function all(){
		$html=$this->http("groups/?seemore");
		$html=dom(dom($html,"<table")[1],"<a",1);
		array_shift($html);
		//get 
		return array_map(function ($id){
			// get  the id from url
			$id=$id[1]["href"];
			$id=$this->grabGroupIdFromURL($id);
			
			return new Group($id,$this);
		}, $html);
	}
	public function Group($id){
		return new Group($id,$this);
	}
	public static function grabGroupIdFromURL($url){
		return strcut($id,strpos($id,"s/")+3,strpos($id,"?")-1)[0];
	}
}




class Group{
	public $parent=null;
	public $id=null;
	function  __construct($id,$parent){
		$this->parent=$parent;
		$this->id=$id;
	}
	//Group action
	public function join(){
		info("Group(".$this->id.") has been liked");
	}
	public function disjoin(){
		info("Group(".$this->id.") has been unliked");
	}
	public function data(){
		info("Group information[id:".$this->id."]");
	}
	//postes actions
	public function postes(){
		$ids=["efkef","pojzpgjpzr","sjpogjpr","pzjfrf68"];
		return array_map(function ($id){
			return $this->post($id);
		}, $ids);
	}
	public function post($id){
		return new Post($id,$this);
	}


}




 ?>