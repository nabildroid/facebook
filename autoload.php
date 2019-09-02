<?php 
spl_autoload_register(function ($name){
	$name = str_replace("\\", DIRECTORY_SEPARATOR, $name);

	$PATH=__DIR__."/".strtolower($name);
	include_once $PATH.".php";
});

 ?>