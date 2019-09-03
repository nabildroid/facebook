<?php 
namespace Facebook\Message;
use Facebook\Utils\Html;
use Facebook\Utils\Util;
use Facebook\Utils\Content;

trait chat{
	public function chat($page=0){
		$this->fetch();
		if($this->firstConversation)return [];
		//prepare the url
		if(!$this->childs["items"])
			$next=$this->messageUrl();
		else $next=$this->childs["next_page"];

		for ($i=count($this->childs["items"]);$i <=$page; $i++) {
			if(!$next)break;
			$this->http($next);
			//get friend name note: it's not test in all scenarios
			if(!$this->friend->getName()){
				$friendName=$this->dom("<span")[0];
				if(isset($friendName[0])&&trim($friendName[0]))
					$this->friend->name=trim($friendName[0]);
			}

			$content=$this->splitMessages();

			$this->childs["items"]=array_merge($this->childs["items"],[$content["msgs"]]);
			$this->childs["next_page"]=$content["next_page"];
			$next=$content["next_page"];
		}

		if(isset($this->childs["items"][$page]))
			return  array_reverse($this->childs["items"][$page]);
		else return [];
	}


	private function splitMessages(){
		$sections=$this->dom('id="messageGroup"')[0];
		$sections=Html::dom($sections,"<div",1);
		//get next_page url
		$sections=Util::filter($sections,function($sec){
			return isset($sec[1]["id"])&&Util::instr($sec[1]["id"],"see_");
		});
		$next=isset($sections[0][0])?$sections[0][0][0]:"";
		$messages=$sections[1];

		$msgs=[];
		if(isset($messages[0][0])){
			$msgs_html=Html::dom($messages[0][0],"<div");
			foreach ($msgs_html as $msg_html) {
				//if message hasn't time ignore it(is not message)
				if(!Util::instr($msg_html,"<abbr"))continue;

				$msg=$this->parseSingleMessage($msg_html);
				array_push($msgs,$msg);
			}
		}
		$next=Html::dom($next,"<a",1);
		$next=isset($next[0][1]["href"])?$next[0][1]["href"]:"";
		return ["msgs"=>$msgs,"next_page"=>$next];
	}

	private function parseSingleMessage($html){
		$msg=Html::dom($html,"<div");//first is content(sender&&content) and secoud is the time
		//note: create parseTime global function for create timestem from (time ago)
		$time=Html::dom($msg[1],"<abbr")[0];

		$msg=$msg[0];
		$sections=Util::strcut($msg,0,strpos($msg,"</a>")+3);
		//get sender of such message (string)
		$sender=Html::dom($sections[0],"<strong")[0];
		//get content 
		$content=Content::parse($sections[1]);
		$friendName=$this->friend->getName(1);

		return [
			"sender"=>$sender==$friendName?1:0,
			"content"=>$content
		];
	}
}


 ?>