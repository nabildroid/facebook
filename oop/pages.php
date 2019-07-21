<?php 
class Pages{
	public $parent=null;
	function __construct($parent){
		$this->parent=$parent;
	}
	public function all(){
		$ids=["jzpdufp","jefzoi","mùùsdlpl","587dedzlmejfm","pkzpfokofk"];
		return array_map(function ($id){
			return new Page($id,$this);
		}, $ids);
	}
	public function page($id){
		return new Page($id,$this);
	}
}




class Page{
	public $parent=null;
	public $id=null;
	function  __construct($id,$parent){
		$this->parent=$parent;
		$this->id=$id;
	}
	//page action
	public function like(){
		info("page(".$this->id.") has been liked");
	}
	public function unlike(){
		info("page(".$this->id.") has been unliked");
	}
	public function data(){
		info("page information[id:".$this->id."]");
	}
	//posts actions
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