<?php
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