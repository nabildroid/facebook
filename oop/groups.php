<?php 
class Groups extends Common{
	public $parent=null;
	function __construct($parent){
		$this->parent=$parent;
	}
	public function myGroups(){
		$this->http("/groups/?seemore");
		$my=$this->dom("<ul")[0];
		$groups=dom($my,"<a",1);
		$groups=array_map(function($group){
			return [
				"name"=>$group[0],
				"id"=>$group[1]["href"],
			];
		},$groups);
		var_dump($groups);
	}
	public function suggestionGroups(){
		$this->http("/groups");
		$suggetion=findDom($this->dom("<ul"),"Join");
		$groups=dom($suggetion,"<li");
		$groups=array_map(function($group){
			$group=dom($group,"<td");
			$info=dom($group[0],"<a",1)[0];
			$join=dom($group[1],"<a",1)[0];
			return [
				"name"=>$info[0],
				"id"=>$info[1]["href"],
				"join"=>$join[1]["href"]
			];
		},$groups);
		var_dump($groups);
	}
}

 ?>