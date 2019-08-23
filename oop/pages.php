<?php 
class Pages extends common{
	public $parent=null;
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	private  function fetch(){
		$this->http("/pages");
		$this->html=findDom($this->dom("<table"),"Like");
		if(!$this->html)return [];
		$html=$this->dom("<div");
		$html=filter($html,function($h){
			if(strpos($h,"<table")!==false||strpos($h,"<a")===false)return true;
			else return false;
		})[0];
		$this->html=$html;		
	}
	private function processInlinePagesHtml($pages){
		$pages=filter($pages,function($page){
			return strpos($page,"See More")==false;
		})[0];
		$pages=array_map(function($d){
			$info=[];
			$data= dom($d,["<span","<a"],1);
			$imageANDname=dom($data[0][0],"<img",1)[0];
			$info["image"]=$imageANDname["src"];
			$info["name"]=$imageANDname["alt"];
			$info["id"]=$data[1][1]["href"];
			$last_element=array_pop($data);
			if(isset($last_element[1])&&$last_element[1]["find_tag"]=="<a"){
				$info["like_link"]=$last_element[1]["href"];
				$before_last_element=array_pop($data);
				if(preg_match("/\d+/",$before_last_element[0])){
					preg_match_all('!\d+!', $before_last_element[0], $matches);
					if(isset($matches[0]))
						$info["likes_length"]=$matches[0][0];
				}
			}
			
			return $info;
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
			$pages=$this->processInlinePagesHtml($pages);
			return array_map(function ($page){
				return new Page($page,$this,1);
			},$pages);
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
			$pages=$this->processInlinePagesHtml($pages);
			return array_map(function ($page){
				return new Page($page,$this);
			},$pages);
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
			$pages=$this->processInlinePagesHtml($pages);
			return array_map(function ($page){
				return new Page($page,$this);
			},$pages);
		}else return [];
	}


}




 ?>