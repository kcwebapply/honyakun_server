<?php
class Axial_Command
        {
        var $commandName = '';
        var $parameters = array();
       
        public function Axial_Command($commandName,$parameters)
                {
                $this->commandName = $commandName;
                $this->parameters = $parameters;
                }
        public function getCommandName()
                {
                return $this->commandName;
                }
        public function getParameters()
                {
                return $this->parameters;
                }
        }


?>
