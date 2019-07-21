<?php 

function jsondecode($txt){
	$txt=html_entity_decode($txt);
	$txt=stripslashes(trim($txt));
	$txt= json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $txt), true );
	return $txt;
}
function findInnerText($txt,$array){
	foreach ($array as $arr) {
		if($arr[0]==$txt)
			return $arr; 
	}
}

function filter($arr,$callback=false){
	$new=[];
	$not=[];
	foreach ($arr as $a) {
		if(!$callback&&$a||($callback&&call_user_func($callback,$a)))
			array_push($new,$a);
		else array_push($not,$a);
	}
	return [$new,$not];
}



 ?>