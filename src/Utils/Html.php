<?php 
namespace Facebook\Utils;

class Html{
	static function dom($cn,$search,$header=0,$grabText=0){
		//version:2.0.2
		
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
		$find_tag="";//desire tag
		$cn=self::comment($cn);
		for ($i=0; $i <strlen($cn) ; $i++) {
			// find tag
			if($cnt==0){
				$find_tag=self::multiSearch($search,$cn,$i);
				if($find_tag){
					$nclose=self::autoCloseTagsDetect($find_tag,1)==true;//if the tag does't has closed tag 
					$tag=1;$cnt=!$nclose?1:0;$i+=strlen($find_tag);
					$attr="";
					for ($i=$i; $i >=0 ; $i--) //start back when  the tag begin 
						if($cn[$i]=="<")break;

					for($i=$i;$i<strlen($cn);$i++){ // grap all attribute from tag
							if($cn[$i]==">"){$i++;break;}
						$attr.=$cn[$i];
					}
					if($header||$nclose){
						for ($h=0; $h < strlen($attr); $h++) { 
							if($attr[$h]=="="&&($attr[$h+1]=="'"||$attr[$h+1]=='"')){
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
					if($grabText&&trim($text)){ //store grabed text
						$stok[]=$text;
						$text="";					
					}
					if($nclose){
						$cnt=0;
						$headers["find_tag"]=$find_tag;
						$stok[]=$headers;
						$headers=array();
						$item="";
						$i--;
					}
				}elseif($grabText)
					$text.=$cn[$i];
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
				if($cn[$i]=="<" && $cn[$i+1]!="/" && !self::autoCloseTagsDetect($cn,$i+1))
					{$tag++;}
				elseif($cn[$i]=="<" && $cn[$i+1] =="/" && !self::autoCloseTagsDetect($cn,$i+1))
					{$tag--;}
				if($tag==0){
					//go to next letter after the close of this tag
					for($i=$i;$i<strlen($cn);$i++)
							if($cn[$i]==">")break;

					if($header){
						$headers["find_tag"]=$find_tag;
						$stok[]=array($item,$headers);
					}
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
			$stok[]=$text;
		return $stok;
	}
	static function comment($cn){
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
	static function autoCloseTagsDetect($cn,$i){
		$tags=["img","link","meta","source","wbr","br"," br","hr"," hr","input"];
		return self::multiSearch($tags,$cn,$i);
	}
	static function multiSearch($search,$cn,$i){
		if(is_array($search))
			foreach ($search as $key) {
				$indicator=substr($cn,$i,strlen($key)+1);
				if(preg_match("/^".$key."(?!\w)/",$indicator))
				return $key;
			}
		elseif(preg_match("/^".$search."(?!\w)/",substr($cn,$i,strlen($search)+1)))
			return $search;
		else return false;
	}

	static function doms($cn,$tags){
		foreach ($tags as $tag) {
			$cn=is_array($cn)?$cn[0]:$cn;
			$cn=self::dom($cn,$tag);
		}
		return $cn;
	}

	static function findDom($doms,$target){
		$doms=Util::filter($doms,function ($d) use (&$target){
			return Util::FindInTree($d,$target);
		});
		if(isset($doms[0][0]))
			return $doms[0][0];
		else return [];
	}
}

 ?>
