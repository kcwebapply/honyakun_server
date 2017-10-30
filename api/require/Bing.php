<?php
class Bing{
    var $client_id;
    var $client_secret;
    var $scope;
    var $grant_type;
 
    function __construct($cid,$csec,$scp,$gt){
        $this->client_id = $cid;
        $this->client_secret = $csec;
        $this->scope = $scp;
        $this->grant_type = $gt;
    }
 
    function bingOAuth(){
            $url = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13";
            $arg = array(
                        "client_id" => $this->client_id,
                        "client_secret" => $this->client_secret,
                        "scope" => $this->scope,
                        "grant_type" => $this->grant_type
                    );
            //POSTƒIƒvƒVƒ‡ƒ“
            $options = array('http' => array(
                                    'method' => 'POST',
                                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                                    'content' => http_build_query($arg, "", "&")
                                )
                        );
 
            $token = file_get_contents($url,false,stream_context_create($options));
            $json = json_decode($token);
 
        return $json->access_token;
 
    }
 
    function BingTranslator($text,$to,$from,$token){
        $data = array(
                    "Text" => $text,
                    "To" => $to,
                    "From" => $from
                );
        $url = "http://api.microsofttranslator.com/v2/Http.svc/Translate?".http_build_query($data);
        $options = array('http' => array(
                'method' => 'GET',
                'header' => "Authorization: Bearer ".$token,
               // 'content' => http_build_query($arg, "", "&")
        )
        );
 
        $result = file_get_contents($url,false,stream_context_create($options));
 
        return $result;
 
    }

     function BingImage($word,$token){
        $data = array(
                    "Query" => "'".$word."'",
                    "Market" => "'ja-JP'",
                    "Adult" => "'Strict'",
                    "ImageFilters"=>"'Size:Medium'",
                    "Options"=>"''",
                    "Latitude"=>"''",
                    "Longitude"=>"''"

                );
       // $url = "http://api.microsofttranslator.com/v2/Http.svc/Translate?".http_build_query($data);
       // $url = 'https://api.datamarket.azure.com/Bing/Search/';
        $url = "https://api.datamarket.azure.com/Bing/Search/v1/Image?".http_build_query($data);
        $options = array('http' => array(
                'method' => 'GET',
                'header' => "Authorization: Bearer ".$token,
               // 'content' => http_build_query($arg, "", "&")
        )
        );
 
        $result = file_get_contents($url,false,stream_context_create($options));
 
        return $result;
 
    }
}
?>
