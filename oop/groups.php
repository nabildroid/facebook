<?php 
class Groups extends Common{
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	public function myGroups(){
		$this->http("groups/?seemore");
		$my=$this->dom("<ul")[0];
		$groups=dom($my,"<a",1);
		$groups=array_map(function($group){
			//"name"=>$group[0]
			$group=new Group($this,Group::IdFromUrl($group[1]["href"]),1);
			return $group;
		},$groups);
		return $groups;
	}
	public function suggestionGroups(){
		$this->http("groups");
		$suggetion=findDom($this->dom("<ul"),"Join");
		$groups=dom($suggetion,"<li");
		$groups=array_map(function($group){
			
			$group=dom($group,"<td");
			$info=dom($group[0],"<a",1)[0];
			$join=dom($group[1],"<a",1)[0];
			//"name"=>$info[0]
			$group=new Group($this,Group::IdFromUrl($info[1]["href"]));
			$group->actions=$join;
			return $group;

		},$groups);
		return $groups;
	}
}

 ?>