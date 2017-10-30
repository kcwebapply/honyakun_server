<?php
spl_autoload_register(function($className){
	require($className.".php");
});
header('Access-Control-Allow-Origin: *');
$req = $_SERVER["REQUEST_URI"];
$urlInterpreter = new Axial_UrlInterpreter();
$command = $urlInterpreter->getCommand();
$commandDispatcher = new camera_API_Dealer($command);
echo $commandDispatcher->Dispatch();
?>
