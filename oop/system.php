<?php
class  System{
	
	/**
		* @todo BAD BAD BAD ... it's provide a way to access full account :( :( 
		* it public now because of some satic function need to call http in post of exemple
	*/
	public function http($url,$data="",$headers=[],$responseHeader=0){
		if(strpos($url,"http")!==0)
			$url="https://mbasic.facebook.com/".$url;
		$http=ping($url,$data,array_merge([
			"Cookie:".$this->cookie
		],$headers),$responseHeader);
		if(is_string($http)){
			$html=doms($http,["<div","<div","<div"]);
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