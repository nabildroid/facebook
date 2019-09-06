### Facebook Api through WebScarping Technologie
## works only in mbasic.facebook.com
## works only in english version of facebook
----------
----------

# common classes functionality
### variables

#### id
```php
public $id;
```
it's the id of class could be a **integer** or **string**
for example `id` of `Post` or `Page`, `Group`, `Profile`...


#### name
```php
public $name;
```
the name of current selected componet it could be name of user `Profile->name` or name of group/page
sometimes only force fetch allow to get name propeity 


#### admin
```php
public $admin;
```
means of such component is create by this account or not
like `Post` or `Comment`
and in `Group` it tell the current memberchep state

#### content
```php
public $content;
```
is the content of `Post` or `Comment` and used also in some other componet 
propriety like `Profile->bio`
**it's array** that contain **parsed html** so it like a **json** that map to certin 
html tags  

#### likes
```php
public $likes=[
	"length"=>0,
	"users"=>[],
	"mine"=>0,
	"url"=>"",
	"like"=>""
];
```
associative array contain information about compoent likes *info about likes submitted by other users*

- **length** number of likes that component got
- **users**  array of `Profile` that like such component 
- **mine**   does such account already like such component or not
- **url**    url of page that contain all users who likes, **url generate** `$likes->users`
- **like**   is the action, url allows account to like some compenet 

#### childs
```php 
public $childs=[  
	"items"=>[],
	"next_page"=>null,
	"next_page_indicator"=>"",
	"add"=>""
];
```
each componet may has children for example `Post` has `Comment` as childs and `Comment` may has
other children of type `Comment` in this case the children are **replies**
>this variable prevent fetching same children multipel times

- **items**      array contain multi arrays of **children** compoent array for each page
- **next_page**  url lead to next page of childrens
- **next_page_indicator** the first pagination caption lead to next page to prevent loop of click to next then previous then back to next 
- **add**        html of `form` responsible for publish new child 

### functions
#### fetch
```php
public function fetch($force=0)
```
send http request to compoenent id then parse the response
`$force` sometimes the response of fetch will be fixes using `fixHttpResponse` 
so may be such **response is not enough to get some propiety** or 
the current response is not updated and even calling `fetch(0)` will not
effect anythings due to prevent multi request that has same response
so by forcing `fetch(1)` it will request the content of compenent form
his origin id and update all his propriety 

#### IdfromUrl
```php
static function IdFromUrl($url)
```
each component knows which part of `$url` represent his id
so it parse **string** `$url` and returns either **integer** or **string**



# general classes that depend on component classes

## common

it **abstract class** contain base functions like `http` and getting functions
and common variable between all components 

### common variables
----
#### parent
```php
public $parent=null;
```
is the parent that generate such componet it's necissary argument in all compoents
when inisiatie them because it lead to `root`

#### root
```php
public $root=null;
```
it's always `Account` **class**
this vraible takes his value when `Common` `construct` invoked
from the **parent class**

#### html
```php
public $html="";
```
contain the response of `http` function

#### lastHttpRequest
```php
public $lastHttpRequest=null;
```
carry the response and arguments of last `http` request in array
so when `http` invoke it assign like that
```php
$this->lastHttpRequest=[$url,$data,$headers,$responseHeader]
```

#### fetched
```php
protected $fetched=0;
```
boolean varibale for prevent more then one fetch

### common functions
------------------
#### http
```php
public function http($url="",$data="",$headers=[],$responseHeader=0)	
```
the core of our project ;)
it's allow each component to make http request and the corespond response
will be in `$html` common varaible
##### arguments
- **url** the url of such http request 
- **data** is **associative array** that hold all **HTTP POST REQUEST** name/value pair
- **headers** is **associative array** that hold all Headers of such request
- **responseHeader** boolean allows to get beside the HTTP response content an **Http response headers** 
if it's true `$html` will be array of pattren 
	`{content:"html string",headers:"array of name/values pair"}`

#### fixHttpResponse
```php
	public function fixHttpResponse($response,$url="",$data="",$headers=[],$responseHeader=0)
```
prefixe the desire response that expected from `http`
say for example **we already have the html** of `Post` why we would make **new http**
that will return **same Html** as we had
so `fixHttpResponse` freeze `http` and make it **return desired response**
##### arguments
- **response** desired response that we need `http` to return

