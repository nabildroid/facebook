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
			$content=$this->splitMessages();

			$this->childs["items"]=array_merge($this->childs["items"],[$content["msgs"]]);
			$this->childs["next_page"]=$content["next_page"];
			$next=$content["next_page"];
		}

		if(isset($this->childs["items"][$page]))
			return $this->childs["items"][$page];
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
		//get sender of such message (string)
		$sender=Html::dom($msg,"<strong")[0];
		//get content 
		$content=Html::dom($msg,"<div")[0];
		$content=Content::parse($content);

		return [
			"sender"=>$sender,
			"content"=>$content
		];
	}
}


 ?>