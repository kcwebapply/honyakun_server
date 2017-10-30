<?php
class Axial_UrlInterpreter
{
 
        function Axial_UrlInterpreter(){
                $requestURI = explode('/', $_SERVER['REQUEST_URI']);
                $scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
                for($i= 0;$i < sizeof($scriptName);$i++){
                        if ($requestURI[$i] == $scriptName[$i]){
                            unset($requestURI[$i]);
                        }
                }
                $commandArray = array_values($requestURI);
                $commandName = $commandArray[0];
		        $parameters = array_slice($commandArray,1);
                $this->command = new Axial_Command($commandArray[0],$parameters);
        }
       
        function getCommand(){
                return $this->command;
        }
}
?>
