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
		if(isset($message[0])&&strpos($message[0],"(")!==false){
			if(is_callable($this->triggers["message"]))
				$this->triggers["message"]($message);
		}
		if(isset($notification[0])&&strpos($notification[0],"<")!==false){
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
	private function parseNotification(){

		$this->http("notifications.php");
		$noti=array();
		$html=doms($this->html,['id="notifications_list"',"<div","<div"]);
		$list=[];
		foreach ($html as $div)
			$list=array_merge($list,dom($div,"<div",1));
		$list=filter($list,function($l){
			return instr($l[1]["class"],"bw")&&instr($l[1]["class"],"bx");
		})[0];
		foreach ($list as $a) {
			$a=dom($a[0],"<tr")[0];
			$a=dom($a,"<a",1)[0];
			$url=urldecode($a[1]["href"]);
			$url=substr($url,strpos($url,"redir=")+6);

			$type=$this->detectNotificationType($a[0]);
			if($type)
				array_push($noti,["type"=>$type,"url"=>$url,"snippet"=>$a[0]]);
		}
		return $noti;
	}

	private function detectNotificationType($title){
		//check add post or photo
		if(instr($title,"added a photo")||
			 instr($title,"posted"))
		return 1;
		//post reaction
		if(instr($title,"reacted to your post")||
			 instr($title,"like")&&instr($title,"your post")||
			 instr($title,"reacted to your photo")||
			 instr($title,"like")&&instr($title,"your photo")||
			 instr($title,"reacted to a post you shared")||
			 instr($title,"like to a post you shared")||
		   instr($title,"reacted a photo you shared")||
			 instr($title,"like a photo you shared"))
		return 2;
		//add comment
		if(instr($title,"commented on your")||
			 instr($title,"replied to your comment"))
		return 3;
		//comment reaction
		if(instr($title,"reacted to your comment")||
			 instr($title,"like")&&instr($title,"your comment"))
		return 4;
		//approved post
		if(instr($title,"approved your post")||
			 instr($title,"approved your photo"))
		return 4;
		//aproved join request
		if(instr($title,"has been approved"))
		return 6;
	}
}



?>