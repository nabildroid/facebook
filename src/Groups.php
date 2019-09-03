<?php 
namespace Facebook;
use Facebook\Utils\Html;

use Facebook\Group\Group;
class Groups extends Common{
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	public function myGroups(){
		$this->http("groups/?seemore");
		$my=$this->dom("<ul")[0];
		$groups=Html::dom($my,"<a",1);
		$groups=array_map(function($group){
			$name=trim($group[0]);
			$group=new Group($this,Group::IdFromUrl($group[1]["href"]),1);
			$group->name=$name;
			return $group;
		},$groups);
		return $groups;
	}
	public function suggestionGroups(){
		$this->http("groups");
		$suggetion=Html::findDom($this->dom("<ul"),"Join");
		$groups=Html::dom($suggetion,"<li");
		$groups=array_map(function($group){
			
			$group=Html::dom($group,"<td");
			$info=Html::dom($group[0],"<a",1)[0];
			$join=Html::dom($group[1],"<a",1)[0];
			$name=trim($info[0]);
			$group=new Group($this,Group::IdFromUrl($info[1]["href"]));
			$group->name=$name;
			$group->actions=$join;

			return $group;

		},$groups);
		return $groups;
	}
}

 ?>