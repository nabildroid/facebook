<?php 
namespace Facebook\Group;
use Facebook\Utils\Html;
use Facebook\Utils\Util;

trait actions{
	//Group action
	public function getJoinQuestions(){
		$this->fetch();
		if($this->admin==0){
			$this->join([],1);
			$questions=[];
			if(Util::instr($this->html,"/groups/membership_criteria_answer/")){
				$form_questions=Html::findDom($this->dom("<form"),"<textarea");
				$questions=Html::dom($form_questions,"<span");
			}
			$this->leave(count($questions));
			return $questions;
		}
	}
	public function join($questions=[],$dontSubmitQuestions=0){
		$this->fetch();
		if($this->admin==0){
			if($this->actions){
				$form=$this->actions;
				$this->submit_form($form[0],$form[1]["action"]);
				$form_questions=Html::findDom($this->dom("<form",1),"<textarea");
	
				if($form_questions&&!$dontSubmitQuestions){
					$this->submit_form($form_questions[0],$form_questions[1]["action"],$questions);
				}
				//whenever submit and the content doesn't have textarea is like new fetch happend so there we could get fresh info about group
				if(!Util::instr($this->html,"<textarea")){
					$this->fixHttpResponse($this->html,$this->id);
					$this->fetch(1);
				}	
				
				$this->admin=2;
				sleep(1);
			}
		}
	}
	public function leave($force=0){
		$this->fetch($force);
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
		}else throw new \Exception("user didn't have the permission to leave such group");
	}
}
?>
