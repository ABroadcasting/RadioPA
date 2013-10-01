<?php
# Radio Panel Alpha - is an OpenSource Radio Panel.
# Radio Panel Alpha will be base part of the complete Radio Streaming Administration tool (Open Radio Control Panel)
#
# Copyright (C) 2010-2013 by James Heinrich - http://www.getid3.org
# Copyright (C) 2010-2013 by Max S Alyohin - http://radiocms.ru
# Copyright (C) 2013 by OpenRCP - http://open-rcp.ru
#
#
# The contents of this file are subject to the Mozilla Public License
# Version 1.1 (the "License"); you may not use this file except in
# compliance with the License. You may obtain a copy of the License at
# http://www.mozilla.org/MPL/
#
# Software distributed under the License is distributed on an "AS IS"
# basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
# License for the specific language governing rights and limitations
# under the License.
#
# The Original Code is "RadioCMS".
#
# The Initial Developer of the Original Code is Max S Alyohin.
# Portions created by Initial Developer are Copyright (C) 2010-2013
# by Max S Alyohin. All Rights Reserved.
#
# Product contains getID3 project code are Copyright (C) 2010-2013 by
# James Heinrich. All Rights Reserved.
#
# Portions created by the OpenRCP Development Team (C) 2013 by
# Open Radio Control Panel. All Rights Reserved.
#
# The OpenRCP Home Page is:
#
#    http://open-rcp.ru
#
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