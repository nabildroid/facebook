<?php 
trait message_send{
	public function send($param){
		$this->fetch();
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[]
		],$param);

		if($this->firstConversation){
			if($param["images"]&&$param["text"]){
				$this->send(mergeAssociativeArray($param,["images"=>[]],1));
				$this->fixHttpResponse($this->html,$this->messageUrl());
				$this->fetch(1);
				$this->send(mergeAssociativeArray($param,["text"=>""],1));
				return;
			}elseif($param["images"])$form=$this->childs["add"][1];
			else $form=$this->childs["add"][0];
		}else	$form=$this->childs["add"];
		if(!$param["images"]){//send text
			$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"send");
		}else{
			if(!$this->firstConversation){
				$this->submit_form($form[0],$form[1]["action"],[$param["text"]],"send_photo");
				$form=$this->dom("<form",1)[0];
			}
			$param["images"]=mergeAssociativeArray(["","",""],$param["images"]);
			$inputs=array_merge($param["images"],[$param["text"]]);
			$this->submit_form($form[0],$form[1]["action"],$inputs);
		}
	}

}


?>