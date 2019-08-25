<?php 
class Message extends common{
	public $info=[
		"friend"=>null, 
		"msg_next_page"=>null,
		"msgs"=>[],
		"form"=>"",
		"firstConversation"=>0
	];

	use message_chat;
	use message_send;

	function  __construct($info,$parent){
		$this->parent=$parent;
		parent::__construct();

		$this->info=mergeAssociativeArray($this->info,$info);
		$this->info["msg_next_page"]=$this->messageUrl();
	}
	private function fetch($force=0) {
		if(!$force&&$this->fetched)return;//for prevent multi fetch 
		$this->http($this->messageUrl());
		if($this->checkIfFirstConversation()){
			$this->info["firstConversation"]=1;
			$this->info["form"]=$this->dom("<form",1);
		}else{
			$this->info["firstConversation"]=0;
			$form=findDom($this->dom("<form",1),"<textarea");	
			$this->info["form"]=$form;
		}
		$this->fetched=1;
	}

	private function checkIfFirstConversation(){
		$criteria=findDom($this->dom("<a"),"Add Recipients");
		return $criteria==true;
	}

	private function messageUrl(){
		return "/messages/read/?fbid=".$this->info["friend"];
	}

}