<?php 
class Message extends common{
	public $parent=null;
	public $info=[
		"friend"=>null, 
		"msg_next_page"=>null,
		"msgs"=>[],
		"form"=>"",
		"firstConversation"=>0
	];

	function  __construct($info,$parent){
		$this->parent=$parent;
		parent::__construct();

		$this->info=mergeAssociativeArray($this->info,$info);
		$this->info["msg_next_page"]=$this->messageUrl();
	}
	public function messageUrl(){
		return "/messages/read/?fbid=".$this->info["friend"];
	}
	public function fetch_info($force=0) {
		if(!$force&&$this->info["form"])return;//for prevent multi fetch 
		$this->http($this->messageUrl());
		if($this->checkIfFirstConversation()){
			$this->info["firstConversation"]=1;
			$this->info["form"]=$this->dom("<form",1);
		}else{
			$this->info["firstConversation"]=0;
			$form=findDom($this->dom("<form",1),"<textarea");	
			$this->info["form"]=$form;
		}
	}
	public function checkIfFirstConversation(){
		$criteria=findDom($this->dom("<a"),"Add Recipients");
		return $criteria==true;
	}
	public function chat($page=0){
		$this->fetch_info();
		if($this->info["firstConversation"])return [];

		if(is_numeric($page)){
			if(isset($this->info["msgs"][$page]))
				return $this->info["msgs"][$page];
			else{
				for ($i=count($this->info["msgs"]);$i <=$page; $i++) {
					if(!$this->info["msg_next_page"])break;

					$this->http($this->info["msg_next_page"]);
					$html=doms($this->html,['id="messageGroup"',"<div"]);

					$msgs=dom($html[1],"<div");
					$msgs=array_map(function($msg){
						$msg=dom($msg,"<div")[0];//take content without second information(time...)
						$msg=strcut($msg,0,strpos($msg,"</a>")+3);  //split the content from the sender

						$sender=$msg[0];
						$content=parseContent($msg[1]);

						if(strpos($sender,$this->info["friend"])!==false)
							$sender=1;
						else $sender=0;

						return [
							"content"=>$content,
							"from"=>$sender
						];
					},$msgs);

					$this->info["msgs"]=array_merge($this->info["msgs"],[$msgs]);

					$next=findDom(dom($html[0],"<a",1),"See Older Messages");
					if(isset($next[1]["href"]))
						$this->info["msg_next_page"]=$next[1]["href"];

				}
				if(isset($this->info["msgs"][count($this->info["msgs"])-1]))
					return $this->info["msgs"][count($this->info["msgs"])-1];
				else return [];
			}
		}else {
			return $this->info["msgs"];
		}
	}
	public function send($param){
		$this->fetch_info();
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[]
		],$param);


		if($this->info["firstConversation"]){
			if($param["images"]&&$param["text"]){
				$this->send(mergeAssociativeArray($param,["images"=>[]]));
				$this->fetch_info(1);
				$this->send(mergeAssociativeArray($param,["text"=>""]));
			}elseif($param["images"])$form=$this->info["form"][1];
			else $form=$this->info["form"][0];

		}else		$form=$this->info["form"];

		if(!$param["images"]){//send text
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"send");
		}else{
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"send_photo");
			$form=$this->dom("<form",1)[0];
			$param["images"]=mergeAssociativeArray(["","",""],$param["images"]);
			$inputs=array_merge($param["images"],[$param["text"]]);
			$this->submit_form($form[0],$form[1]["action"],$inputs);
		}
	}
}