<?php 
class Pages extends common{
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	private  function fetch(){
		if($this->fetched)return;
		$this->http("pages");
		$this->html=findDom($this->dom("<table"),"Like");
		if(!$this->html)return [];
		$html=$this->dom("<div");
		$html=filter($html,function($h){
			if(strpos($h,"<table")!==false||strpos($h,"<a")===false)return true;
			else return false;
		})[0];
		$this->html=$html;
		$this->fetched=1;	
	}
	private function processInlinePagesHtml($pages){
		$pages=filter($pages,function($page){
			return strpos($page,"See More")==false;
		})[0];
		$pages=array_map(function($d){
			$info=[];
			$data=dom($d,"<div",1)[0];
			//get id from id attribute 
			if(isset($data[1]["id"])){
				preg_match_all("/\d+/",$data[1]["id"],$id);
			}
			$id=isset($id[0][0])&&$id[0][0]?$id[0][0]:"";
			//get name of page
			$name=dom($data[0],"<span")[0][0];
			//get like link
			$like_link=findDom(dom($data[0],"<a",1),"Like");
			if(isset($like_link[1]["href"]))
				$like_link=$like_link[1]["href"];
			else $like_link="";

			return [
				"id"=>$id,
				"name"=>$name,
				"like_link"=>$like_link
			];
		},$pages);
		return $pages;
	}
	public function myPages(){
		$this->fetch();
		if($this->html){
			for ($i=0;$i<count($this->html)-1;$i++)
				if(strpos($this->html[$i],"Pages You Work On")!==false){
					$this->html=$this->html[$i+1];break;
				}
			if(is_array($this->html))return [];
			$pages=doms($this->html,["<div","<div"]);
			return $this->createPages($pages,1);
		}else return [];
	}
	public function  invitedPages(){
		$this->fetch();
		if($this->html){
			for ($i=0;$i<count($this->html)-1;$i++)
				if(strpos($this->html[$i],"Page Invites")!==false){
					$this->html=$this->html[$i+1];break;
				}
			if(is_array($this->html))return [];
			$pages=doms($this->html,["<div","<div"]);
			return $this->createPages($pages);
		}else return [];
	}
	public function suggestionPages(){
		$this->fetch();
		if($this->html){
			for ($i=0;$i<count($this->html)-1;$i++)
				if(strpos($this->html[$i],"Suggested Pages")!==false){
					$this->html=$this->html[$i+1];break;
				}
			if(is_array($this->html))return [];
			$pages=doms($this->html,["<div","<div"]);
			return $this->createPages($pages);
		}else return [];
	}

	private function createPages($pages,$admin=0){
		$pages=$this->processInlinePagesHtml($pages);
		return array_map(function ($page_info)use($admin){
			$page=new Page($this,$page_info["id"],$admin);
			$page->likes["link"]=$page_info["like_link"];

			return $page;
		},$pages);
	}

}




 ?>