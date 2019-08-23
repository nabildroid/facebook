<?php 
class Common{
	public $root=null;
	public $html="";
	public $lastHttpRequest=null;
	public function __construct(){
		//prepare the root variable
		if(!$this->root){
			$this->root=$this->parent;
			while(!is_a($this->root,"Account")&&isset($this->root->parent))
				$this->root=$this->root->parent;
		}
	}
	public function http($url="",$data="",$headers=[],$responseHeader=0){		
		//check if previous request is equivalent to current one 
		if($this->lastHttpRequest===[$url,$data,$headers,$responseHeader])
			return $this->html;
		else $this->lastHttpRequest=[$url,$data,$headers,$responseHeader];

		$this->html=$this->root->http($url,$data,$headers,$responseHeader);
		$this->getMyId();
	}
	/**
		* to prevent uncessary request that it's response aready exist
		* @param $response the response that request suppose to return 
		* @param $url,$data,$headers,responseHeader are responsable of such response
		* @return null
	**/
	public function fixHttpResponse($response,$url="",$data="",$headers=[],$responseHeader=0){
		$this->html=$response;
		$this->lastHttpRequest=[$url,$data,$headers,$responseHeader];		
	}
	public function dom($search,$getAttribute=0,$grabText=0){
		return dom($this->html,$search,$getAttribute,$grabText);
	}
	/**
		* parse all the html content and get from any form that has an action this account id it's usually named a "av" in form action paramater
		* @return int this account id or null if the html content hasn't id
	**/
	public function getMyId(){
		$id=$this->root->profile->id();
		if($this->html&&!intval($id)){
			preg_match_all("/<form.*action=.*av=.(\d)*?(?=&)/",$this->html,$id);
			if(!isset($id[0][0]))return null;
			$id=explode("=",$id[0][0]);
			$id=intval(array_pop($id));
		}
		return $this->root->profile->info["id"]=$id;
	}
	/**
		*submit any form 
		* @param $html is the content(HTML) of form that hold any inputs
		* @param $url where the form action happen 
		* @param $values array of all values that will submitted by order, can be a text or **url** for files
		* @param $target_submit if one submit must trigger in this form
		* @param forceInput is key/value pair for forcing any input to take static value
	*/
	public function submit_form($html,$url,$values=[],$target_submit="",$forceInput=""){
		$inputs=dom($html,["<input","<textarea"],1);
		$files=[];
		$data=[];
		foreach ($inputs as $input) {
			if(isset($input[1]))$input=$input[1];
				//text inputs
				if($input["find_tag"]=="<textarea"||$input["type"]=="text")
					$data[$input["name"]]=array_shift($values);

				//hidden inputs
				elseif($input["type"]=="hidden"&&isset($input["name"])){
					//when uploading multi images some hidden input takes mylti values 
					//and such input his name allways and with [] to indicate array 
					$input["name"]=str_replace("[]", "",$input["name"]);

					$value=isset($input["value"])?$input["value"]:"";
					if(isset($data[$input["name"]])&&$value){
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
					$file_path="./temp/".uniqid().".jpg";
					$img=array_shift($values);
					if($img){
						file_put_contents($file_path,file_get_contents($img));
						$files[]=$file_path;
						$file = new CURLFile($file_path,mime_content_type($file_path),$input["name"]);	
						$data[$input["name"]]=$file;
					}
				}
		}
		if($forceInput){
			foreach ($forceInput as $key => $value)
				$data[$key]=$value;
		}
		
		if(!$files)$data=http_build_query($data);

		$this->http($url,$data);
		if($files){
			foreach ($files as $file)
				unlink($file);
		}
	}

	/**
		*@return such class id if it exist
	**/
	public function id(){
		if(isset($this->info["id"])&&$this->info["id"])
			return $this->info["id"];
		else return null;
	}

}

 ?>