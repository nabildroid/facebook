<?php 
namespace Facebook;
use Facebook\Utils\Html;
use Facebook\Utils\Util;
use Facebook\Utils\Content;

class Notification extends common{
	public $triggers=[
		"message"=>null,
		"notification"=>null
	];
	function __construct($parent){
		$this->parent=$parent;
		parent::__construct();
	}
	
	public function parseMenu($html){
		$a=Html::dom($html,"<a",1);
		$message=Html::findDom($a,"Message");
		$notification=Html::findDom($a,"Notification");
		//message
		if(isset($message[0])&&strpos($message[0],"(")!==false&&isset($message[1]["href"])){
			//get sender id from href from message url
			$msg_url=$message[1]["href"];
			preg_match_all("/(\d+){4}/",$msg_url,$id);
			if(count($id[0])){
				if($id[0][0]==$this->root->profile->getId())
					$id=$id[0][1];
				else $id=$id[0][0];
			}else $id=0;
			
			if($id&&is_callable($this->triggers["message"]))
				$this->triggers["message"]($id);
		}
		if(isset($notification[0])&&strpos($notification[0],"<")!==false){
			if(is_callable($this->triggers["notification"]))
				$this->triggers["notification"]($this->parseNotification());
		}
	}

	public function setMessageTrigger($fnc){
		if(is_callable($fnc))
			$this->triggers["message"]=$fnc;
		else throw new \Exception("the message trigger must be a function", 1);
	}

	public function setNotificationTrigger($fnc){
		if(is_callable($fnc))
			$this->triggers["notification"]=$fnc;
		else throw new \Exception("the notification trigger must be a function", 1);
	}
	private function parseNotification(){

		$this->http("notifications.php");
		$noti=array();
		$html=Html::doms($this->html,['id="notifications_list"',"<div","<div"]);
		$list=[];
		foreach ($html as $div)
			$list=array_merge($list,Html::dom($div,"<div",1));
		$list=Util::filter($list,function($l){
			return Util::instr($l[1]["class"],"bw")&&Util::instr($l[1]["class"],"bx");
		})[0];
		foreach ($list as $a) {
			$a=Html::dom($a[0],"<tr")[0];
			$a=Html::dom($a,"<a",1)[0];
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
		if(Util::instr($title,"added a photo")||
			 Util::instr($title,"posted"))
		return 1;
		//post reaction
		if(Util::instr($title,"reacted to your post")||
			 Util::instr($title,"like")&&Util::instr($title,"your post")||
			 Util::instr($title,"reacted to your photo")||
			 Util::instr($title,"like")&&Util::instr($title,"your photo")||
			 Util::instr($title,"reacted to a post you shared")||
			 Util::instr($title,"like to a post you shared")||
		   Util::instr($title,"reacted a photo you shared")||
			 Util::instr($title,"like a photo you shared"))
		return 2;
		//add comment
		if(Util::instr($title,"commented on your")||
			 Util::instr($title,"replied to your comment"))
		return 3;
		//comment reaction
		if(Util::instr($title,"reacted to your comment")||
			 Util::instr($title,"like")&&Util::instr($title,"your comment"))
		return 4;
		//approved post
		if(Util::instr($title,"approved your post")||
			 Util::instr($title,"approved your photo"))
		return 5;
		//aproved join request
		if(Util::instr($title,"has been approved"))
		return 6;
	}
}



?>