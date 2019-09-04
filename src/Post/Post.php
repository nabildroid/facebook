<?php 
namespace Facebook\Post;
use Facebook\Utils\Html;
use Facebook\Utils\Util;
use Facebook\Utils\Content;

use Facebook\Page\Page;
use Facebook\Profile\Profile;
use Facebook\Group\Group;

class Post extends \Facebook\Common{
	use actions;
	use comments;
	use fullcontent;
	use splithtml;
	use parsesource;
	use wholikes;

	//basic info
	public $id;     //id of comment 
	public $user;   //the auther of this comment [Profile]
	public $content;
	public $picture;  //url of full size image
	public $admin=0;//if this post is mine or not

	//likes information including link to make a like 
	public $likes=[
		"length"=>0,  //number of likes
		"users"=>[],  //users who likes this comment [array of profile]
		"mine"=>0,    //if this account has been liked this post
		"url"=>"",    //url of page contain all users who likes
		"like"=>""    //url for make a new like to such comment
	];

	//comment inforamtion
	public $childs=[  
		/* @todo "length"=>0, //number of replys */
		"items"=>[], //array contain multi arrays of comment array for each page 
		"next_page"=>null,//url lead to next page of comment
		"next_page_indicator"=>"",//sometimes initiale caption of next reply page is "View previous replies" and othertime is "View previous replies" 
		"add"=>""    //html form for create a reply 
	];

	//source of such post
	public $source=[
		"origin"=>"", //if such post is shared from another one 
		"page"=>"",   //post cames from a page
		"group"=>""   //post cames from a agoup
	];


	
	function __construct($parent,$id=null){
		$this->parent=$parent;
		parent::__construct();
		
		$this->id=$id;
	}
	//get informaion about this post from it id
	public function fetch($force=0){
		if(!$force&&$this->fetched)return;
		$this->http($this->id);
		if(is_array($this->html))
			$data=$this->parseInlinePost();
		else{
			$type=self::detectType($this->html);
			if($type)
				$data=$this->splitImageHtml();
			else
				$data=$this->splitPostHtml();
		}
		////common property
		$this->content=Content::parse($data["content"]);
		$this->likes["length"]=$data["likes_length"];
		$this->likes["like"]=$data["like_link"];
		$this->likes["mine"]=$data["already_liked"];
		//source
		$source=self::parseSource($data["source"]["html"],$data["source"]["attribute"]);
		//source basic
		if($source["user"]){
			if($this->checkIsMine($source["user"]["id"]))
				$this->user=$this->root->profile;
			else{
				$this->user=new Profile($this,$source["user"]["id"]);
				$this->user->name=$source["user"]["name"];
			}		
		}
		$this->id=$source["id"]["id"];

		//source options
		if($source["origin_post"]["id"]){
			$this->source["origin"]=new Post($this,$source["origin_post"]["id"]);
		}
		if($source["page"]["id"]){
			$this->source["page"]=new Page($this->root,$source["page"]["id"]);
			$this->source["page"]->name=$source["page"]["name"];
		}
		if($source["group"]["id"]){
			$group_id=Group::idFromUrl($source["group"]["id"]);
			//note: is this->root right parent for such group or this post may be his parent!!
			$this->source["group"]=new Group($this->root,$group_id);
			$this->source["group"]->name=$source["group"]["name"];
		}
		//image
		if(isset($data["image"][1]["href"]))
			$this->picture=$data["image"][1]["href"];
		//comments
		if(isset($data["comments_html"]))
			$this->parseCommentSection($data["comments_html"]);

		

		$this->fetched=1;
	}
	//detect wether this post is for me or not
	private function checkIsMine($user){
		if($user==$this->root->profile->id)
			return $this->admin=true;
	  else return $this->admin=false;
	}
	/**
	 * convert likes number from string to int for example 1k => 1000 1,500 => 1500
	 * @param $str likes_number_str string 
	 * @return intiger
	 */
	static function LikesStringToInt($str){
		preg_match_all("/[\d,\.]*(?:\w)/",$str,$str);
		$str=str_replace(",","",$str[0][0]);
		preg_match_all("/\w$/",$str,$suffix);
		$suffix=isset($suffix[0][0])?$suffix[0][0]:"";

		preg_match_all("/[\d.]*/",$str,$flt);
		$flt=isset($flt[0][0])?floatval($flt[0][0]):0;

		switch (strtolower($suffix)) {
			case 'k':
				$flt*=1000;
				break;
			case 'm':
				$flt*=1000000;
				break;
		}
		if($str&&!$flt)$flt=1;
		return $flt;
	}

}



?>