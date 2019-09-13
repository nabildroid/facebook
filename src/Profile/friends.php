<?php
namespace Facebook\Profile;
use Facebook\Utils\Util;
use Facebook\Utils\Html;
trait friends{
	public function friends(){
		$this->fetch();

		$all=[];
		$next=$this->id."?v=friends";
		while ($next) {
			$this->http($next);
			$users=Html::doms($this->html,["<div","<div","<div"]);
            if(!count($users))break;
			//select right div that has all friends
			//fast solution for error of selecting bad div that conatin friends
			if(count($users)>1)
				$users=strlen($users[1])>strlen($users[0])?$users[1]:$users[0];
			else $users=$users[0];

			$users=Html::dom($users,"<table");
			//sometimes the first element is url for Find Friends
			if(!Util::instr($users[0],"<img"))
				array_shift($users);

			$friends=array_map(function($friend){
				$a=Html::dom($friend,"<td")[1];
				$a=Html::dom($friend,"<a",1)[0];
				$id=Profile::idFromUrl($a[1]["href"]);
				$user=new Profile($this,$id);
				$user->name=trim($a[0]);
				return $user;
			},$users);
			$all=array_merge($all,$friends);

			//get next page
			$next=Html::findDom($this->dom("<a",1),"See More");
			if(isset($next[1]["href"]))
				$next=$next[1]["href"];
			else $next="";
		}
		return $all;
	}

	public function pendingRequests(){
		$this->permission(1);
		$this->fetch();

		$this->http("friends/center/requests?seemore");
		$users=Util::filter($this->dom("<td"),function($td){return strpos($td,"<img")!==0;})[0];
		$users=array_map(function($user){
			$a=Html::dom($user,"<a",1);
            if(!$a)return null;

			preg_match_all("/uid=.(\d)*/",$a[0][1]["href"],$id);
			$id=intval(substr($id[0][0],4));
			$confirm=Html::findDom($a,"Confirm");
			$confirm[0]="Confirm Friend";
			$reject=Html::findDom($a,"Delete Request");
			$reject[0]="Delete Request";
			$user=new Profile($this,$id);
			$user->name=trim($a[0][0]);
			//note:here should be merged array
			$user->actions=[$confirm,$reject];
			return $user;
		},$users);
        $users=Util::filter($users);
		return $users;
	}

}

 ?>