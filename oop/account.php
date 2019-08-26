<?php 
class Account extends system{
	public $wall;
	public $pages;
	public $groups;
	public $messages;
	public $notification;
	protected $cookie;
	function  __construct(){
		$this->profile=new Profile($this,"profile.php",1);
		$this->wall=new Wall($this);
		$this->pages=new Pages($this);
		$this->groups=new Groups($this);
		$this->messages=new Messages($this);
		$this->notification=new Notification($this);
	}
	public function login($cookie){
		$this->cookie=$cookie;
		preg_match_all("/c_user=.(\d).*?(?=;)/",$cookie,$id);
		if(isset($id[0][0]))$id=substr($id[0][0],7);
		$this->profile->id=$id;
	}
}


 ?>