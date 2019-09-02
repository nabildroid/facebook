<?php 
namespace Facebook\Wall;
use Facebook\Utils\Html;

use Facebook\Post\Post;

trait privacy{
	private function currentPrivacy($formHtml){
		$current_privacy=Html::dom($formHtml,'name="view_privacy"',1)[0];
		if(isset($current_privacy[1]["value"]))
			return strtolower(trim($current_privacy[1]["value"]));
		else return false;
	}
	private function changePrivacy($form,$privacy){
		if($privacy){
			if($this->currentPrivacy($form[0])!=$privacy){

				/*if form contain csid(as hidden input), so that easy to get the code of any privacy by only one request then force such value to publish (submit last form).
				but if the form doesn't contain it we must submit the privacy input then we can get the csid and do the half part of the first condition*/
				$csid=Html::dom($form[0],'name="csid"');
				if(!isset($csid[0])){
					$this->submit_form($form[0],$form[1]["action"],[],"view_privacy");
					preg_match_all("/csid=.+?(?=&)/",$this->html,$csid);
					$csid=substr($csid[0][0],5);
				}else $csid=$csid[0][1]["value"];

				$this->http("/composer/mbasic/?csid=".$csid."&errcode=0&cwevent=composer_entry&filter_type=0&priv_expand=see_all&view_privacy");
				$html=$this->dom("<table")[0];
				$html=Html::doms($html,["<td","<tr"]);

				foreach ($html as $tr) {
					$name=Html::dom($tr,"<div")[1];
					if(isset(Html::dom($name,"<strong")[0]))$name=Html::dom($name,"<strong")[0];
					if(strtolower($name)==$privacy){
						$privacy=Html::dom($tr,"<a",1);
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
}


 ?>