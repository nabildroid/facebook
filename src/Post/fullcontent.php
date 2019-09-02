<?php 
namespace Facebook\Post;
use Facebook\Utils\Util;
use Facebook\Utils\Content;
trait fullcontent{
	//get Full content
	public function fullContent(){
		$this->fetch();
		if($this->content){
			$content=$this->content;
			//note: use findDom to check if "more" exist and where
			$flat=Content::flat($content);
			if(Util::instr($flat,"...More")){
				$this->fetch(1);
			} 
			return $this->content;
		}
	}
}



?>