other arguments it's same as arguments of `html` and **must be the same** to make `html`
returns desired response

#### submit_form
```php
protected function submit_form($html,$url,$values=[],$target_submit="",$forceInput="")
```
it allow any component to submit any kind of forms `multipart/form-data` or `application/x-www-form-urlencoded` and so on

##### arguments

- **html** content of form that has all input 
- **url** the action of form
- **values** array of values that each input will take **in order**
- **target_submit** the name of submit input that will trigger the submit action of such form
- **forceInput** associative array ,it's extra key/value pair will added to values 

>in case of submit `multipart/form-data` **images is only type accept** and 
the **his value must be url**, means when trying to submit form contain images
the action will be `submit_form($html,$url,['url of image1','url of img2','some text value'])`

#### getters
`public function getParent()`
`public function getId($int=0)`
`public function getUser()`

only for `Post` `Comment`
`public function getContent()`

`public function getLikes($prop="")`
`public function getChilds($prop="")`

only for `Post`
`public function getSource($prop="")`

only for **image** `Post` or `Profile` 
`public function getPicture($prop="")`

only for `Profile`
`public function getBio()`


`public function getAdmin()`
`public function getName($force=0)`



## account 
hold all account functionality like profile messages and wall ....
#### initail the account
for that accout must be logined to facebook using only real account **cookie(string)**

```php
$user=new Account;
$user->login("cookie");
```
## notifications
setting function that will trigger when new notification/message arrives

### message 
set trigger
```php
$user->notification->setMessageTrigger($fnc);
```
the argument must be valid function that takes one argument

the trigger will invoked whenever new message arrive
and will pass a **the id of sender** to trigger

>**Note**: messages stay arrive until the content of message readed then will desippear


### notifications
set trigger
```php
$user->notification->setNotificationTrigger($fnc);
```
the argument must be valid function that takes one argument

the trigger will invoke whenever new notification arrive
and will pass a array as paramater to trigger
such array contain `type`, `url`, `snippet`

- **type** is integer tells which type is this noti
- **url** when the new notification lead to 
- **snippet** what the notification tell (caption) **Html**

possible `type`
1. some user **publish** new post or photo
2. some user **like/react** to such account post
3. some user **comment** on such account post or has been replied
4. some user **like/react** to such account **comment**
5. acount published **post** has been **approved**
6. request to **join group** has been **approved**


>**Note**: notification disappear once it readed by the trigger


## wall
it's facebook home page (profile wall)
accissing wall with
```php
$user->wall;
```

#### get suggestion posts from wall
```php
$posts=$user->wall->posts();
```
return array of `Post` but each Post must force fetched for getting full capabelity
```php
$posts[0]->fetch(1);
```
by default when getting any propreity if it exist Post will return it or will force himself to fetch entiry `Post` information **Post Page** *when click to see full Post*

#### publish new post 
publish new post in account wall
```php
$user->wall->publish($param);
```
`$param` it's associative array could takes muti arguments or only one

all parameter acceptable
```
"text"=>"",
"images"=>[],
"privacy"=>"",
"tags"=>[]
```

- **text** the content of new post **text only**
- **images** array of **max 3 url** of picture
- **privacy** the scope of audiece that such post target 
- **tags** array of profiles that will tagged to such post

>`privacy` could only be **public** or **only me** or **friends**


> `publish`  returns such new `Post` object 


## profile
profile it handle all profile actions like setting/getting  and response to request
get posts and so on

there's two type of profile 
	1. account profile is unique and always related with `root`
	2. user profile initial with user id

account profile
```php
$user->profile
```

user profile
```php
$someone=$new Profile($parent,$user_id)
```

`$user_id` must be either **integer** or **string** which lead to user profile

in some cases the id must be integer for that in order to convert string
id to integer, we should force fetching the `Profile` so that garanti 
that the id is integer
```php
$someone->fetch(1);
```

### common functionality between account profile and user profile

#### posts
get posts 
```php
$profile->posts($page)
```
return array of `Post`


