<?php 
trait message_chat{
	public function chat($page=0){
		$this->fetch();
		if($this->info["firstConversation"])return [];

		if(is_numeric($page)){
			if(isset($this->info["msgs"][$page]))
				return $this->info["msgs"][$page];
			else{
				for ($i=count($this->info["msgs"]);$i <=$page; $i++) {
					if(!$this->info["msg_next_page"])break;

					$this->http($this->info["msg_next_page"]);
					$html=doms($this->html,['id="messageGroup"',"<div"]);

					$msgs=dom($html[1],"<div");
					$msgs=array_map(function($msg){
						$msg=dom($msg,"<div")[0];//take content without second information(time...)
						$msg=strcut($msg,0,strpos($msg,"</a>")+3);  //split the content from the sender

						$sender=$msg[0];
						$content=parseContent($msg[1]);

						if(strpos($sender,$this->info["friend"])!==false)
							$sender=1;
						else $sender=0;

						return [
							"content"=>$content,
							"from"=>$sender?new Profile($this,["id"=>$sender]):$this->root->profile
						];
					},$msgs);

					$this->info["msgs"]=array_merge($this->info["msgs"],[$msgs]);

					$next=findDom(dom($html[0],"<a",1),"See Older Messages");
					if(isset($next[1]["href"]))
						$this->info["msg_next_page"]=$next[1]["href"];

				}
				if(isset($this->info["msgs"][count($this->info["msgs"])-1]))
					return $this->info["msgs"][count($this->info["msgs"])-1];
				else return [];
			}
		}else {
			return $this->info["msgs"];
		}
	}
}


 ?>