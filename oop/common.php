<?php 
class Common{
	public $root=null;
	public $html="";
	public function http($url="",$data="",$headers=[]){
		if($this->root!=null)
			$root=$this->root;
		else{
			$root=$this->parent;
			while(!is_a($root,"Profile"))
				$root=$root->parent;
			$this->root=$root;
		}
		$this->html=$root->http($url,$data,$headers);
	}
	public function dom($search,$getAttribute=0,$closedTag=0){
		return dom($this->html,$search,$getAttribute,$closedTag);
	}
	public function submit_form($html,$url,$values){
		$inputs=dom($html,"<input",1);
		$data=[];
		foreach ($inputs as $input) {
			if($input["type"]=="text")
				$data[$input["name"]]=array_shift($values);
			else if ($input["type"]=="hidden")
				$data[$input["name"]]=$input["value"];
		}
		$this->http($url,$data);
	}

}

 ?>