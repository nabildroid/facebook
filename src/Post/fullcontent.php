<?php 
namespace Facebook\Post;
use Facebook\Utils\Util;
trait fullcontent{
	//get Full content
	public function fullContent(){
		$this->fetch();
		if($this->content){
			//note: bad criteria
			if(Util::findInTree($this->content,["content"=>"More"])){
				$this->fetch(1);
			} 
			return $this->content;
		}
	}
}



?>