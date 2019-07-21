<?php 
include "./functions/dom.php";
include "./functions/function.php";
spl_autoload_register(function ($name){
	include "./oop/".strtolower($name).".php";
});

 ?>