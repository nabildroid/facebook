<?php 

function jsondecode($txt){
	$txt=stripslashes(trim($txt));
	$txt= json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $txt), true );
	return $txt;
}
/**
	* costumised filter function or only true values filter in default (without @param $callback)
	* @param $arr the array that will filtered
	* @param $callback is the indicator if element is accepted or nor
	* @return array(array of accepted , array of the rest)
**/
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
 * @param $origin the template of the array
 * @param $new where the template take thier values
 * @param (boolean) $takeEmptyChild override template value even if the @param $new has empty value
 * @return template with new value
 */
function mergeAssociativeArray($origin,$new,$takeEmptyChild=0){
	foreach ($origin as $key => $value) {
		if(isset($new[$key])&&($new[$key]||!$new["$key"]&&$takeEmptyChild)){
			$origin[$key]=$new[$key];
		}
	}
	return $origin;
}
/**
 * search in string 
 * @param $str  string that the search will apply to
 * @param $s target could be string or array 
 * @return boolean if @param $s is string or if it array return will be string that match
 */
function instr($str,$s){
	if(is_array($s)){
			foreach ($s as $key) {
				if(strpos($str,$key)!==false)
					return $key;
			}return false;
	}else
	return strpos($str,$s)!==false;
}

 ?>