<?php
	class Filter {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
        private function __construct() {
            $this->ssh = Ssh::create();
        }
        
		public function apply($text) {
			$text = trim($text);
			return $text;		}

		public function toRequestString($value) {        	return str_replace(" ", "%20", $value);		}

		public function wordWrap($text, $wrap=30) {
		    $arr = explode(" ", $text);
            foreach ($arr as $index=>$word) {
                $strlen = (int) iconv_strlen($word, 'utf-8');
                if ($strlen > 25) {
                    $offset = (int) floor($strlen/2);
                    $arr[$index] = iconv_substr($word, 0, $offset, 'utf-8')."\n".iconv_substr($word, $offset, $strlen, 'utf-8');
                }     
            }
            			return implode(" ", $arr);		}
        
        public function tagForId3v1($tag) {
            $tag = str_replace(" - ", "- ", $tag);
            
            if (strlen($tag) > 29) {
                $tag = substr($tag, 0 , 29);
            }
            
            $tag = @iconv('utf-8', 'utf-8', $tag);
            $tag = trim($tag);

            return $tag;
        }

		public function cleanTag($tag) {		                
            $tag = str_replace(" - ", "- ", $tag);
            
            if (Ssh::create()->checkEzstreamCompatibility()) {
                if (iconv_strlen($tag, 'utf-8') > 90) {
                    $tag = iconv_substr($tag, 0, 90, 'utf-8');
                }
            } else {
                if (strlen($tag) > 29) {
                    $tag = substr($tag, 0 , 29);
                } 
            }    
        
			$tag = @iconv('utf-8', 'utf-8', $tag);
		    $tag = trim($tag);

			return $tag;
		}

		public function cleanFileName($file_name) {
			$file_name_tmp = $file_name;

			if (strpos($file_name, '/') !== false) {
				$explode_char = '/';
			}
			else {
				$explode_char = '\\';
			}

			$file_name_array = explode($explode_char, $file_name);

			$path_name = "";

			for ($i=0; $i<sizeof($file_name_array)-1; $i++) {
				$path_name .= $file_name_array[$i].$explode_char;
			}

			$file_name = $file_name_array[count($file_name_array) - 1];

			$file_name = str_replace(".mp3", "", $file_name);
			$file_name = str_replace(".MP3", "", $file_name);

		    $file_name = str_ireplace("'", "", $file_name);
		    $file_name = str_ireplace("&", "and", $file_name);
		    $file_name = str_ireplace("\"", "", $file_name);
			$file_name = str_ireplace("   ", " ", $file_name);
			$file_name = str_ireplace("  ", " ", $file_name);

			if (TRANSLIT == "on") {
				$file_name = $this->translit($file_name);
			}
			
			$file_name = @iconv('utf-8', 'utf-8', $file_name);
			
			$file_name = trim($file_name);
			
			if (empty($file_name)) {
				$file_name = "empty-".rand(1, 99999);
			}
	
			$file_name = $path_name.$file_name.".mp3";

			return $file_name;		}

		public function translit($text) {
        	$text = str_replace("а", "a", $text);
			$text = str_replace("б", "b", $text);
			$text = str_replace("в", "v", $text);
			$text = str_replace("г", "g", $text);
			$text = str_replace("д", "d", $text);
			$text = str_replace("е", "e", $text);
			$text = str_replace("ё", "yo", $text);
			$text = str_replace("ж", "zh", $text);
			$text = str_replace("з", "z", $text);
			$text = str_replace("и", "i", $text);
			$text = str_replace("ы", "i", $text);
			$text = str_replace("й", "y", $text);
			$text = str_replace("к", "k", $text);
			$text = str_replace("л", "l", $text);
			$text = str_replace("м", "m", $text);
			$text = str_replace("н", "n", $text);
			$text = str_replace("о", "o", $text);
			$text = str_replace("п", "p", $text);
			$text = str_replace("р", "r", $text);
			$text = str_replace("с", "s", $text);
			$text = str_replace("т", "t", $text);
			$text = str_replace("у", "u", $text);
			$text = str_replace("ф", "f", $text);
			$text = str_replace("х", "h", $text);
			$text = str_replace("ц", "c", $text);
			$text = str_replace("ч", "ch", $text);
			$text = str_replace("ш", "sh", $text);
			$text = str_replace("щ", "sh", $text);
			$text = str_replace("ъ", "", $text);
			$text = str_replace("ь", "", $text);
			$text = str_replace("э", "e", $text);
			$text = str_replace("ю", "yu", $text);
			$text = str_replace("я", "ya", $text);

			$text = str_replace("А", "A", $text);
			$text = str_replace("Б", "B", $text);
			$text = str_replace("В", "V", $text);
			$text = str_replace("Г", "G", $text);
			$text = str_replace("Д", "D", $text);
			$text = str_replace("Е", "E", $text);
			$text = str_replace("Ё", "YO", $text);
			$text = str_replace("Ж", "ZH", $text);
			$text = str_replace("З", "Z", $text);
			$text = str_replace("И", "I", $text);
			$text = str_replace("Ы", "I", $text);
			$text = str_replace("Й", "Y", $text);
			$text = str_replace("К", "K", $text);
			$text = str_replace("Л", "L", $text);
			$text = str_replace("М", "M", $text);
			$text = str_replace("Н", "N", $text);
			$text = str_replace("О", "O", $text);
			$text = str_replace("П", "P", $text);
			$text = str_replace("Р", "R", $text);
			$text = str_replace("С", "S", $text);
			$text = str_replace("Т", "T", $text);
			$text = str_replace("У", "U", $text);
			$text = str_replace("Ф", "F", $text);
			$text = str_replace("Х", "H", $text);
			$text = str_replace("Ц", "C", $text);
			$text = str_replace("Ч", "CH", $text);
			$text = str_replace("Ш", "SH", $text);
			$text = str_replace("Щ", "SH", $text);
			$text = str_replace("Ъ", "", $text);
			$text = str_replace("Ь", "", $text);
			$text = str_replace("Э", "E", $text);
			$text = str_replace("Ю", "YU", $text);
			$text = str_replace("Я", "YA", $text);

			return $text;
		}	}
?>