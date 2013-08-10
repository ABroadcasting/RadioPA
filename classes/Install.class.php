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
        
		private function __construct() {			$this->request = Request::create();
            $this->filename = $this->request->getRadioPath()."_config.php";
            $this->file = file($this->filename);		}

		public function getWgetCron() {			return Ssh::create()->getWgetCommand()." http://".$this->request->getServerVar('HTTP_HOST')."/radio/"."event.php -O event.php";		}

		public function getPhpCron() {  			$file_adres = "полый/путь/до/php ".$this->request->getServerVar('DOCUMENT_ROOT')."/radio/"."event.php";
			$file_adres = str_replace("//","/",$file_adres);
			return $file_adres;		}

		public function ifHag5() {			$user = $this->request->getPostVar('user');
			$password = $this->request->getPostVar('password');

			if (empty($user) or empty($password)) {				return "<p>Поля не могу быть пустыми</p>";			}

			$this->saveConfig('USER', $user);            $this->saveConfig('PASSWORD', $password);

            Header("Location: install.php?hag=6");		}

		public function ifHag4() {
			$play_list_file = $this->request->getPostVar('playlist');
			$cf_ezstream = $this->request->getPostVar('cf_ezstream');
			$cf_icecast = $this->request->getPostVar('cf_icecast');			if (empty($play_list_file) or empty($cf_ezstream) or empty($cf_icecast)) {				return "<p>Заполнены не все поля.</p>";
			}
            
            if (!file_exists($cf_icecast)){
                return"<p>Файл конфигурации icecast не существует.</p>";
            }
            
            if (!file_exists($cf_ezstream)) {
                return "<p>Файл конфигурации ezstream не существует.</p>";
            }
            
			if (!file_exists($play_list_file)) {				return "<p>Файл плейлиста не существует.</p>";
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

			Header("Location: install.php?hag=5");		}

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
    		Header("Location: install.php?hag=4");		}

		public function ifHag2() {			$link = @mysql_connect(
				$this->request->getPostVar('db_host'),
				$this->request->getPostVar('db_login'),
				$this->request->getPostVar('db_password')
			);
			$link_db = @mysql_select_db($this->request->getPostVar('db_name'));

			if ($link and $link_db) {
				$this->saveConfig('DB_HOST', $this->request->getPostVar('db_host'));
				$this->saveConfig('DB_LOGIN', $this->request->getPostVar('db_login'));
				$this->saveConfig('DB_PASSWORD', $this->request->getPostVar('db_password'));
				$this->saveConfig('DB_NAME', $this->request->getPostVar('db_name'));				$this->createTable($this->request->getPostVar('db_name'));
				Header("Location: install.php?hag=3");			} else {				return "<p>Не удалось установить соеденение</p>";			}		}

		public function createTable($db_name) {			mysql_query("SET NAMES 'utf8'") 
				or die("Install query failed : " . mysql_error());
			mysql_query("ALTER DATABASE `".$db_name."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `last_zakaz` (
			  `id` varchar(15) NOT NULL,
			  `idsong` varchar(15) NOT NULL,
			  `track` varchar(100) NOT NULL,
			  `time` varchar(25) NOT NULL,
			  `skolko` varchar(10) NOT NULL,
			  `ip` varchar(25) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `login` (
			  `ip` varchar(25) NOT NULL,
			  `dj` varchar(50) NOT NULL,
			  `raz` tinyint(10) NOT NULL,
			  `time` varchar(25) NOT NULL,
			  `hash` varchar(25) NOT NULL,
			  `admin` int(1) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `playlist` (
			  `id` int(11) NOT NULL auto_increment,
			  `name` text,
			  `playmode` tinyint(4) default NULL,
			  `enable` tinyint(4) default NULL,
			  `event1` text,
			  `event2` text,
			  `now` tinyint(4) default NULL,
			  `show` tinyint(4) default NULL,
			  `sort` int(11) default NULL,
			  `last_time` bigint(20) default NULL,
			  `allow_zakaz` int(11) default '1',
			  `auto` int(11) default '0',
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `poisk` (
				`title` varchar(50) NOT NULL,
				`artist` varchar(50) NOT NULL,
				`id` int(10) NOT NULL,
				`idsong` int(11) NOT NULL,
				`filename` text NOT NULL,
				`duration` int(11) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `songlist` (
			  `idsong` int(11) NOT NULL auto_increment,
			  `zakazano` int(10) NOT NULL,
			  `id` int(11) default NULL,
			  `filename` text,
			  `artist` text,
			  `title` text,
			  `album` text,
			  `genre` text,
			  `albumyear` int(11) default NULL,
			  `duration` int(11) default NULL,
			  `played` int(1) default '0',
			  `sort` int(11) default NULL,
              PRIMARY KEY  (`idsong`),
              FULLTEXT KEY `artist` (`artist`,`title`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `statistic` (
			  `type` varchar(50) NOT NULL,
			  `country` varchar(20) NOT NULL,
			  `country_name` varchar(25) NOT NULL,
			  `ip` varchar(50) NOT NULL,
			  `client` varchar(150) NOT NULL,
			  `listeners` varchar(15) NOT NULL,
			  `time` int(20) NOT NULL,
			  `date` varchar(10) NOT NULL,
			  KEY `stream` (`listeners`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			mysql_query("CREATE TABLE IF NOT EXISTS `tracklist` (
			  `title` text,
			  `id` int(20) NOT NULL auto_increment,
			  `idsong` int(11) NOT NULL,
			  `filename` varchar(200) NOT NULL,
			  `time` varchar(25) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `user_ip` (
			  `id` int(20) NOT NULL auto_increment,
			  `ip` varchar(100) NOT NULL,
			  `time` varchar(100) NOT NULL,
			  `nomer` int(2) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `zakaz` (
			  `id` int(11) NOT NULL auto_increment,
			  `idsong` int(10) NOT NULL,
			  `filename` text,
			  `artist` text,
			  `title` text,
			  `album` text,
			  `duration` int(11) default NULL,
			  `admin` int(1) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `settings` (
			  `name` varchar(25) NOT NULL,
			  `value` text NOT NULL,
			  PRIMARY KEY  (`name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;")
			 or die("Install query failed : " . mysql_error());

			mysql_query("CREATE TABLE IF NOT EXISTS `dj` (
			  `id` tinyint(50) NOT NULL auto_increment,
			  `description` varchar(100) NOT NULL,
			  `dj` varchar(50) NOT NULL,
			  `password` varchar(50) NOT NULL,
			  `admin` int(1) NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `dj` (`dj`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;")
			 or die("Install query failed : " . mysql_error());
			 			 
             $this->saveSetting('main_text', 'Здесь вы можете хранить общие записи..');
             $this->saveSetting('online', '0');		}

		public function getPerms($file) {        	if (is_writable($file)) {        		return '<span class="green"><b>доступен для записи</b></span>';        	} else {        		return '<span class="red"><b>недоступен для записи</b></span>';        	}		}

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

		public function getSsh2() {			if (function_exists("ssh2_connect")) {				return '<span class="green"><b>установлена</b></span>';			} else {				return '<span class="red"><b>не установлена</b></span>';			}		}
        
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

		public function isGreen($string) {			if (strpos($string, 'green') !== false) {				return true;			} else {				return false;			}		}

		public function addStatistic() {
			$add_site = "http://radiocms.ru/stations.php?i_url=".URL."&i_ip=".IP;
			$this->request->get($add_site);
		}

		public function ifHag1() {            if (
            	$this->isGreen(
            		$this->getPerms($this->request->getMusicPath())
            	)	 and
            	$this->isGreen(
            		$this->getPerms($this->request->getRadioPath()."_config.php")
            	)	 and
            	$this->isGreen(
            		$this->getPerms($this->request->getRadioPath()."_system.php")
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
            ) {            	return true;            } else {            	return false;            }		}
		
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
        }	}
?>