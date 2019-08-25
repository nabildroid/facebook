<?php 
class Wall extends common{
	use wall_posts;
	use wall_privacy;
	use wall_publish;
	
	function  __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}

	


	
}




 ?>