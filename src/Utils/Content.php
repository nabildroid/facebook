<?php 
namespace Facebook\Utils;
use Facebook\Profile\Profile;
use Facebook\Page\Page;
use Facebook\Group\Group;
class Content{
	//those functions all part of parseContent 
	static function ignoreSameComposition($arr){
		for ($i=0; $i < count($arr) ; $i++) { 
			if(!isset($arr[$i]))continue;
			$index=$arr[$i];
			while(is_array($index["child"])&&count($index["child"])==1&&$index["type"]==$index["child"][0]["type"]){
				$temp=$index["child"][0];
				if(isset($index["attr"])&&$index["attr"]&&(!isset($temp["attr"])||!$temp["attr"]))
					$temp["attr"]=$index["attr"];
				$index=$temp;
			}
			if(isset($index["child"])&&is_array($index["child"])&&$index["child"]){
				$arr[$i]["child"]=self::ignoreSameComposition($index["child"]);
				if(isset($index["attr"]))
					$arr[$i]["attr"]=$index["attr"];
				//check if all his childs has same type as the parent
				if($arr[$i]["type"]!=2){
					$check=count($arr[$i]["child"])>1;
					foreach ($arr[$i]["child"] as $child) {
						if(isset($child["type"])&&$arr[$i]["type"]!=$child["type"]){
							$check=false;
							break;
						}
					}
					if($check){
						
						array_splice($arr,$i,1,$arr[$i]["child"]);
					}
				}
			}
			else $arr[$i]=$index;
		}
		return $arr;
	}
	static function cleanHtml($arr){
		$new=[];//new array that will hold clean  Html
		if(!is_array($arr))$arr=[$arr];//to force all parameter to be array
		for ($i=0; $i <count($arr) ; $i++) { 
			$type=4;$child=[];
			$attr=[];
			if(!isset($arr[$i]))continue;
			if(is_string($arr[$i]))
				$child=$arr[$i];
			else if(isset($arr[$i][1])) {
				$type=self::detectTagType($arr[$i][1]["find_tag"]);
				$child=self::cleanHtml($arr[$i][0]);
			}
			else {
				$type=self::detectTagType($arr[$i]["find_tag"]);
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
	static function detectTagType($tag){
		if($tag=="<a")
			return 1;
		elseif($tag=="<img")
			return 2;
		elseif($tag=="<p")
			return 4;
		elseif($tag=="<div")
			return 3;
		elseif($tag=="<span") // it makes the content more complex
			return 4;
		else return 4;//text
	}
	static function branchApplay($arr,$tags){
		for ($i=0; $i <count($arr) ; $i++) { 
			//deal with free text
			if(is_string($arr[$i])&&trim($arr[$i])){
				$temp=Html::dom($arr[$i],$tags,1,1);
				if($temp&&(count($temp)>1||is_array($temp[0])))
					$arr[$i]=self::branchApplay($temp,$tags);
			}
			//deal with elements
			if( is_array($arr[$i]) &&isset($arr[$i][0])&& is_string($arr[$i][0]) ){
				$temp=Html::dom($arr[$i][0],$tags,1,1);
				if($temp)
				$arr[$i][0]=self::branchApplay($temp,$tags);
			}
		}
		return $arr;
	}
	static function readableContent($arr){
		$result=[];
		if(!is_array($arr))$result=$arr;
		else
		for ($i=0; $i <count($arr) ; $i++) {
			if(!isset($arr[$i]))continue;
			$type=$arr[$i]["type"];
			$content=isset($arr[$i]["child"])?$arr[$i]["child"]:"";
			$attr=isset($arr[$i]["attr"])?$arr[$i]["attr"]:[];
			$temp=[];
			if($type==4&&isset($attr["style"])&&Util::instr($attr["style"],"background-image:")){
				preg_match_all("/url\(\".*?(?=\")/",$attr["style"],$emoji);
				if(isset($emoji[0][0]))
					$emoji=substr($emoji[0][0],5);
				else $emoji="";
				if($emoji){
					$temp=["type"=>"emoji","src"=>$emoji];	
				}else $temp=["type"=>"text"];
			}
			elseif($type==4){
				$temp=["type"=>"text"];
			}
			elseif($type==3&&Util::FindInTree($content,"<strong")){
				$temp=["type"=>"post"];
				$content=array_splice($arr,$i+1);
			}
			elseif ($type==3) {
				$temp=["type"=>"block"];
			}
			elseif ($type==1&&isset($attr["href"])&&Util::instr($attr["href"],"hashtag")) {
				$temp=["type"=>"hashtag","href"=>urldecode($attr["href"])];
			}
			elseif ($type==1&&isset($attr["href"])&&Util::instr($attr["href"],"video_redirect")) {
				$temp=["type"=>"video","href"=>urldecode($attr["href"])];
				$content="";
			}
			elseif ($type==1&&$content&&$content[0]["type"]==2) {
				$temp=["type"=>"photo","href"=>urldecode($attr["href"])];
				$content="";
			}
			elseif($type==1&&$attr["href"]&&Profile::idFromUrl($attr["href"])){
				$temp=["type"=>"profile","id"=>Profile::idFromUrl($attr["href"])];
			}
			elseif($type==1&&$attr["href"]&&Group::idFromUrl($attr["href"])){
				$temp=["type"=>"group","id"=>Group::idFromUrl($attr["href"])];
			}
			elseif($type==1&&$attr["href"]&&Page::idFromUrl($attr["href"])){
				$temp=["type"=>"page","id"=>Page::idFromUrl($attr["href"])];
			}
			elseif($type==1&&$attr["href"]){
				$temp=["type"=>"link","href"=>urldecode($attr["href"])];
			}
			elseif($type==2&&isset($content["src"])&&$content["src"]){
				$temp=["type"=>"image","src"=>urldecode($content["src"])];
			}
			else $temp=["type"=>$type];
			$content=self::readableContent($content);
			if($content)
				$temp["content"]=$content;
			if($temp&&count(array_keys($temp))>1)
				array_push($result,$temp);
		}
		return $result;
	}
	static function deleteTables($html){
		while(Util::instr($html,"<table")){
			$start=strpos($html,"<table")+1;
			$end=strpos($html,"</table>")+7;
			$arr=Util::strcut($html,$start,$end);
			$needle=Html::dom($arr[0],"<td");
			$needle=join($needle);
			$html=substr_replace($arr[1],$needle,$start-1,0);
		}
		return $html;
	}
	//main parseContent
	static function parse($html){
		$tags=["<p","<div","<a","<img","<span"];
		if(is_array($html))$html=implode("", $html);
		$html=self::deleteTables($html);
		//delete any <wbr>
		$html=str_replace("<wbr />","",$html);
		$html=Html::dom($html,$tags,1,1);
		$html1=self::branchApplay($html,$tags);
		$content=self::cleanHtml($html1);
		$content=self::ignoreSameComposition($content);
		$content=self::readableContent($content);
		return $content;
	}
	/**
	 * make html from readableContent
	 * @param $arr is what the parse() returns;
	 * @return string (html)
	 */
	static function flat($arr){
		$content="";
		if(is_string($arr))return $arr;

		foreach ($arr as $elm){
			$issetChild=isset($elm["content"])&&$elm["content"];
			if(($elm["type"]=="block"||$elm["type"]=="post")&&$issetChild){
				$content.="<div>";
				$content.=self::flat($elm["content"]);
				$content.="</div>";
			}
			elseif($elm["type"]=="emoji"){
				$content.="<img ";
				if(isset($elm["src"]))
					$content.="src='".$elm["src"]."' ";
				$content.="alt='".$elm["content"]."'";
				$content.=">";
			}
			elseif($elm["type"]=="text"&&is_string($elm["content"])){				
				$content.=$elm["content"];
			}
			elseif($elm["type"]=="text"){
				$content.="<p>";
				$content.=self::flat($elm["content"]);
				$content.="</p>";	
			}
			elseif($elm["type"]=="link"){
				$content.="<a ";
				if(isset($elm["href"]))
					$content.="href='".$elm["href"]."'";
				$content.=">";
				if($issetChild)
					$content.=self::flat($elm["content"]);
				else $content.="link";
				$content.="</a>";
			}
			elseif($elm["type"]=="photo"){
				$content.="<a ";
				if(isset($elm["href"]))
					$content.="href='".$elm["href"]."'";
				$content.=">";
				$content.="photo";
				$content.="</a>";
			}
			elseif($elm["type"]=="hashtag"){
				$content.="<a ";
				if(isset($elm["href"]))
					$content.="href='".$elm["href"]."'";
				$content.=">";
				if($issetChild)
					$content.=self::flat($elm["content"]);
				$content.="</a>";
			}
			elseif($elm["type"]=="profile"||$elm["type"]=="page"||$elm["type"]=="group"){
				$content.="<a ";
				$content.="href='".$elm["id"]."'";
				$content.=">";
				if($issetChild)
					$content.=self::flat($elm["content"]);
				$content.="</a>";
			}
			elseif($elm["type"]=="image"){
				$content.="<img ";
				$content.="src='".$elm["src"]."'";
				$content.=">";
			}
			elseif($elm["type"]=="video"){
				$content.="<a ";
				if(isset($elm["href"]))
					$content.="href='".$elm["href"]."'";
				$content.=">";
				$content.="video";
				$content.="</a>";
			}
		}
		return $content;
	}
}



?>