<?php 
trait message_send{
	public function send($param){
		$this->fetch();
		$param=mergeAssociativeArray([
			"text"=>"",
			"images"=>[]
		],$param);


		if($this->info["firstConversation"]){
			if($param["images"]&&$param["text"]){
				$this->send(mergeAssociativeArray($param,["images"=>[]]));
				$this->fetch(1);
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


?>