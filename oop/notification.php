<?php 
class Notification extends common{
	public $parent=null;
	public $triggers=[
		"message"=>null,
		"notification"=>null
	];
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	
	public function parseMenu($html){
		$a=dom($html,"<a",1);
		$message=findDom($a,"Message");
		$notification=findDom($a,"Notification");

		//message
		if(strpos($message[0],"(")!==false){
			if(is_callable($this->triggers["message"]))
				$this->triggers["message"]($message);
		}
		if(1||strpos($notification[0],"<")!==false){
			if(is_callable($this->triggers["notification"]))
				$this->triggers["notification"]($this->parseNotification());
		}
	}

	public function setMessageTrigger($fnc){
		if(is_callable($fnc))
			$this->triggers["message"]=$fnc;
		else throw new Exception("the message trigger must be a function", 1);
	}

	public function setNotificationTrigger($fnc){
		if(is_callable($fnc))
			$this->triggers["notification"]=$fnc;
		else throw new Exception("the notification trigger must be a function", 1);
	}
	public function parseNotification(){

		$this->http("notifications.php");
		$noti=array();
		$html=$this->dom("<tr");
		foreach ($html as $a) {
			$a=dom($a,"<a",1)[0];
			$url=urldecode($a[1]["href"]);
			$url=substr($url,strpos($url,"redir=")+6);
			$type=null;
			//check add post or photo
			if(instr($a[0],"added a photo")||
				 instr($a[0],"posted"))
			$type=1;
			//post reaction
			if(instr($a[0],"reacted to your post")||
				 instr($a[0],"like your post")||
				 instr($a[0],"reacted to your photo")||
				 instr($a[0],"like your photo")||
				 instr($a[0],"reacted to a post you shared")||
				 instr($a[0],"like to a post you shared")||
			   instr($a[0],"reacted a photo you shared")||
				 instr($a[0],"like a photo you shared"))
			$type=2;
			//add comment
			if(instr($a[0],"commented on your")||
				 instr($a[0],"replied to your comment"))
			$type=3;
			//comment reaction
			if(instr($a[0],"reacted to your comment")||
				 instr($a[0],"like your comment"))
			$type=4;
			//approved post
			if(instr($a[0],"approved your post")||
				 instr($a[0],"approved your photo"))
			$type=4;
			//aproved join request
			if(instr($a[0],"has been approved"))
			$type=6;
			if($type)
				array_push($noti,["type"=>$type,"url"=>$url,"snippet"=>$a[0]]);
		}
		return $noti;
	}
}



?>