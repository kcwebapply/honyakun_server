<?php

require_once("require/Bing.php");
require_once("require/simple_html_dom.php");
require_once("Axial_Command_Dispatcher.php");
require_once("tesLib.php");
require_once("camera_API_Functions.php");
require_once("require/langEnum.php");
header('Access-Control-Allow-Origin: *');

class camera_API_Dealer extends Axial_Command_Dispatcher{
     //トレイトしたこのcameraAPIListのメソッドを読んでいる。
     use cameraAPIList;
     use langEnum;
     const YOURCLIENTID='wadakun';
     const YOURCLIENTSECRET='xxxxxx';
     public function Dispatch(){
                switch ($this->command->getCommandName()){
                        case 'TextTranslate' :
                              $paras = preg_replace("/^parameters\?/","",$this->command->parameters[0]);
                              $eachparas = explode("&",$paras);
                          	  $parasDic = array();

                              foreach($eachparas as $kv){
					                         $kvset = explode("=",$kv);
					                         $parasDic[$kvset[0]] = $kvset[1];
				                       }
                              $text = $parasDic["text"];
                              $lang = $parasDic["lang"];
                              $result = $this->TextGet(urldecode($text),$lang);
                              
                              break;
                        case 'WordGet' :
                              $paras = preg_replace("/^parameters\?/","",$this->command->parameters[0]);
                              $eachparas = explode("&",$paras);
                              $parasDic = array();
                              foreach($eachparas as $kv){
					                       $kvset = explode("=",$kv);
					                       $parasDic[$kvset[0]] = $kvset[1];
				                      }
                              $word = $parasDic["word"];
                              $lang = $parasDic["lang"];
                              $result = $this->getGlosebeAPI(urldecode($word),$lang);

                              break;
                        case "ocr":
                              $paras = preg_replace("/^parameters\?/","",$this->command->parameters[0]);
                              $eachparas = explode("&",$paras);
                              $parasDic = array();
                              foreach($eachparas as $kv){
                                $kvset = explode("=",$kv);
                                $parasDic[$kvset[0]] = $kvset[1];
                              }
                              $lang = $parasDic["lang"];
                              $lang = $this->returnTesLang($lang);
                        		  $result = $this->returnOcr($lang);
                              break;
                        default:
                                break;
                }
              return $result;
     }


    

}


?>