#### profile picture\cover
```php
$profile->getPicture("profile");
$profile->getPicture("cover");
```
return `Post`


#### profile bio *descrition*
```php
$profile->getBio();
```
return **array of parsed html**


#### friends
get all friends to such profile
```php
$profile->friends();
```
return array of `Profile`


### account profile unique functionality

#### set profile picture
to set profile picture using **url of image**
```php
$user->profile->setProfilePicture($url);
```

#### set profile cover picture
to set profile cover picture using **url of image**
```php
$user->profile->setCoverPicture($url);
```

### set bio
set profile bio must be **plain text**
```php
$user->profile->setBio($txt);
```

### pending friend requests
get array of `Profile` who send friend request to account
```php
$user->profile->pendingRequests()
```
return array of `Profile`

### users profile functionality

>to send/react with friend request to profile we need to do so from his profile

#### send friend request
```php
$someone->sendFriendRequest();
```
returns boolean

#### accept the friend request
if `$someone` sent request to `Account` we can accept it
```php
$someone->confirmUserRequest()
```
returns boolean


#### reject friend request
if `$someone` sent request to `Account` we can reject it
```php
$someone->rejectUserRequest()
```
returns boolean


### post
hold all post functionality such as like and get content and publish comment

there's two type of posts image `Post` and normal `Post` each has same functionality exept for getPicture function in image `Post`

`Post` could be full fetched which means no need to fetched any more, or half fetched
so it have only the first part of content when content is large, no information about childrens including add new child

>**Note** all functions that return array of posts actuely half fetched posted
and if full fetched needed,for that force fetched required 

#### getters

##### user
the post author
```php
$post->getUser();
```
return either `Profile` or null when such `Post` has been published by `Page`

##### admin
check if post has been writen by `Account` or by other `Profile`
```php
$post->getAdmin();
```
return boolean

##### content
get content of `Post` as **parsed html type**
```php
$post->getContent();
```
return array of parsed html

>**Note**: if post is half fetched the content will messing half part and some time the most parts so it better when full content needed to use force fetching the `Post` or use use `fullcontent` which automatcally force fetching the `Post` if that required

##### get fullcontent
get full content by request single `Post` by it `id` whenever content is large
to it check if content contain `More` keyword which indicate that `Post` contain
only first part of full content
```php
$post->fullcontent();
```
return array of parsed html

>**Note**: fullcontent will check the existence of `More` keyword before trying to 
force fetching the entire `Post` by it `id`

##### picture
works only with image `Post` type
```php
$post->getPicture();
```
return either null or link to `View full size` of image 

##### source
each post has caples of source **origin-post**, **page**, **group**

###### origin-post
if post share other post so the last will be the **origin-post**
```php
$post->getSource("origin");
```
return either null or `Post`

>**Note**: the content of **origin-post** exist in parsed html content of **parent** `Post` and it type is `'post'`

###### page
if `Post` was published by `Page` or shared from `Page`
```php
$post->getSource("page");
```
return either null or `Page`

>**Note**: if `Post` was not shared from `Page` to `Profile` or to `Group` that means
there's no author,so `$post->getUser()` return null

###### group
if `Post` published in group
```php
$post->getSource("group");
```
return either null or `Group`

##### comments
get comments of `Post`
comments section of `Post` works by pagination so in order to get all comments
that required to get to each page of comments section
```php
$post->comments($page);
```
return array of `Comment`

`$page` argument is **integer** and indicate which page of comments will extract comments from it 

>**Note**: the default value of `$page` is `0` which means the first page

##### uses who likes post
```php
$post->wholikes();
```
return array of **all** `Profile` who likes such `Post`

>**Note**: dont used when the post has thousen of likes

##### check if `Account` have been like the post
```php
$post->getLikes("mine"); 
```
return boolean indicate whether the post has been laked by `Account` or note

#### actions

##### like
in order to like unliked post
```php
$post->like();
```
return boolean

##### unlike 
```php
$post->unlike();
```

##### comment
to publish new `Comment` to such `Post`
```php
$post->comment($txt);
```
return such new `Comment`

`$txt` must be **plain text**
