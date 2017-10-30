 <?php



trait cameraAPIList{ 
      //OCR処理メソッド
      public function returnOcr($lang){
            $file =$_FILES["gazo"];

            if(is_uploaded_file($file['tmp_name'])){ 
               move_uploaded_file($file['tmp_name'], './'.$file['name']);
            }else{
              error_log("アップロードされた画像だと認識されていません！", 0);
            }
            //ファイル名取得
            $file = $file["name"];
            $filesize = filesize($file);
            $filename = $file;
            //画像を編集　exif情報を読み取り、回転を調整する。
            $image = imagecreatefromjpeg($filename);
            list($image_w, $image_h) = getimagesize($filename);
             $imagen = exif_read_data($filename,"EXIF");
                if(!empty($imagen['Orientation'])) {
                  switch($imagen['Orientation']) {                   
                    case 8:
                     list($width,$height)=getimagesize($file);
                        $canvas = imagecreatetruecolor($height,$width);
                        $image = imagerotate($image,90,0);
                        break;
                    case 3:
                     list($width,$height)=getimagesize($file);
                          $canvas = imagecreatetruecolor($width,$height);
                        $image = imagerotate($image,180,0);
                        break;
                    case 6:
                           list($width,$height)=getimagesize($file);
                          $canvas = imagecreatetruecolor($height,$width);
                        $image = imagerotate($image,-90,0);

                        break;
                  }
                }

            //画像を配置する。
            imagejpeg($image,           
                      "./output3.jpg",    
                      100               
                     );
           
            imagedestroy($image);
            $text="";
            $moji = date("F j, Y, g:i a");
            $tessCommand = '/usr/local/bin/tesseract output3.jpg image8 -l '.$lang.' -psm 1';
           // $tessCommand = '/usr/local/bin/tesseract output3.jpg image8 -l '.$lang.' -psm 1 alpha.txt';
            exec($tessCommand,$output,$retVal);
            $ocrfile = "image8.txt";
            copy($ocrfile,"./imageText/".$moji.".txt");
            $ocrfile = "./imageText/".$moji.".txt";
            $filetext = fopen($ocrfile,"rt");
            if(fgets($filetext)=="" or filesize($ocrfile) < 10){
                 print("please take a picture again");
            }

              $beforeLine=2;

               while(($line = fgets($filetext))!==false){
                 $WordAry = explode(" ",$line);
                 if($beforeLine>1){
                    $line = preg_replace("/\\n/", " ", $line);
                    $text.=trim($line,"\t\n\r\0\x0B");
                 }else{
                    $text.="</br>";
                   //$text.="!!!!!!!!";
                     $line = preg_replace("/\\n/", " ", $line);
                    $text.=trim($line,"\t\n\r\0\x0B");
                 }
                 $beforeLine = count($WordAry);
               }
            //$orgText = preg_replace("/(){2,}/","<",$text);
            $orgText  = "\n\n\n\n\n\n\n\n\n\n\n".preg_replace("/(\<\/br\>)+/","\n\n",$text);

            $content="<!DOCTYPE HTML><html><head><meta charset='utf-8'><style type='text/css'>a{text-decoration: none;color:#000000} #card{background-color:#FFFFFF} #caard{box-shadow:2px 2px 2px rgba(0,0,0,0.6);background-color:#FFFFFF}   #aw{text-decoration: none;color:#FF33FF}</style><script type='text/javacript' src='main.js'></script></head>";
            $content.="<body style=\"font-family: \"HelveticaNeue-Bold\", \"Helvetica Neue Bold\",\"Helvetica Neue\", 'ãƒ’ãƒ©ã‚®ãƒŽè§’ã‚´ Pro W6','Hiragino Kaku Gothic Pro'\">";
            $lines = $this->splitText($text);
            foreach($lines as $line){
                     $content.="<div id='card'>";
                       $nowText="";
                        $eWord = call_user_func(function ($v,$cont) {
                            if($v=="chi_sim"){
                                return preg_split("//u", $cont, -1, PREG_SPLIT_NO_EMPTY);
                            }else{
                                return preg_split("/\s/",$cont);  
                            }
                        }, $lang,$line);
                       //preg_split("/\s/",$line);
                       foreach($eWord as $word){
                          $content.="<a href='native://requestGo/{$word}' >{$word}</a> ";
                          $nowText.=$word;
                          $nowText.=" ";     
                       }
                       //$content.=".";
                  
                      if(count($lines)>1 and preg_match("/[a-z]+/",preg_replace("/br/","",$line)) and strlen($line)>10){
                         //$content.=".";
                         $nowText.=".";
                      }
                 // $content.=count($lines);
                 //  $content.=strlen($nowText)."\n";
                      $nowText = preg_replace("/\<\/br\>/","",$nowText);
                      if(strlen($nowText)>30 and strlen($nowText)<300){
                        $content.="<div style='text-align:right'><a href='native://requestText/{$nowText}' id='aw'>翻訳</a></div>";
                      }else{
                        if(strlen($nowText)>10){
                       //$content.="</br>";
                        }
                      }
                    $content.="</div>";
                 }
                # $content.="</div></br>";

              $content.="</body>";
              $contAry = array("lang"=>$lang,"content"=>$content,"org"=>$orgText,"score"=>$score);
               //print($content);
              //print(json_encode($contAry));
              return json_encode($contAry);
     }


