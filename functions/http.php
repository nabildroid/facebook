<?php 
function ping($url,$data="",$headers=[],$responseHeader=0){
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
	if($responseHeader){
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
	}


	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 5);

	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) snap Chromium/75.0.3770.142 Chrome/75.0.3770.142 Safari/537.36");

	$response = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	    exit();
	}

	if($responseHeader){
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$response = substr($response, $header_size);
		return ["body"=>html_entity_decode($response),"header"=>$header];
	}else return html_entity_decode($response);

}

 ?>