<?php 
namespace Facebook\Message;
use Facebook\Utils\Html;

class Message extends \Facebook\Common{
	use chat;
	use send;

	public $friend; //friend object
	/**
	 * is this first time of message between acount and friend
	 * because facebook has two pages one for first messages and 
	 * second for an old time messages
	 */
	public $firstConversation=0; 
	public $childs=[//contain messages 
		"items"=>[], //messages
		"next_page"=>"", //next page of messages (see old messages)
		"add"=>"" //form for send new message
	];

	
	/**
	 * @param $friend must be type of Profile or Page
	 */
	function  __construct($parent,$friend){
		$this->parent=$parent;
		parent::__construct();

		$this->friend=$friend;
	}
	private function fetch($force=0) {
		if(!$force&&$this->fetched)return;//for prevent multi fetch 
		$this->http($this->messageUrl());
		if($this->checkIfFirstConversation()){
			$this->firstConversation=1;
			$this->childs["add"]=$this->dom("<form",1);
		}else{
			$this->firstConversation=0;
			$form=Html::findDom($this->dom("<form",1),"<textarea");	
			$this->childs["add"]=$form;
		}
		$this->fetched=1;
	}

	private function checkIfFirstConversation(){
		$criteria=Html::findDom($this->dom("<a"),"Add Recipients");
		return $criteria==true;
	}

	private function messageUrl(){
		return "/messages/read/?fbid=".$this->friend->getId();
	}

}