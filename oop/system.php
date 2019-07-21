<?php
require "./functions/http.php";
class  System{
	public function http($url,$data=""){
		$url="https://mbasic.facebook.com/".$url;
		return ping($url,$data,[
			"Cookie:".$this->cookie
		]);
	}

	
}


 ?>