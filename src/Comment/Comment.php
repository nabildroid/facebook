<?php 
namespace Facebook\Comment;
use Facebook\Post\Post;

class Comment extends \Facebook\Common{
	use actions;
	use parse;
	use subcomments;
	use wholikes;

	//note: comment has two possiblity of parent [Post or Comment]
	protected $PARENT_TYPE="Post"; 
	//basic info
	public $id;     //id of comment 
	public $user;   //the auther of this comment [Profile]
	public $content;
	public $admin=0;  //if this comment is mine or not (the author)
	//likes information including link to make a like 
	public $likes=[
		"length"=>0,  //number of likes
		"users"=>[],  //users who likes this comment [array of profile]
		"mine"=>0,    //if this account has been liked this comment
		"url"=>"",    //url of page contain all users who likes
		"like"=>""    //url for make a new like to such comment
	];

	//subcomments inforamtion
	public $childs=[  
		"length"=>0, //number of replys
		"items"=>[], //array contain multi arrays of comment array for each page 
		"next_page"=>"",//url lead to next page of subcomments
		"next_page_indicator"=>"",//sometimes initiale caption of next reply page is "View previous replies" and othertime is "View previous replies" 
		"add"=>"",    //html form for create a reply 
	];

	function __construct($parent,$id=null){
		$this->parent=$parent;
		parent::__construct();

		$this->id=$id;
	}


	/**
	 * fetch content of this comment
	 * - it may has fixed response from his parent so $this->html is 
	 * contain only html of this comment
	 * - else for getting content must make a new http request and parse
	 * such response of http request case it's page of post or reply
	 */
	protected function fetch($force=0){
		if(!$force&&$this->fetched)return;
		//request the comment by it id
		$this->http($this->id);
		
		//detect the type of response page (either Post page or Reply page or singleComment html)
		$type=Post::detectType($this->html);
		$comments=[];
		//Post page (create new post fix it http then parse it comment)
		if($type!==false){			
			$post=new Post($this->root,$this->id);
			$post->fixHttpResponse($this->html,$this->id);
			$comments=$post->comments();
		}		
		/*single comment html note:bad criteria
		  the html is already fixed by fixHttpResponse*/
		elseif(strpos($this->html,"<h3")==5){
			$this->parseSingleComment();
		}
		//Reply page (get all replys make new Post (parent) assign it to such replys)
		else{
			$info=$this->splitReplys();
			preg_match_all("/story_fbid=\d+/",$info["origin_post"],$postId);
			$postId=intval(substr($postId[0][0],11));
			$post=new Post($postId,$this->root);
			$data=self::parseCommentSection($info["replys"],$post);
			$add=$data["add"];
			$comments=$data["items"];
		} 

		//comments exist if there are either page Post or Reply
		if($comments){
			foreach ($comments as $comment)
				if($comment->id==$this->id){
					$this->copyFrom($comment);break;
				}
			//case of this is reply comment so it has add to reply
			if(isset($add)&&$add)
				$this->childs["add"]=$add;

			/**
			 * case of next page in reply  doesn't has any sence
			 */
		}

		$this->fetched=1;
	}

	/**
	 * make this comment identical to @param $comment
	 * @todo is not important to get fetched information, sometimes the id is enough
	 * so instend of getting for exemple from getUser() get it from user
	 */
	private function copyFrom(Comment $comment){
		$this->parent=$comment->parent;
		$this->id=$comment->getId();
		$this->admin=$comment->getAdmin();
		$this->user=$comment->getUser();
		$this->content=$comment->getContent();
		$this->likes=$comment->getLikes();
		$this->childs=$comment->getChilds();
	}


}


 ?>