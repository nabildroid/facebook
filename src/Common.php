<?php
namespace Facebook;
use Facebook\Utils\Html;
use Facebook\Utils\Util;

abstract class Common extends Errors{
	public $root=null;
	public $parent=null;
	public $html="";
	public $lastHttpRequest=null;

	protected $fetched=0; //toggle for prevent more then one fetch

	public function __construct(){
		//prepare the root variable
		if(!$this->root){
			$this->root=$this->parent;
			while(!is_a($this->root,"Account")&&isset($this->root->parent))
				$this->root=$this->root->parent;
		}
	}
	/**
		* @todo BAD BAD BAD ... it's provide a way to access full account :( :(
		* it public now because of some satic function need to call http in post of exemple
	*/
	public  function http($url="",$data="",$headers=[],$responseHeader=0){
		//check if previous request is equivalent to current one
		if($this->lastHttpRequest==[$url,$data,$headers,$responseHeader])
			return $this->html;
		else $this->lastHttpRequest=[$url,$data,$headers,$responseHeader];

		$this->html=$this->root->http($url,$data,$headers,$responseHeader);
	}

	/**
		* to prevent uncessary request that it's response aready exist
		* @param $response the response that request suppose to return
		* @param $url,$data,$headers,responseHeader are responsable of such response
		* @return null
	  */
	public function fixHttpResponse($response,$url="",$data="",$headers=[],$responseHeader=0){
		$this->html=$response;
		$this->lastHttpRequest=[$url,$data,$headers,$responseHeader];
	}
	protected function dom($search,$getAttribute=0,$grabText=0){
		return Html::dom($this->html,$search,$getAttribute,$grabText);
	}

	/**
	 *submit any form
	 * @param $html is the content(HTML) of form that hold any inputs
	 * @param $url where the form action happen
	 * @param $values array of all values that will submitted by order, can be a text or **url** for files
	 * @param $target_submit if one submit must trigger in this form
	 * @param forceInput is key/value pair for forcing any input to take static value
	 */
	protected function submit_form($html,$url,$values=[],$target_submit="",$forceInput="",$debug=0){
		$inputs=html::dom($html,["<input","<textarea"],1);
		$files=[];
		$data=[];
		foreach ($inputs as $input) {
			if(isset($input[1]))$input=$input[1];

				//when uploading multi images some hidden input takes multi values
				//and such input his name always end with [] to indicate array
				if(isset($input["name"]))
					$input["name"]=str_replace("[]", "",$input["name"]);

				//text inputs
				if($input["find_tag"]=="<textarea"||$input["type"]=="text"){
					$value=array_shift($values);

					//check if $input["name"] is referent to array or note
					if(isset($data[$input["name"]])&&$value){//referent to array
						if(!is_array($data[$input["name"]]))
							$data[$input["name"]]=[$data[$input["name"]],$value];
						else array_push($data[$input["name"]],$value);
					}else $data[$input["name"]]=$value;
				}
				//hidden inputs
				elseif($input["type"]=="hidden"&&isset($input["name"])){
					$value=isset($input["value"])?$input["value"]:"";

					//check if $input["name"] is referent to array or note
					if(isset($data[$input["name"]])&&$value){//referent to array
						if(!is_array($data[$input["name"]]))
							$data[$input["name"]]=[$data[$input["name"]],$value];
						else array_push($data[$input["name"]],$value);
					}else $data[$input["name"]]=$value;
				}
				// submit inputs
				elseif (isset($input["type"])&&$input["type"]=="submit"&&isset($input["name"])){
					if(!$target_submit||$target_submit&&strtolower($target_submit)==strtolower($input["name"]))
						$data[$input["name"]]=isset($input["value"])?$input["value"]:"";
				}
				/*
					files input
					download the pictures from URL then upload it then delete it
					all URLS of pictures are in @param $input
				*/
			 	elseif(isset($input["type"])&&$input["type"]=="file"){
					$file_path="./temp_images/".uniqid().".jpg";
					$img=array_shift($values);
					if($img){
						file_put_contents($file_path,file_get_contents($img));
						$files[]=$file_path;
						$file = new \CURLFile($file_path,mime_content_type($file_path),$input["name"]);
						$data[$input["name"]]=$file;
					}
				}
		}
		if($forceInput){
			foreach ($forceInput as $key => $value)
				$data[$key]=$value;
		}
		//note: delete this
		if($debug)var_dump($data);
		if(!$files)$data=http_build_query($data);

		$this->http($url,$data);
		if($files){
			foreach ($files as $file)
				unlink($file);
		}
	}


	##### GETTER -- SETTER #####

	public function getParent(){
		if($this->PARENT_TYPE){
			if(!is_a($this->parent,$this->PARENT_TYPE))
				$this->fetch();
		}
		return $this->parent;
	}

	/**
	 *@param $int get integer
	 *@return such class id if it exist
	 */
	public function getId($int=0){
		if($int||!$this->id)
			$this->fetch();
		return $this->id;
	}

	public function getUser(){
		if(!$this->user)
			$this->fetch();
		return $this->user;
	}

	public function getContent(){
		if(!$this->content)
			$this->fetch();
		return $this->content;
	}


	public function getLikes($prop=""){
		if(!$this->likes||$prop&&!$this->likes[$prop])
			$this->fetch();
		return $prop?$this->likes[$prop]:$this->likes;
	}

	public function getChilds($prop=""){
		if(!$this->childs||$prop&&!$this->childs[$prop])
			$this->fetch();
		return $prop?$this->childs[$prop]:$this->childs;
	}
	public function getSource($prop=""){
		if(!$this->source||$prop&&!$this->source[$prop])
			$this->fetch();
		return $prop?$this->source[$prop]:$this->source;
	}

	public function getPicture($prop=""){
		if(!$this->picture||$prop&&!$this->picture[$prop])
			$this->fetch();
		return $prop?$this->picture[$prop]:$this->picture;
	}
	public function getBio(){
		if(!$this->bio)
			$this->fetch();
		return $this->bio;
	}
	public function getAdmin(){
		$this->fetch();
		return $this->admin;
	}
	public function getName(){
		if(!$this->name)
			$this->fetch();
		return $this->name;
	}






}

 ?>