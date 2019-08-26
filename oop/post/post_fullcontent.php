<?php 
trait post_fullcontent{
	//get Full content
	public function fullContent(){
		$this->fetch();
		if($this->content){
			$content=$this->content;
			$flat=flatContent($content);
			if(instr($flat,"...More")){
				$this->fetch(1);
			} 
			return $this->content;
		}
	}
}



?>