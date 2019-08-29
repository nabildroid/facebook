<?php 
trait group_actions{
	//Group action
	public function join($questions=[]){
		$this->fetch();
		if($this->admin==0){
			if($this->actions){
				$form=$this->actions;
				$this->submit_form($form[0],$form[1]["action"]);
				$form_questions=findDom($this->dom("<form",1),"<textarea");
				if($form_questions){
					$this->submit_form($form_questions[0],$form_questions[1]["action"],$questions,"","",1);
				}
				$this->admin=2;
			}
		}
	}
	public function leave(){
		$this->fetch();
		if($this->admin==1){
			$this->http("/group/leave/?group_id=".$this->id);
			//it may return form to confirm request
			$form=$this->dom("<form",1);
			if(isset($form[0]))
				$this->submit_form($form[0][0],$form[0][1]["action"],[],"confirm");	
			$this->admin=0;
			return true;
		}elseif($this->admin==2&&$this->actions){
			$form=$this->actions;
			$this->submit_form($form[0],$form[1]["action"]);
			$this->admin=0;
		}else throw new Exception("user didn't have the permission to leave such group");
	}
}
?>
