<?php
require "./functions/http.php";
class  System{
	public function http($url,$data="",$headers=[]){
		$url="https://mbasic.facebook.com/".$url;
		return ping($url,$data,array_merge([
			"Cookie:".$this->cookie
		],$headers));
	}

	
}


 ?>