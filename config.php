<?php 
include __DIR__."/functions/dom.php";
include __DIR__."/functions/http.php";
include __DIR__."/functions/function.php";
spl_autoload_register(function ($name){
	include __DIR__."/oop/".strtolower($name).".php";
});

 ?>