<?php 
class Wall extends common{
	public $parent=null;
	function  __construct($parent){
		$this->parent=$parent;
	}
	public function all(){
		$this->http();
		$html=$this->dom('<div id="m_newsfeed_stream"')[0];
		//get next page
		$nextPage=dom($html,'<a',1);
		$nextPage=array_pop($nextPage)[1]["href"];
		//get posts
		$posts= array_filter(dom($html,"<div"));
		$posts=dom(array_pop($posts),"<div",1);
		//create posts objects
		$tempPosts=[];
		foreach ($posts as $post){
			$info=Post::GetInfoFromListedPost($post);
			if($info)
				$tempPosts[]=new Post($info["from"]["id"],$this,$info);
		}
		return $tempPosts;
	}
	private function changePrivacy($form,$privacy){
		if($privacy){
			$current_privacy=dom($form[0],'name="view_privacy"',1)[0];
			if(strtolower($current_privacy[1]["value"])!=$privacy){

				/*if form contain csid(as hidden input), so that easy to get the code of any privacy by only one request then force such value to publish (submit last form).
				but if the form doesn't contain it we must submit the privacy input then we can get the csid and do the half part of the first condition*/
				$csid=dom($form[0],'name="csid"');
				if(!isset($csid[0])){
					$this->submit_form($form[0],$form[1]["action"],[],"view_privacy");
					preg_match_all("/csid=.+?(?=&)/",$this->html,$csid);
					$csid=substr($csid[0][0],5);
				}else $csid=$csid[0][1]["value"];

				$this->http("/composer/mbasic/?csid=".$csid."&errcode=0&cwevent=composer_entry&filter_type=0&av=100009747405464&priv_expand=see_all&view_privacy");
				$html=$this->dom("<table")[1];
				$html=doms($html,["<td","<tr"]);

				foreach ($html as $tr) {
					$name=dom($tr,"<div")[1];
					if(isset(dom($name,"<strong")[0]))$name=dom($name,"<strong")[0];
					if(strtolower($name)==$privacy){
						$privacy=dom($tr,"<a",1);
            $privacy=array_pop($privacy)[1]["href"];
            preg_match_all("/privacyx=.+?(?=&)/",$privacy,$privacy);
            $privacy=substr($privacy[0][0],9);
            return $privacy;
					}
				}
			}
		}
		return false;
	}

	public function publish($txt="",$images=[],$privacy=""){
		$this->http();
		$form=$this->dom("<form",1)[1];	

		if(!$images){//publish image
			$privacy=$this->changePrivacy($form,$privacy);

			$this->submit_form($form[0],$form[1]["action"],[$txt],"",$privacy?[
				"privacyx"=>$privacy
			]:"");

		}else{//publish text
			//fecth upload page
			$this->submit_form($form[0],$form[1]["action"],[$txt],"view_photo");
			//upload images
			$form=dom($this->html,"<form",1)[0];
			$this->submit_form($form[0],$form[1]["action"],$images,"add_photo_done");
			$form=dom($this->html,"<form",1)[0];
			//publish post
			$privacy=$this->changePrivacy($form,$privacy);
			$this->submit_form($form[0],$form[1]["action"],[$txt],"view_post",$privacy?[
				"privacyx"=>$privacy
			]:"");
		}
	}
}




 ?>