<?php 
trait group_actions{
	//Group action
	public function join($questions=[]){
		$this->fetch();
		if($this->member===0){
			if($this->info["join"]){
				$form=$this->info["join"];
				$this->submit_form($form[0],$form[1]["action"]);
				$form_questions=findDom($this->dom("<form",1),"<textarea");
				$this->submit_form($form_questions[0],$form_questions[1]["action"],$questions);
				$this->member=2;
			}
		}
	}
	public function leave(){
		$this->fetch();
		if($this->member===1){
			$this->http("/group/leave/?group_id=".$this->id());
			$this->member=0;
			return true;
		}elseif($this->member===2&&$this->info["join"]){
			$form=$this->info["join"];
			$this->submit_form($form[0],$form[1]["action"]);
			$this->member=0;
		}else throw new Exception("user didn't have the permission to leave such group");
	}
}
?>
