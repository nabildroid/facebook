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


## account 
hold all account functionality like profile messages and wall ....
#### initail the account
for that accout must be login to facebook using only real account **cookie(string)**

```php
$user=new Account;
$user->login("cookie");
```
## wall
it's facebook home page (profile wall) 

#### get suggestion posts from wall
```php
$posts=$user->posts();
```
return array of `Post` but each Post must force fetched for getting full capabelity
```php
$posts[0]->fetch(1);
```
by default when getting any propreity if it exist Post will return it or will force himself to fetch entiry 
Post information **Post Page** *when click to see full Post*
