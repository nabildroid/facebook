<?php 
class Pages extends common{
	public $parent=null;
	function __construct($parent){
		$this->parent=$parent;
	}
	private  function fetch(){
		$this->http("/pages");
		$this->html=findDom($this->dom("<table"),"like");
		$this->html=$this->dom("<div");
		$sections=filter($this->html,function($div){
			return strpos($div,"<h3")!==0;
		});
		$this->html=isset($sections[0])?$sections[0]:[];
		if(!$this->html)
			throw new Exception("there's no page could process", 1);
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
	/**
	* TODO  add dynamic way to split between section like findDom, usually my page hasn't like button ,suggestion pages has like button and number of likes ,invited pages hasn't number of likes and instend of that has name of who invite me
	*/
	public function myPages(){
		$this->fetch();
		$pages=doms($this->html[0],["<div","<div"]);
		$pages=$this->processInlinePagesHtml($pages);
		return array_map(function ($page){
			return new Page($page,$this,1);
		},$pages);
	}
	public function  invitedPages(){
		$this->fetch();
		$pages=doms($this->html[1],["<div","<div"]);
		$pages=$this->processInlinePagesHtml($pages);
		return array_map(function ($page){
			return new Page($page,$this);
		},$pages);
		
	}
	public function suggestionPages(){
		$this->fetch();
		$pages=doms($this->html[2],["<div","<div"]);
		$pages=$this->processInlinePagesHtml($pages);
		return array_map(function ($page){
			return new Page($page,$this);
		},$pages);
	}


}




 ?>