     private function splitText($text){
       $text = preg_replace("/\.\.+/",".",$text);
       $textAry = array();
       $textB  = array();
       $textSets = preg_split("/(\.|\?|\;)/",$text,-1,PREG_SPLIT_DELIM_CAPTURE);
       $resultText = "";
       $resuTAry = array();
       $tmp2 = array();

       $i = 0;
       foreach($textSets as $texts){
          if($i%2==0){
            $tmpAry[$i] = $texts;
          }else{
             $tmpAry[$i-1] .= $texts;
          }
          $i+=1;
       }


       foreach($tmpAry as $Etext){

     //!preg_match("((?<!(Web|Inc|Ltd|Feb|Mrs|Ltd|Mar|Apr|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\.",$Etext)
         if(preg_match("/((?<!(Mr|Ms|Co))\.|\?|\;)/",$Etext) and preg_match("/((?<!(lnc|Web|Inc|Ltd|Feb|Mrs|Ltd|Mar|Apr|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\.|\?|\;)/",$Etext) and strlen($Etext) > 10){
           //array_push($textAry,$Etext);
           $resultText .= $Etext;
           $resultText.="!?!?!?";
         }else{
           $resultText .= $Etext;
         }

       }
       $resultTexts = explode("!?!?!?",$resultText);
       return $resultTexts;
     }

    public function getGlosebeAPI($word,$lang){

        $url = "https://glosbe.com/gapi/translate?from=".$lang."&dest=ja&format=json&phrase=".urlencode($word)."&pretty=true";
        $result = file_get_contents($url);
        //var_dump($result);

        $json = json_decode($result);
        $jsonRes = get_object_vars($json)["tuc"];
        if(count($jsonRes)>0){
            $res = get_object_vars($jsonRes[0]);
            $mean = get_object_vars($res["phrase"])["text"];
            $texts = $this->getGlosebeText($word,$lang);
            $texts["mean"] = $mean;
            return json_encode($texts);
            //echo get_object_vars($res["meanings"][0])["text"];
        }else{
             $YOURCLIENTID='wadakun';
            $YOURCLIENTSECRET='WnrVMVJeQHi4mQ+V78m2DKlQSGT5V5AGdRiFU5oD8w8=';
            $bing = new Bing($YOURCLIENTID, $YOURCLIENTSECRET, "http://api.microsofttranslator.com", "client_credentials");
                    //ぶっちゃけこのメソッドで全部終わらせてもいいのだけれど、あとで使うかもとか思ったんで独立。
            $token = $bing->bingOAuth();
            $res = $bing->BingTranslator($word, "ja", $lang, $token);
            $res = preg_replace("/\<[^\<]+\>/","",$res);
            $json = json_encode(array("text"=>"","trans"=>"","mean"=>$res));
            return $json;
        }
     }


    private function getGlosebeText($text,$lang){
       $url = "https://ja.glosbe.com/".$lang."/ja/".$text;
       $result = file_get_html($url);
       $ret = $result->find("div[class=tableRow row-fluid]");
       $st = str_get_html($ret[0]);
       $textSets = preg_split("/\<\/sup\>/",$st);
       $exampleText = $textSets[1];
       $exampleText = preg_replace("/\<[^\<]+\>/", "", $exampleText);
       $exampleText = preg_replace("/tatoebaja\s+$/", "", $exampleText);
       $exampleText = preg_replace("/opensubtitles2ja/", "", $exampleText);
       $exampleJapanese = $textSets[2];
       $exampleJapanese = preg_replace("/\<[^\<]+\>/", "", $exampleJapanese);
       return array("text"=>$exampleText,"trans"=>$exampleJapanese);
    }

    //文書生成
    public function TextGet($text,$lang){
        	$key= preg_replace("/\&lt\;br\&gt\;/", " ", $text);
        	$key=preg_replace("/(\&lt\;|\&gt\;)/","",$key);
        	$key=preg_replace("/\&quot\;/","",$key);
        	$text=preg_replace("/\,/","",$key);    
        	$textCount = preg_split("/\s+/",$text);
        	if(count($textCount) < 100){
        		$bing = new Bing(self::YOURCLIENTID, self::YOURCLIENTSECRET, "http://api.microsofttranslator.com", "client_credentials");
        	//ぶっちゃけこのメソッドで全部終わらせてもいいのだけれど、あとで使うかもとか思ったんで独立。
        		$token = $bing->bingOAuth();
        		header('content-type','application/x-www-form-urlencoded');
        		header('Access-Control-Allow-Origin: *');
        		$res = $bing->BingTranslator($text, "ja", $lang, $token);
        		$res = preg_replace("/\<[^\>]+\>/","",$res);

            return $res;
        	}else{
        		$key = preg_replace("/\s+/","+",$text);
        		$Apkey = "xxxxxx";
        		$req  =  "https://www.googleapis.com/language/translate/v2?key=${Apkey}&source=".$lang."&target=ja&q=${key}";
        		$res = file_get_contents($req);
        		$res = json_decode($res);
        		$wdata = get_object_vars(get_object_vars(get_object_vars($res)["data"])["translations"][0])["translatedText"];
            return $wdata;
        	}

    }

}
  
?>