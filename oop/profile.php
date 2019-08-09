<?php 
class Profile extends system{
	public $wall;
	public $pages;
	public $groups;
	public $messages;
	public $cookie;
	function  __construct(){
		$this->wall=new Wall($this);
		$this->pages=new Pages($this);
		$this->groups=new Groups($this);
		$this->messages=new Messages($this);
	}
	public function login($cookie){
		$this->cookie=$cookie;
	}
	protected function parseMenu($html){

	}
}


 ?>