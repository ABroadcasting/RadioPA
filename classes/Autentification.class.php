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
	class Autentification {
	    
        public static $object;
        public $hashLength = 15;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
		private function __construct() {			 $this->dateTime = Date::create();
			 $this->db = MySql::create();
			 $this->request = Request::create();		}

		public function handler() {
			$this->deleteOldAuth();			if ($this->request->hasGetVar('exit')) {				$this->logout();			}
			if (
				$this->request->hasPostVar('user') and
				$this->request->hasPostVar('password')
			) {
                $this->login();
			}
			$this->securityRun();		}

		public function logout() {			if ($this->request->hasCookieVar('hash')) {
	    		$use_hash = $this->request->getCookieVar('hash');
	    		$query = " DELETE FROM `login` WHERE `hash` = '$use_hash' ";
    			$this->db->queryNull($query);
    		}

    		Header("Location: /radio/");		}

		public function getUser() {			if (!$this->request->hasCookieVar('hash')) {				return false;
			}

			$query = "SELECT * FROM `login`";
			$lines = $this->db->getLines($query);

			if (!empty($lines)) {
				foreach ($lines as $line) {
    				if ($line['hash'] == $this->request->getCookieVar('hash')) {
    					$user = $line;
					}
				}
			}

			if (empty($user)) {				return false;			}

            $this->updateLoginEntry($user['hash']);
            $this->setCookieVar($user['hash']);

			return $user;		}

		public function securityRun() {  			if (
				$this->request->hasPostVar('user') and
				$this->request->hasPostVar('password')
			) {
				$query = "SELECT * FROM `login` WHERE `ip` = '".$this->request->getIp()."'";
				$line = $this->db->getLine($query);
				if ($line['raz'] >= 5) {
  					echo "<br><center style=\"font-family: Arial, Helvetica, sans-serif; font-size: 15px;\">Слишком много попыток, попробуйте через 15 минут.</center>";
  					exit;
				}

				if ($line) {
					$query = " UPDATE `login` SET `raz` = `raz`+1 WHERE `ip` = '".$this->request->getIp()."'  ";
					$this->db->queryNull($query);
				} else {
					$query="INSERT INTO `login` ( `ip` , `raz` ,`time` ) VALUES ('".$this->request->getIp()."','1','".$this->dateTime->getAuthTime()."')";
					$this->db->queryNull($query);
				}
			}		}

		public function login() {            foreach ($this->getAllUsers() as $i=>$user) {
        		if ($user['name'] == $this->request->getPostVar('user') and $user['password'] == $this->request->getPostVar('password')) {
        			$hash = $this->generateHash();
        			$this->insertLoginEntry($user, $hash);
                    $this->setCookieAndGoToPanel($hash);
                    exit;
        		}
			}		}

		public function setCookieVar($hash) {
?>
			<script>
				set_cookie('hash', '<?=$hash?>', 1750);

				function set_cookie(name, value, expires) {
					date = new Date();
					date.setSeconds(date.getSeconds() + expires);
					document.cookie = name + "=" + escape(value) + "; expires=" + date.toGMTString() +  "; path=/";
				}
			</script>
<?php
		}

		public function setCookieAndGoToPanel($hash) {?>
			<script>
				set_cookie('hash', '<?=$hash?>', 1750);
				locationHref('/radio');

				function set_cookie(name, value, expires) {
					date = new Date();
					date.setSeconds(date.getSeconds() + expires);
					document.cookie = name + "=" + escape(value) + "; expires=" + date.toGMTString() +  "; path=/";
				}

				function locationHref(url) {
					document.location.href = url;
				}
			</script>
<?php		}

		public function updateLoginEntry($hash) {			$query = "UPDATE `login` SET `time` = '".$this->dateTime->getAuthTime()."' WHERE `hash` = '$hash'";
			$this->db->queryNull($query);		}

		public function insertLoginEntry($user, $hash) {
			$query = "INSERT INTO `login` ( `ip` , `dj` , `raz` ,`time` , `hash`, `admin` )
				VALUES ('".$this->request->getIp()."','".$user['name']."','0','".$this->dateTime->getAuthTime()."','$hash','".$user['admin']."')";
			$this->db->queryNull($query);		}

		public function getAllUsers() {
			$userArr[0]['name'] = USER;
			$userArr[0]['password'] = PASSWORD;
			$userArr[0]['admin'] = 1;

			$query = "SELECT * FROM `dj`";
			$lines = $this->db->getLines($query);

			$x = 1;
			foreach ($lines as $line) {
   				$userArr[$x]['name'] = $line['dj'];
   				$userArr[$x]['password'] = $line['password'];
   				$userArr[$x]['admin'] = $line['admin'];
   				$x++;
			}

			return $userArr;		}
		public function generateHash() {			$num = range(0, 9);
   			$alf = range('a', 'z');
        	$_alf = range('A', 'Z');
        	$symbols = array_merge($num, $alf, $_alf);
        	shuffle($symbols);
        	$code_array = array_slice($symbols, 0, (int)$this->hashLength);
        	$code = implode("", $code_array);
      		return $code;		}

		public function deleteOldAuth() {			$query = "DELETE FROM `login` WHERE `time` < ".$this->dateTime->getNow();
    		$this->db->queryNull($query);		}	}



?>