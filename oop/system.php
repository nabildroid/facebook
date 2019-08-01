<?php
require "./functions/http.php";
class  System{
	public function http($url,$data="",$headers=[],$responseHeader=0){
		$url="https://mbasic.facebook.com/".$url;
		return ping($url,$data,array_merge([
			"Cookie:".$this->cookie
		],$headers),$responseHeader);
	}

	
}


 ?>