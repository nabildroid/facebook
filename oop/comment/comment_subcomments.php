<?php 
trait comment_subcomments{
	public function subcomments($page=0){
		$this->fetch();

		if(isset($this->childs["items"][$page]))
			return $this->childs["items"][$page];
		else{
			//prepare the url
			$next=$this->id;
			if($this->childs["next_page"])
				$next=$this->childs["next_page"];
			if(!$next){
				$this->fetch(1);
				return $this->subcomments($page);
			}

			for ($i=count($this->childs["items"]); $i <=$page; $i++) { 
				if(!$next)break;

				$this->http($next);
				$data=$this->splitComments()["comments"];
				$data=self::parseComments($data,$this);
				
				$this->childs["next_page"]=$next=$data["next"];
				$this->childs["items"]=array_merge($this->childs["items"],[$data["items"]]);
				$this->childs["add"]=$data["form"];

			}
			if(isset($this->childs["items"][count($this->childs["items"])-1]))
				return $this->childs["items"][count($this->childs["items"])-1];
			else return [];
		}
	}
}


 ?>