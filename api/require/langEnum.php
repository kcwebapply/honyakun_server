<?php


trait langEnum{

	function returnTesLang($lang){
		switch($lang){
			case "en":
				return "eng";
			case "zh-CHS":
				return "chi_sim";
			case "es":
				return "spa";
			case "fr":
				return "fra";
			case "ko":
				return "kor";
			case "de":
				return "deu";
			case "ru":
				return "rus";
			case "it":
				return "ita";
		}
	}


}

?>
