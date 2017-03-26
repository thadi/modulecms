<?php

spl_autoload_register(function ($className) {
	$module = preg_replace('/Module$/', '', $className);
    include(__DIR__ . "/modules/" . $module . "/" . ucfirst($className) . ".php");
});

include("php.fn.php");
include("System.php");

System::set(new System());
System::get()->updateModules();

include("Modules.php");

System::get()->setModulesObj(new Modules());

$path = explode("/" , System::get()->getModule("request")->getPath());
$path = array_merge(array(), array_filter($path, function($comp){ return !empty($comp); }));

$response = System::get()->getModule("request")->handleRequest($path);

if(is_array($response)){
	$response = json_encode($response);
}

echo $response;

?>