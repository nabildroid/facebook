<?php 


function dom($cn,$search,$header=0,$nclose=0,$grabText=0){
	//version:1.3.2
	
	// $cn 	   => content
	// $search => what we are loking for 
	// $header => iclude attributes of tag
	// $nclose => if the tag does't has closed tag 
	$cnt=0;// controle between find get tag and save it  
	$stok=array();// for save all tag into it
	$item=""; // for save one tag into it
	$headers=array(); // for stoke attribute name and value accusiative array fo
	$text="";//for save all not wrapped text by desire tag
	// delete any comment from cn
	$cn=comment($cn);
	for ($i=0; $i <strlen($cn) ; $i++) {
		// find tag
		if($cnt==0){
			$find_tag=multiSearch($search,$cn,$i);
			if($find_tag){
				$tag=1;$cnt=!$nclose?1:0;$i+=strlen($find_tag);
				$attr="";
				for ($i=$i; $i >=0 ; $i--) //start back when  the tag begin 
					if($cn[$i]=="<")break;

				for($i=$i;$i<strlen($cn);$i++){ // grap all attribute from tag
						if($cn[$i]==">"){$i++;break;}
					$attr.=$cn[$i];
				}
				if($header){
					for ($h=0; $h < strlen($attr); $h++) { 
						if($attr[$h]=="="){
							//get the attr name
							$l_attr_value="";$l_attr_name="";
							for ($hr=$h-1; $hr >=0; $hr--) { // header right 
								if($attr[$hr]==" ")break; 
								$l_attr_name.=$attr[$hr]; // local attribute name
							}
							for ($hl=$h+2; $hl < strlen($attr)-1 ; $hl++) { // header left 
								if(($attr[$hl]=="'"||$attr[$hl]=='"')&&($hl+2>= strlen($attr)||$attr[$hl+1]==' '))
									{$h=$hl;break;} 
								$l_attr_value.=$attr[$hl]; // local attribute value
							}
							$headers[strrev($l_attr_name)]=$l_attr_value;
						}
					}
				}
				if($nclose){
					$cnt=0;
					$stok[]=$headers;
					$headers=array();
					$item="";
					$i--;
					continue;
				}
				if($grabText&&trim($text)){ //store grabed text
					$stok[]=["",["text"=>$text]];
					$text="";
				}
			}elseif($grabText){
				$text.=$cn[$i];
			}
		}
		// save tag
		if($cnt==1){ 
			// echo $tag==$tag1?'':$tag;
			// $tag1=$tag;
			//ignore comments
			if($cn[$i]=="<"&&$cn[$i+1]=="!"&&$cn[$i+2]=="-"&&$cn[$i+3]=="-"){
				$i+=3;
				for ($c=$i; $c <strlen($cn) ; $c++) { 
					if($cn[$c]=="-"&&$cn[$c+1]=="-"&&$cn[$c+2]==">")
						{$i=$c+3;break;}
				}	
			}
			if($cn[$i]=="<" && $cn[$i+1]!=="/" 
				&& !($cn[$i+1]==="!") 
				&& !($cn[$i+1]==="b" && $cn[$i+2]==="r") 
				&& !($cn[$i+1]==="h" && $cn[$i+2]==="r") 
				&& !($cn[$i+1]==="i" && $cn[$i+2]==="m" && $cn[$i+3]==="g") 
				&& !($cn[$i+1]==="w" && $cn[$i+2]==="b" && $cn[$i+3]==="r") 
				&& !($cn[$i+1]==="m" && $cn[$i+2]==="e" && $cn[$i+3]==="t"&& $cn[$i+4]==="a")
				&& !($cn[$i+1]==="l" && $cn[$i+2]==="i" && $cn[$i+3]==="n"&& $cn[$i+4]==="k")
				&& !($cn[$i+1]==="i"&&$cn[$i+2]==="n"&&$cn[$i+3]==="p"&& $cn[$i+4]==="u"&& $cn[$i+5]==="t")
				&& !($cn[$i+1]==="s"&&$cn[$i+2]==="o"&&$cn[$i+3]==="u"&& $cn[$i+4]==="r"&& $cn[$i+5]==="c"&& $cn[$i+6]==="e")
				){$tag++;}
			if(($cn[$i]=="<" && $cn[$i+1] =="/")&&
				!($cn[$i+2]==" " &&$cn[$i+3]=="b"&&$cn[$i+4]=="r")&&
				!($cn[$i+2]=="b"&&$cn[$i+3]=="r")&&
				!($cn[$i+2]==" "&&$cn[$i+3]=="h"&&$cn[$i+4]=="r")
			){$tag--;}
			if($tag==0){
				//go to next letter after the close of this  tag
				for($i=$i;$i<strlen($cn);$i++)
						if($cn[$i]==">")break;
				if($header)
					$stok[]=array($item,$headers);
				else $stok[]=$item;
				//reset every things
				$headers=array();
				$item="";
				$cnt=0;
				continue;
			}else 
				$item.=$cn[$i];
		}
	}
	//save the last grabbed text if exist
	if(trim($text))
		$stok[]=["",["text"=>$text]];
	return $stok;
}
function comment($cn){
	$temp="";
	$cnt=1;
	for ($i=0; $i <strlen($cn) ; $i++) { 
		if($cnt&&($cn[$i]=="<"&&$cn[$i+1]=="!"&&$cn[$i+2]=="-"))
			{$cnt=0;$i+=2;continue;}
		if(!$cnt&&($cn[$i]=="-"&&$cn[$i+1]=="-"&&$cn[$i+2]==">"))
			{$cnt=1;$i+=2;continue;}
		if($cnt)$temp.=$cn[$i];
	}
	return $temp;
}
function multiSearch($search,$cn,$i){
	if(is_array($search))
		foreach ($search as $key) {
			if(substr($cn,$i,strlen($key))==$key)
				return $key;
		}
	elseif(substr($cn,$i,strlen($search))==$search)
		return $search;
	else return false;
}

function strcut($str,$s,$e,$split=""){
	$target="";
	$remain="";
	$str=preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
	for ($i=0; $i <count($str) ; $i++) { 
		if($i>=$s-1&&$i<$e+1){
			$target.=$str[$i];
			if($i+1==$e||$i>count($str)-2){
				$remain.=$split;
			}
		}
		else $remain.=$str[$i];
	}
	return [$target,$remain];
}

function doms($cn,$tags){
	foreach ($tags as $tag) {
		$cn=is_array($cn)?$cn[0]:$cn;
		$cn=dom($cn,$tag);
	}
	return $cn;
}


 ?>
