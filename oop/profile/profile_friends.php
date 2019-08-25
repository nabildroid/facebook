<?php 
trait profile_friends{
	public function friends(){
		$this->fetch();

		$all=[];
		$next=$this->id()."?v=friends";
		while ($next) {
			$this->http($next);
			$html=doms($this->html,["<div","<div","<div"])[1];
			$users=dom($html,"<div");
			if($this->admin){
				$users=array_pop($users);
				$users=dom($users,"<div");
			}

			$friends=array_map(function($friend){

				$a=dom($friend,"<a",1)[0];
				preg_match_all("/[a-z0-9.=]+/", $a[1]["href"],$id);
				if($id[0][0]=="profile.php")
					$id=intval(substr($id[0][1],3));
				else $id=$id[0][0];
				//@note: the id could be a string !!!!!
				return new Profile($this,["id"=>$id]);
			},$users);
			$all=array_merge($all,$friends);

			//get next page 
			$next=findDom($this->dom("<a",1),"See More");
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
		$users=filter($this->dom("<td"),function($td){return strpos($td,"<img")!==0;})[0];
		$users=array_map(function($user){
			$a=dom($user,"<a",1);
			preg_match_all("/uid=.(\d)*/",$a[0][1]["href"],$id);
			$id=intval(substr($id[0][0],4));
			$confirm=findDom($a,"Confirm");
			$confirm[0]="Confirm Friend";
			$reject=findDom($a,"Delete Request");
			$reject[0]="Delete Request";
			return new Profile($this,[
				"id"=>$id,
				"actions"=>[$confirm,$reject]
			]);
		},$users);	
		return $users;
	}	

}

 ?>