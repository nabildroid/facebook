<?php 
namespace Facebook\Wall;

class Wall extends \Facebook\Common{
	use posts;
	use privacy;
	use publish;
	
	function  __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}

	
}




 ?>