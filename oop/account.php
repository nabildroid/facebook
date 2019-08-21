<?php 
class Account extends system{
	public $wall;
	public $pages;
	public $groups;
	public $messages;
	protected $cookie;
	function  __construct(){
		$this->profile=new Profile($this,"",1);
		$this->wall=new Wall($this);
		$this->pages=new Pages($this);
		$this->groups=new Groups($this);
		$this->messages=new Messages($this);
	}
	public function login($cookie){
		$this->cookie=$cookie;
		preg_match_all("/c_user=.(\d).*?(?=;)/",$cookie,$id);
		if(isset($id[0][0]))$id=substr($id[0][0],7);
		$this->profile->info["id"]=$id;
	}
	
	protected function parseMenu($html){

	}
	


	
}


 ?>