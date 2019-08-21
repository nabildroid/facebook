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


//those functions all part of parseContent 
function ignoreSameComposition($arr){
	for ($i=0; $i < count($arr) ; $i++) { 
		if(!isset($arr[$i]))continue;
		$index=$arr[$i];
		while(is_array($index["child"])&&count($index["child"])==1&&$index["type"]==$index["child"][0]["type"]){
			$index=$index["child"][0];
		}

		if(is_array($index["child"])&&$index["child"]){
			$arr[$i]["child"]=ignoreSameComposition($index["child"]);
			if(isset($index["attr"]))
				$arr[$i]["attr"]=$index["attr"];
		}
		else $arr[$i]=$index;
	}
	return $arr;
}
function cleanHtml($arr){
	$new=[];//new array that will hold clean  Html
	if(!is_array($arr))$arr=[$arr];//to force all parameter to be array
	for ($i=0; $i <count($arr) ; $i++) { 
		$type=0;$child=[];
		$attr=[];
		if(!isset($arr[$i]))continue;
		if(is_string($arr[$i]))
			$child=$arr[$i];
		else if(isset($arr[$i][1])) {
			$type=detectTagType($arr[$i][1]["find_tag"]);
			$child=cleanHtml($arr[$i][0]);
		}
		else {
			$type=detectTagType($arr[$i]["find_tag"]);
			$child=$arr[$i];
		}
		if($child){
			if(isset($arr[$i][1])&&is_array($arr[$i][1])&&$arr[$i][1])
				$new[]=["type"=>$type,"child"=>$child,"attr"=>$arr[$i][1]];		
			else $new[]=["type"=>$type,"child"=>$child];
		}
	}
	return $new;
}
function detectTagType($tag){
	if($tag=="<a")
		return 1;
	elseif($tag=="<img")
		return 2;
	elseif($tag=="<p")
		return 3;
	elseif($tag=="<div")
		return 4;
	// elseif($tag=="<span") // it makes the content more complex
	// 	return 5;
	else return 0;//text
}
function branchApplay($arr,$tags){
	for ($i=0; $i <count($arr) ; $i++) { 
		//deal with free text
		if(is_string($arr[$i])&&trim($arr[$i])){
			$temp=dom($arr[$i],$tags,1,1);
			if($temp&&(count($temp)>1||is_array($temp[0])))
				$arr[$i]=branchApplay($temp,$tags);
		}
		//deal with elements
		if( is_array($arr[$i]) &&isset($arr[$i][0])&& is_string($arr[$i][0]) ){
			$temp=dom($arr[$i][0],$tags,1,1);
			if($temp)
			$arr[$i][0]=branchApplay($temp,$tags);
		}
	}
	return $arr;
}

//main parseContent
function parseContent($html){
	$tags=["<p","<div","<a","<img","<span"];
	if(is_array($html))$html=join($html);
	$html=dom($html,$tags,1,1);
	$html1=branchApplay($html,$tags);
	// echo json_encode(cleanHtml($html1));
	return (ignoreSameComposition(cleanHtml($html1)));

}

/**
	*concatenate all single child of type 0
	*@param $arr is what the parseContent() return;
	*@return string
**/
function flatContent($arr){
	$content="";
	for ($i=0; $i < count($arr) ; $i++) { 
		if(!isset($arr[$i]))continue;
		$index=$arr[$i];
		while(is_array($index["child"])&&count($index["child"])==1)
			$index=$index["child"][0];

		if(is_array($index["child"])&&$index["child"]){
			$temp=flatContent($index["child"]);
			if($temp)
				$content.="<child>".$temp;
		}elseif(!is_array($index["child"])&&$index["child"])
			$content.=$index["child"];
		elseif($index) $content.=$index;
	}
	return $content;	
}


/**
	* recursive function for  search on  depth of any array it's good for select one element in doms array
	* @param $tree, array that contain all elements including our $target
	* @param $target, what we looking for, i doesn't metter if it array or part of full string 
	* @return boolean
**/
function FindInTree($tree,$target){
	if(is_array($tree)){
		foreach($tree as $key => $value){
			if(is_array($target)){
				if (is_array($value)&&$target===$value)
					return true;
				else{
					foreach ($target as $Tkey => $Tvalue) {
						if($Tkey===$key&&$Tvalue===$value)
							return true;
						elseif(is_array($value)&&FindInTree($value,[$Tkey=>$Tvalue]))
							return true;
					}
				}
			}
			elseif(is_array($value)&&FindInTree($value,$target))
				return true;
			elseif(strpos($key,$target)!==false||$value===$target)
				return true;
			elseif (!is_array($value)&&strpos($value,$target)!==false)
				return true;
		}
	}elseif(!is_array($target)&&strpos($tree,$target)!==false)
		return true;
}

function findDom($doms,$target){
	$doms=filter($doms,function ($d) use (&$target){
		return FindInTree($d,$target);
	});
	if(isset($doms[0][0]))
		return $doms[0][0];
	else return [];
}
/**
*@param $origin the template of the array
*@param $new where the template take thier values
*@return template with new value
*/
function mergeAssociativeArray($origin,$new){
	foreach ($origin as $key => $value) {
		if(isset($new[$key])){
			$origin[$key]=$new[$key];
		}
	}
	return $origin;
}

function instr($str,$s){
	return strpos($str,$s)!==false;
}

 ?>