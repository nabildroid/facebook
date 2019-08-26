<?php 
trait post_wholikes{
	  //get all who liked this post
		public function wholikes(){
			return self::fetch_wholikes("",$this);
		}

		//get all users who likes this post
		static function fetch_wholikes($url="",$parent){
			//ufi/reaction/profile/browser/?ft_ent_identifier=ID_HERE
				$all_users=[];
			if($url||empty($parent->likes["users"])){
				$next=$url?$url:"/ufi/reaction/profile/browser/?ft_ent_identifier=".$parent->getId();
				do{
					$next=is_array($next)?$next[0][1]["href"]:$next;//first next is string then it array
					$parent->http($next);
					$parent->html=$parent->dom("<ul")[0];
					$users=dom($parent->html,"<a",1);
					//get only the url of users and next page if it available
					$users=filter($users,function($user){
						return strpos($user[0],"<span")===false||strpos($user[0],"See More")!=false;
					})[0];
					//separe between next page and users
					$links=filter($users,function($link){return strpos($link[0],"<span")===false;});
					$users=$links[0];
					$next=$links[1];
					$users=array_map(function($user)use(&$parent){
						$id=Profile::idFromUrl($user[1]["href"]);
						return new Profile($parent,$id);
					},$users);
					$all_users=array_merge($all_users,$users);

				}while(isset($next[0][1]["href"]));
				
				if(!$url)
					$parent->likes["users"]=$all_users;
				return $all_users;
			}
		}
}

 ?>