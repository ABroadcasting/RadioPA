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
	class Install {
	    
        public $per = "\n";
	    
        public static function create() {
            return new self();
        }
        
		private function __construct() {
			$this->request = Request::create();
            $this->filename = "../conf/config.php";
            $this->file = file($this->filename);
		}

		public function getWgetCron() {
			return Ssh::create()->getWgetCommand()." http://".$this->request->getServerVar('HTTP_HOST')."/"."event.php -O event.php";
		}

		public function getPhpCron() {
  			$file_adres = "полый/путь/до/php ".$this->request->getServerVar('DOCUMENT_ROOT')."/"."event.php";
			$file_adres = str_replace("//","/",$file_adres);
			return $file_adres;
		}

		public function ifHag5() {
			$user = $this->request->getPostVar('user');
			$password = $this->request->getPostVar('password');

			if (empty($user) or empty($password)) {
				return "<p>Поля не могут быть пустыми</p>";
			}

			$this->saveConfig('USER', $user);
            $this->saveConfig('PASSWORD', $password);

            Header("Location: install.php?hag=6");
		}

		public function ifHag4() {
			$play_list_file = $this->request->getPostVar('playlist');
			$cf_ezstream = $this->request->getPostVar('cf_ezstream');
			$cf_icecast = $this->request->getPostVar('cf_icecast');
			if (empty($play_list_file) or empty($cf_ezstream) or empty($cf_icecast)) {
				return "<p>Заполнены не все поля.</p>";
			}
            
            if (!file_exists($cf_icecast)){
                return"<p>Файл конфигурации icecast не существует.</p>";
            }
            
            if (!file_exists($cf_ezstream)) {
                return "<p>Файл конфигурации ezstream не существует.</p>";
            }
            
			if (!file_exists($play_list_file)) {
				return "<p>Файл плейлиста не существует.</p>";
			}

            $pos_vhoh = strrpos($play_list_file, "/");
            $folder_chmod = substr($play_list_file, 0, $pos_vhoh);
            Ssh::create()->sshExec("chmod 777 $folder_chmod && chmod 777 $play_list_file");
            
            $pos_vhoh = strrpos($cf_ezstream, "/");
            $folder_chmod = substr($cf_ezstream, 0, $pos_vhoh);
            Ssh::create()->sshExec("chmod 777 $folder_chmod && chmod 644 $cf_ezstream");
            
            $pos_vhoh = strrpos($cf_icecast, "/");
            $folder_chmod = substr($cf_icecast, 0, $pos_vhoh);
            Ssh::create()->sshExec("chmod 777 $folder_chmod && chmod 644 $cf_icecast");

            $this->saveConfig('PLAYLIST', $play_list_file);
            $this->saveConfig('CF_EZSTREAM', $cf_ezstream);
            $this->saveConfig('CF_ICECAST', $cf_icecast);

            $xml = simplexml_load_file($cf_icecast);
			$this->saveConfig('ICE_LOGIN', $xml->authentication->{'admin-user'});
			$this->saveConfig('ICE_PASS', $xml->authentication->{'admin-password'});

			Header("Location: install.php?hag=5");
		}

		public function ifHag3() {
			$con = @ssh2_connect($this->request->getPostVar('ip'), 22);
			if(!@ssh2_auth_password($con, $this->request->getPostVar('ssh_user'), $this->request->getPostVar('ssh_pass'))) {
				return "<p>Неправильный логин или пароль.</p>";
    		}
    		$this->saveConfig('IP', $this->request->getPostVar('ip'));
    		$this->saveConfig('URL', $this->request->getPostVar('url'));
    		$this->saveConfig('PORT', $this->request->getPostVar('port'));
    		$this->saveConfig('SSH_USER', $this->request->getPostVar('ssh_user'));
    		$this->saveConfig('SSH_PASS', $this->request->getPostVar('ssh_pass'));
    		Header("Location: install.php?hag=4");
		}

		public function ifHag2() {
			$link = @mysqli_connect(
				$this->request->getPostVar('db_host'),
				$this->request->getPostVar('db_login'),
				$this->request->getPostVar('db_password')
			);
			$link_db = @mysqli_select_db($link,$this->request->getPostVar('db_name'));

			if ($link and $link_db) {
				$this->saveConfig('DB_HOST', $this->request->getPostVar('db_host'));
				$this->saveConfig('DB_LOGIN', $this->request->getPostVar('db_login'));
				$this->saveConfig('DB_PASSWORD', $this->request->getPostVar('db_password'));
				$this->saveConfig('DB_NAME', $this->request->getPostVar('db_name'));
				$this->createTable($this->request->getPostVar('db_name'));
				Header("Location: install.php?hag=3");
			} else {
				return "<p>Не удалось установить соеденение</p>";
			}
		}

		public function createTable($db_name) {
            $link = @mysqli_connect(
                $this->request->getPostVar('db_host'),
                $this->request->getPostVar('db_login'),
                $this->request->getPostVar('db_password')
            );
			mysqli_multi_query($link,file_get_contents('install.sql'))
			 or die("Install query failed : " . mysql_error());
			 			 
             $this->saveSetting('main_text', 'Здесь вы можете хранить общие записи..');
             $this->saveSetting('online', '0');
		}

		public function getPerms($file) {
        	if (is_writable($file)) {
        		return '<span class="green"><b>доступен для записи</b></span>';
        	} else {
        		return '<span class="red"><b>недоступен для записи</b></span>';
        	}
		}

		public function ifPerms($file) {
        	if (is_writable($file)) {
        		return true;
        	} else {
        		return false;
        	}
		}

		public function getBaseDir() {
			$base_dir = ini_get("open_basedir");
   			if ($base_dir == "" or $base_dir == "/") {
   			    $base_dir = (empty($base_dir)) ? 'no_value' : $base_dir;
   				return '<span class="green"><b>'.$base_dir.'</b></span>';
   			} else {
   				return '<span class="red"><b>'.$base_dir.'</b></span>';
   			}
		}

		public function getSsh2() {
			if (function_exists("ssh2_connect")) {
				return '<span class="green"><b>установлена</b></span>';
			} else {
				return '<span class="red"><b>не установлена</b></span>';
			}
		}
        
        public function getXML() {
            if (function_exists("simplexml_load_file")) {
                return '<span class="green"><b>установлена</b></span>';
            } else {
                return '<span class="red"><b>не установлена</b></span>';
            }
        }

		public function getCurl() {
			if (function_exists("curl_init")) {
				return '<span class="green"><b>установлена</b></span>';
			} else {
				return '<span class="red"><b>не установлена</b></span>';
			}
		}

		public function getIconv() {
			if (function_exists("iconv")) {
				return '<span class="green"><b>установлена</b></span>';
			} else {
				return '<span class="red"><b>не установлена</b></span>';
			}
		}

		public function getGd() {
			if (function_exists("imageCreate")) {
				return '<span class="green"><b>установлена</b></span>';
			} else {
				return '<span class="red"><b>не установлена</b></span>';
			}
		}

		public function isGreen($string) {
			if (strpos($string, 'green') !== false) {
				return true;
			} else {
				return false;
			}
		}

		public function addStatistic() {
			$add_site = "http://open-rcp.ru/stations.php?i_url=".URL."&i_ip=".IP;
			$this->request->get($add_site);
		}

		public function ifHag1() {
            if (
            	$this->isGreen(
            		$this->getPerms($this->request->getMusicPath())
            	)	 and
            	$this->isGreen(
            		$this->getPerms($this->request->getRadioPath()."../conf/config.php")
            	)	 and
            	$this->isGreen(
            		$this->getPerms($this->request->getRadioPath()."../conf/system.php")
            	)	 and
            	$this->isGreen(
            		$this->getBaseDir()
            	)	 and
            	$this->isGreen(
            		$this->getSsh2()
            	)	 and
            	$this->isGreen(
            		$this->getCurl()
            	)	 and
            	$this->isGreen(
            		$this->getIconv()
            	)   and
                $this->isGreen(
                    $this->getGd()
                )    and
                $this->isGreen(
                    $this->getXML()
                )
            ) {
            	return true;
            } else {
            	return false;
            }
		}
		
        public function saveConfig($const, $value) {     
            $value = htmlspecialchars($value, ENT_QUOTES, "utf-8");
            for ($i=0; $i<count($this->file); $i++) {
                if (strpos($this->file[$i], "define('$const'")) {
                    $this->file[$i] = "\t"."define('$const', '$value');".$this->per;
                    $h = fopen($this->filename, 'w+');
                    fwrite($h, implode($this->file, ""));
                    fclose($h);
                }
            }
        }
        
        public function saveSetting($name, $value) {
            $query = "SELECT * FROM  `settings` WHERE `name`='$name' LIMIT 1";
            $line = $this->getLine($query);
            if (!empty($line)) {
                $query = "UPDATE `settings` SET `value` = '".addslashes($value)."' WHERE `name`= '$name';";
                 $this->queryNull($query);
            } else {
                $query = "INSERT INTO `settings` ( `name` , `value` ) VALUES ('$name', '".addslashes($value)."');";
                $this->queryNull($query);;
            }
        }
        
        public function getLine($query) {
            $result = mysql_query($query) or die($this->debug());
            return mysql_fetch_array($result, MYSQL_ASSOC);
        }
        
        public function queryNull($query) {
            mysql_query($query) or die($this->debug());
        }
	}
?>