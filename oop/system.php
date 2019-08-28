<?php
class  System{
	
	/**
		* @todo BAD BAD BAD ... it's provide a way to access full account :( :( 
		* it public now because of some satic function need to call http in post of exemple
		*
	*/
	public function http($url,$data="",$headers=[],$responseHeader=0){
		//which facebook version we gonna use
		$mode=$this->FREE_FACEBOOK?"free":"mbasic";
		if(strpos($url,"http")!==0)
			$url="https://".$mode.".facebook.com/".$url;

		$http=ping($url,$data,array_merge([
			"Cookie:".$this->cookie
		],$headers),$responseHeader);
		if(is_string($http)){
			
			$html=dom($http,"<div");
			while(count($html)===1){
				$html=dom($html[0],"<div");
				//delete bottom menu
				if(count($html)>1&&instr($html[count($html)-1],'href="/logout.php?'))
					array_pop($html);
			}
		
			if($this->FREE_FACEBOOK)
				array_shift($html);
			
			if(!isset($html[0]))return $http;
			$menu=array_shift($html);
			$content=array_shift($html);
			if(!instr($url,"notifications.php"))
				$this->notification->parseMenu($menu);
			return $content;
		}else return $http;
	}

}


 ?>