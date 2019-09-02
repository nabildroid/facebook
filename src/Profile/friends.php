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
			//select right div that has all friends
			echo "__________NOT__WORKING___________";
			var_dump($users);
			exit;
			if(count($users)>2)
				$users=$users[1];
			elseif($this->admin)$users=$users[1];
			else  $users=$users[0];

			$users=Html::dom($users,"<table");
			//sometimes the first element is url for Find Friends
			if(!Util::instr($users[0],"<img"))
				array_shift($users);

			$friends=array_map(function($friend){
				$a=Html::dom($friend,"<td")[1];
				$a=Html::dom($friend,"<a",1)[0];
				preg_match_all("/[a-z0-9.=]+/", $a[1]["href"],$id);
				if($id[0][0]=="profile.php")
					$id=intval(substr($id[0][1],3));
				else $id=$id[0][0];
				//@note: the id could be a string !!!!!
				return new Profile($this,$id);
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
			preg_match_all("/uid=.(\d)*/",$a[0][1]["href"],$id);
			$id=intval(substr($id[0][0],4));
			$confirm=Html::findDom($a,"Confirm");
			$confirm[0]="Confirm Friend";
			$reject=Html::findDom($a,"Delete Request");
			$reject[0]="Delete Request";
			$user=new Profile($this,$id);
			//note:here should be merged array
			$user->actions=[$confirm,$reject];
			return $user;
		},$users);	
		return $users;
	}	

}

 ?>