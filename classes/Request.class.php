<?php
	class Request {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
        private function __construct() {
            /* nothing */
        }
        		public function getIp() {			return $_SERVER["REMOTE_ADDR"];		}

		public function get($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, str_replace(" ", "%20", $url));
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;		}

		public function getRadioPath() {  			if (defined('RADIO_PATH')) {  				return RADIO_PATH;  			} else {
  			    if ($this->hasServerVar("SHELL") or $this->hasServerVar("PWD")) {
				    return str_replace("//", "/", dirname($this->getServerVar("SCRIPT_FILENAME"))."/");                } else {
                    return str_replace("//", "/", $this->getServerVar("DOCUMENT_ROOT")."/radio/");
                }
            }		}

		public function getMusicPath() {
  			if (defined('MUSIC_PATH')) {
  				return MUSIC_PATH;
  			} else {
				return str_replace("//", "/", $this->getServerVar("DOCUMENT_ROOT")."/music/");
  			}
		}
        
        public function hasServerVar($var) {
            if (!empty($_SERVER[$var])) {
                return true;
            } else {
                return false;
            }
        }

		public function hasPostVar($var) {			if (!empty($_POST[$var])) {				return true;			} else {				return false;			}		}

		public function hasCookieVar($var) {
			if (!empty($_COOKIE[$var])) {
				return true;
			} else {
				return false;
			}
		}

		public function hasGetVar($var) {
			if (!empty($_GET[$var])) {
				return true;
			} else {
				return false;
			}
		}

		public function getCookieVar($var) {
			return $_COOKIE[$var];
		}

		public function getPostVar($var) {
			return $_POST[$var];
		}

		public function getGetVar($var) {
			return $_GET[$var];
		}

		public function getServerVar($var) {
			return $_SERVER[$var];
		}	}
?>