<?php 
class Account extends system{
	public $wall;
	public $pages;
	public $groups;
	public $messages;
	protected $cookie;
	function  __construct(){
		$this->profile=new Profile($this);
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