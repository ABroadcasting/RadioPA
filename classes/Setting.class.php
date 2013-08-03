<?php
	class Setting {
		public $per = "\n";
		public $filename = "_config.php";
        
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }

		private function __construct() {			$this->db = MySql::create();
			$this->request = Request::create();
            $this->filename = $this->request->getRadioPath()."_config.php";
            $this->file = file($this->filename);		}

		public function handler() {			if ($this->request->hasPostVar('main_text')) {
				$this->saveMainText();
			}
			//setting
			if ($this->request->hasPostVar('system_symvol')) {
				$this->saveConfig('SYSTEM_SYMVOL', $this->request->getPostVar('system_symvol'));
			}
            if ($this->request->hasPostVar('system_stream')) {
				$this->saveSetting('stream', $this->request->getPostVar('system_stream'));
			}
			if ($this->request->hasPostVar('net_povtorov')) {
				$this->saveConfig('NO_REPEAT', $this->request->getPostVar('net_povtorov'));
			}
            if ($this->request->hasPostVar('limit_event')) {
				$this->saveConfig('LIMIT_EVENT', $this->request->getPostVar('limit_event'));
			}
			if ($this->request->hasPostVar('limit_zakazov')) {
				$this->saveConfig('LIMIT_ZAKAZOV', $this->request->getPostVar('limit_zakazov'));
			}
			if ($this->request->hasPostVar('translit')) {
				$this->saveConfig('TRANSLIT', $this->request->getPostVar('translit'));
			}
			//setting_system
			if ($this->request->hasPostVar('ip')) {
				$this->saveConfig('IP', $this->request->getPostVar('ip'));
			}
			if ($this->request->hasPostVar('url')) {
				$this->saveConfig('URL', $this->request->getPostVar('url'));
			}
			if ($this->request->hasPostVar('port')) {
				$this->saveConfig('PORT', $this->request->getPostVar('port'));
			}
			if ($this->request->hasPostVar('setting_user')) {
				$this->saveConfig('USER', $this->request->getPostVar('setting_user'));
				$this->updateLogin($this->request->getPostVar('setting_user'));
			}
			if ($this->request->hasPostVar('setting_password')) {
				$this->saveConfig('PASSWORD', $this->request->getPostVar('setting_password'));
			}
			if ($this->request->hasPostVar('cf_icecast')) {
				$this->saveConfig('CF_ICECAST', $this->request->getPostVar('cf_icecast'));
			}
			if ($this->request->hasPostVar('cf_ezstream')) {
				$this->saveConfig('CF_EZSTREAM', $this->request->getPostVar('cf_ezstream'));
			}
			if ($this->request->hasPostVar('playlist')) {
				$this->saveConfig('PLAYLIST', $this->request->getPostVar('playlist'));
			}
			if ($this->request->hasPostVar('temp_upload')) {
				$this->saveConfig('TEMP_UPLOAD', $this->request->getPostVar('temp_upload'));
			}
			//setting_dir
			if ($this->request->hasPostVar('dir_name')) {
				$this->saveConfig('DIR_NAME', $this->request->getPostVar('dir_name'));
			}
			if ($this->request->hasPostVar('dir_url')) {
				$this->saveConfig('DIR_URL', $this->request->getPostVar('dir_url'));
			}
			if ($this->request->hasPostVar('dir_stream')) {
				$this->saveConfig('DIR_STREAM', $this->request->getPostVar('dir_stream'));
			}
			if ($this->request->hasPostVar('dir_description')) {
				$this->saveConfig('DIR_DESCRIPTION', $this->request->getPostVar('dir_description'));
			}
			if ($this->request->hasPostVar('dir_genre')) {
				$this->saveConfig('DIR_GENRE', $this->request->getPostVar('dir_genre'));
			}
			if ($this->request->hasPostVar('dir_show')) {
				$this->saveConfig('DIR_SHOW', $this->request->getPostVar('dir_show'));
			}
			if ($this->request->hasPostVar('dir_bitrate')) {
				$this->saveConfig('DIR_BITRATE', $this->request->getPostVar('dir_bitrate'));
			}		}

		public function updateLogin($login) {
        	$query = "UPDATE `login` SET `dj` = '$login' WHERE `hash` = '".$this->request->getCookieVar('hash')."'";
			$this->db->queryNull($query);		}

		public function getSystemStream() {			$query = "SELECT * FROM  `settings` WHERE `name` = 'stream' LIMIT 1";
            $line = $this->db->getLine($query);
			return $line['value'];		}

		public function saveMainText() {
		    $this->saveSetting('main_text', $this->request->getPostVar('main_text'));		}

		public function getDescription() {			$query = "SELECT * FROM `settings` WHERE `name` = 'main_text' LIMIT 1";
			$line = $this->db->getLine($query);
			return $line['value'];		}

		public function checkNetPovtorov() {			$query = "SELECT id FROM `songlist`";
			if ($this->db->getCountRow($query) <= NO_REPEAT) {
				return 'значение больше чем песен в плейлистах';
			}		}

		public function saveConfig($const, $value) {     			$value = htmlspecialchars($value, ENT_QUOTES, "utf-8");
			for ($i=0; $i<count($this->file); $i++) {
				if (strpos($this->file[$i], "define('$const'")) {
					$this->file[$i] = "\t"."define('$const', '$value');".$this->per;
					$h = fopen($this->filename, 'w+');
					fwrite($h, implode($this->file, ""));
					fclose($h);
				}
			}
		}

		public function saveSetting($name, $value) {			$query = "SELECT * FROM  `settings` WHERE `name`='$name' LIMIT 1";
 			$line = $this->db->getLine($query);
			if (!empty($line)) {
				$query = "UPDATE `settings` SET `value` = '".addslashes($value)."' WHERE `name`= '$name';";
 				 $this->db->queryNull($query);
			} else {
				$query = "INSERT INTO `settings` ( `name` , `value` ) VALUES ('$name', '".addslashes($value)."');";
 				$this->db->queryNull($query);;
			}		}	}
?>