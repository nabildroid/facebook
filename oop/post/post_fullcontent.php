<?php 
trait post_fullcontent{
	//get Full content
	public function fullContent(){
		if($this->info["content"]){
			$content=$this->info["content"];
			$flat=flatContent($content);
			if(strpos($flat,"...More")!==false){
				$this->fetch();
			} 
			return $this->info["content"];
		}
	}
}



?>