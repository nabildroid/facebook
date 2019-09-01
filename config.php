<?php 
include __DIR__."/functions/dom.php";
include __DIR__."/functions/http.php";
include __DIR__."/functions/function.php";
include __DIR__."/functions/parsecontent.php";
spl_autoload_register(function ($name){

	$origin=$name;
	if(instr($name,"_"))
		$origin=substr($name,0, strpos($name,"_"));

	$PATH=__DIR__."/oop/".strtolower($name);
	if(!file_exists($PATH.".php"))
		$PATH= __DIR__."/oop/".strtolower($origin)."/".strtolower($name);
	include $PATH.".php";


	
});

 ?>