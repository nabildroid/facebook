### Facebook Api through WebScarping Technologie
## works only in mbasic.facebook.com
## works only in english version of facebook
----------
----------

## common classes functionality

#### id
```php
public $id;
```
it's the id of class could be a **intiger** or **string**
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
