<?php 
function ping($url,$data="",$headers=[]){
	var_dump("1");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if($data){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	}
	if($headers){
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
	}


	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 5);
	//curl_setopt($ch, CURLOPT_VERBOSE, 1);
	//curl_setopt($ch, CURLOPT_HEADER, 1);

	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	
	$result = curl_exec($ch);


	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	    exit();
	}
	return html_entity_decode($result);
}

 ?>