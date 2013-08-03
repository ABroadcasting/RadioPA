<?php
	class Statistic {
		const ICON_PATH = "/radio/players/";
        
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
        private function __construct() {
            $this->db = MySql::create();
            $this->setting = Setting::create();
            $this->request = Request::create();
        }

		public $icons = array(
			"WinAmp" 			=> array(
									'icon' => "winamp.gif",
									'clients' => 'winamp'
								),					
			"Консоль PSP" 		=> array(
									'icon' => "psp.png",
									'clients' => 'psp'
								),
			"The Nemesis Player" => array(
									'icon' => "nsplayer.png",
									'clients' => 'nsplayer'
								),
			"Internet Explorer" => array(
									'icon' => "msie.gif",
									'clients' => 'msie'
								),
			"Радио-станция Icecast" => array(
									'icon' => "icecast.png",
									'clients' => 'icecast'
								),
			"Радио-станция Shoutcast"	=> array(
									'icon' => "shoutcast.png",
									'clients' => 'shoutcast'
								),
			"Устройства Apple"	=> array(
									'icon' => "apple.png",
									'clients' => 'ipad, iphone, ipod'
								),
			"VLC-плеер" 		=> array(
									'icon' => "vlc.jpg",
									'clients' => 'vlc'
								),
			"Foobar2000" 		=> array(
									'icon' => "foobar2000.png",
									'clients' => 'foobar2000'
								),
			"Браузер Opera" 		=> array(
									'icon' => "opera.png",
									'clients' => 'opera'
								),
		    "Браузер Chrome"       => array(
                                    'icon' => "chrome.png",
                                    'clients' => 'chrome'
                                ),
			"Windows Media Player" 	=> array(
									'icon' => "wmplayer.gif",
									'clients' => 'wmplayer, windows-media'
								),
			"JetAudio-плеер" 			=> array(
									'icon' => "jetaudio.png",
									'clients' => 'jetaudio'
								),
			"Библиотека BASS" 	=> array(
									'icon' => "bass.gif",
									'clients' => 'bass'
								),
			"Библиотека liquidsoap" 		=> array(
									'icon' => "liquidsoap.gif",
									'clients' => 'liquidsoap'
								),
			"Screamer-плеер" 	=> array(
									'icon' => "screamer.png",
									'clients' => 'screamer'
								),
			"iRusRadio" 		=> array(
									'icon' => "irusradio.gif",
									'clients' => 'irusradio'
								),
			"Устройства Android" => array(
									'icon' => "android.png",
									'clients' => 'android'
								),
			"Устройства Sony"	=> array(
									'icon' => "sonyericsson.png",
									'clients' => 'sony'
								),
			"TuneIn Radio"	=> array(
									'icon' => "tunein.png",
									'clients' => 'tunein'
								),
			"ITunes"	=> array(
									'icon' => "itunes.png",
									'clients' => 'itunes'
								),
			"FreeAMP"	=> array(
									'icon' => "freeamp.png",
									'clients' => 'freeamp'
								),
			"Real Media Player"	=> array(
									'icon' => "realmedia.png",
									'clients' => 'realmedia'
								),
			"XMMS-плеер"	=> array(
									'icon' => "xmms.png",
									'clients' => 'xmms'
								),
			"GStreamer-фреймоврк"	=> array(
									'icon' => "gstreamer.png",
									'clients' => 'gstreamer'
								),
			"Audacious Media Player"	=> array(
									'icon' => "audacious.png",
									'clients' => 'audacious'
								),
			"Проигрыватель AIMP"	=> array(
									'icon' => "aimp.png",
									'clients' => 'aimp'
								),		
			"Библиотека Libav" => array(
                                    'icon' => "libav.png",
                                    'clients' => 'lavf52'
                                ), 
            "Браузер Firefox"=> array(
                                    'icon' => "gecko.png",
                                    'clients' => 'firefox, mozilla'
                                ),                      	                   							
		);

		public function updateAll() {  			$status = $this->requestIcecastStatus();
			$this->updateListeners();  			$this->updateStatistic($status);
  			$this->updateClients($status);		}
        
        public function updateMain() {
            $status = $this->requestIcecastStatus();
            $this->updateListeners();
            $this->updateStatistic($status);
        }

		public function getIcon($agent) {        	foreach ($this->icons as $name=>$player) {
        		$clients = explode(",", $player['clients']);
        		foreach ($clients as $client) {        			if (strpos(strtolower($agent), strtolower(trim($client)))!==false) {        				return self::ICON_PATH.$player['icon'];        			}
        		}        	}		}

		public function getClient($agent) {
        	foreach ($this->icons as $name=>$player) {
        		$clients = explode(",", $player['clients']);
        		foreach ($clients as $client) {
        			if (strpos(strtolower($agent), strtolower(trim($client)))!==false) {
        				return $name;
        			}
        		}
        	}

        	return $agent;
		}

		public function getTime($time) {  			$hour = floor($time/3600);
    		$min = floor(($time%3600)/60);
    		$sec = ($time%3600)%60;

    		$time = "";

			if ($hour != 0) {				$time .= "$hour ч. ";
			}
			if ($min != 0) {				$time .= "$min мин. ";
			}
			if (($hour == 0) and ($min == 0) and ($sec != 0)) {				$time .= "$sec сек.";
			}
			if (($hour == 0) and ($min == 0) and ($sec == 0)) {				$time .= "1 сек.";
			}

			return $time;		}

		public function getClients() {			$query = "SELECT * FROM `statistic` WHERE `type` = 'graph' ORDER BY `time` DESC";
			return $this->db->getLines($query);		}

		public function getLastSongs() {			$query = "SELECT * FROM `tracklist` ORDER BY `time` DESC";
			return $this->db->getLines($query);		}
		
		public function getSystemStreamArray() {
			$streams = explode(",", $this->setting->getSystemStream());
			$tochka = $this->getEzstreamPoint();
			foreach ($streams as $stream) {
				$return[trim($stream)] = trim($stream);
			}	
			$return[$tochka] = $tochka;

			return $return;
		}

		public function updateClients($status) {			$query = "SELECT * FROM  `settings` WHERE `name` = 'listeners' LIMIT 1";
			$line = $this->db->getLine($query);
 			$full_numbers = explode("||", $line['value']);
 			$streams = $this->getSystemStreamArray();

    		$i = 1;
			$url = "";
			if (!empty($full_numbers)) {
			 	foreach ($full_numbers as $value) {
			 		$value = explode(",", $value);
			 		foreach ($streams as $v) {
						if (trim($value[0])==trim($v)) {
			                if (strpos(IP, "http://") === false) {			                	$addr = "http://".IP;
			                }							$url[$i] = $addr.":".PORT."/admin/listclients.xsl?mount="."/".$value[0];
							$i++;
						}
			        }
			    }
			}

			$count_mount = count($url)+1;
			$ik = 1;
			$rand = 1; //
			if ($rand == 1) {
				$date = date("U")+86400;
				$date_today = $date;
				$query = "DELETE FROM `statistic` WHERE `type` = 'graph' and `date` < $date_today";
				$this->db->queryNull($query);
				
				if (!empty($url[$ik]))
				while ($ik < $count_mount) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $url[$ik]);
					curl_setopt($ch, CURLOPT_USERPWD, ICE_LOGIN.":".ICE_PASS);
					$result = curl_exec($ch);
					curl_close($ch);
					$pattern = '#(<tr>\n)?<td align=\"center\">(.*?)</td>#';
					preg_match_all($pattern,$result,$matches);

					$i = -1;
					$result = array();
					$country = array();
					$result_flag = array();
					$country_name = array();
					foreach($matches[2] as $key=>$value) {
					   if ($matches[1][$key]) $i++; // если был <tr> -- новая строка таблицы
					   $result_flag[$i][] = $value;
					}

					$k = 0;
					$s = count($result_flag);

					for ($i=0; $i<$s; $i++) {
					    $ipadres[$i] = $result_flag[$i][0];
					    $timesec[$i] = $result_flag[$i][1];
					    $client[$i] = $result_flag[$i][2];


					    $cIp = "";
					    $cTimeSec = "";
					    $cClient = "";

						if (!empty($ipadres[$i])) {
					    	$cIp = $ipadres[$i];
					    }
					    if (!empty($timesec[$i])) {
					    	$cTimeSec = $timesec[$i];
					    }
					    if (!empty($client[$i])) {
					    	$cClient = $client[$i];
					    }

					    $query = "INSERT INTO `statistic` (`type` ,`ip` ,`country` ,`country_name` ,`time` ,`client` ,`date`)
					       VALUES ('graph', '".$cIp."', '','', '".$cTimeSec."', '".$cClient."', '".$date."')";
						$this->db->queryNull($query);

					}
					$ik++;
				}
			}		}

		public function updateListeners() {
			$query = "SELECT * FROM `statistic` WHERE `type` = 'day'";
			$count = $this->db->getCountRow($query);

			$query = "DELETE FROM `statistic` WHERE (($count>122) and (`type` = 'day')) ORDER BY `time` LIMIT 2;";
			$this->db->queryNull($query);
			
			$date = date("U");
			$query = "SELECT * FROM `statistic` WHERE `type` = 'day' ORDER BY `time` DESC";
			$posl_time = $this->db->getColumn($query, 'time');
			$posl_time = $posl_time + 600;

			if ($posl_time < $date)  {
				$query="INSERT INTO `statistic` (`type` ,`listeners`, `time`) VALUES ('day', '".$this->getListeners()."', '".$date."')";
				$this->db->queryNull($query);
			}
		}	

		public function updateStatistic($status) {
			$page = $status;
			$cf_ez_url = $this->getEzstreamPoint();			$page = str_replace("<pre>", "", $page); //extract data
			$page = str_replace("</pre>", ",", $page); //extract data
			$page = str_replace("\n","",$page);
			$numbers = explode(",/", $page);

			unset($numbers[0]);
			foreach ($numbers as $value) {
				if (isset($full_numbers)) {
					$full_numbers .= "||$value";
				} else {
					$full_numbers = "$value";
				}
			}

			if (!isset($full_numbers)) {
				$full_numbers = "";
			}	
			
			$full_numbers = addslashes($full_numbers);

			$query = "SELECT * FROM  `settings` WHERE `name`='listeners' LIMIT 1";
			$line = $this->db->getLine($query);
			if (!empty($line)) {
				$query="UPDATE `settings` SET `value` = '$full_numbers' WHERE `name`= 'listeners';";
				$this->db->queryNull($query);
			} else {
				$query="INSERT INTO `settings` ( `name` , `value` ) VALUES ('listeners', '$full_numbers');";
			 	$this->db->queryNull($query);
			}
        }

		public function getStreamCount() {        	$stream = $this->getSystemStreamArray();
			$stream = array_unique($stream);
   			$potok = 0;
            $listeners = 0;

            $query="SELECT * FROM  `settings` WHERE `name`='listeners' LIMIT 1";
 			$line = $this->db->getLine($query);

			if (!empty($line['value'])) {
	 			$full_numbers = explode("||", $line['value']);
				foreach ($full_numbers as $value) {
	 				$value = explode(",",$value);
	 				foreach ($stream as $v) {
						if ($value[0]==$v) {
							$listeners = $listeners+$value[3];
							$potok = $potok+1;
						}
		      		}
	    		}
    		}

    		return $potok;		}

		public function getListeners() {        	$stream = $this->getSystemStreamArray();
			$stream = array_unique($stream);
   			$potok = 0;
            $listeners = 0;
            $query = "SELECT * FROM  `settings` WHERE `name`='listeners' LIMIT 1";
 			$line = $this->db->getLine($query);

 			if (!empty($line['value'])) {
	 			$full_numbers = explode("||", $line['value']);
				foreach ($full_numbers as $value) {
	 				$value = explode(",", $value);
	 				foreach ($stream as $v) {
						if ($value[0]==$v) {
							$listeners = $listeners+$value[3];
							$potok = $potok+1;;
						}
		      		}
	    		}
			}	
			
    		if (empty($listeners)) {    			$listeners = 0;
    		}
			
			return $listeners;
		}

		public function getEzstreamPoint() {			$rf_ez = file(CF_EZSTREAM);			if ($rf_ez) {
				for ($i=0; $i<count($rf_ez); $i++) {
					if (strpos($rf_ez[$i], '<url>')!==false) {
						$cf_ez_url = $rf_ez[$i];
					}
				}
			}
			$cf_ez_url =  str_replace("</url>", "", $cf_ez_url);
			$cf_ez_url =  str_replace("<url>", "", $cf_ez_url);
			$cf_ez_url = explode("/", $cf_ez_url);
			$cf_ez_url = $cf_ez_url[3];
    		$cf_ez_url = str_replace("\n", "", $cf_ez_url);

    		return trim($cf_ez_url);		}

		public function requestIcecastStatus() {        	$fp = @fsockopen(IP, PORT, $errno, $errstr, 1);
			if (empty($fp)) {
				return false;
			} else {				fputs($fp,"GET /status2.xsl HTTP/1.0\r\nUser-Agent: Icecast2 XSL Parser (Mozilla Compatible)\r\n\r\n"); //get status2.xsl
 				$page = "";
 				while(!feof($fp)) {
  					$page .= fgets($fp, 1000);
				}
				fclose($fp);
				return $page;			}		}

		public function updateDirectory() {
			$url = "http://radiocms.ru/stations.php?name=".DIR_NAME.
				"&url=".DIR_URL.
				"&stream=".DIR_STREAM.
				"&description=".DIR_DESCRIPTION.
				"&genre=".DIR_GENRE.
				"&listeners=".$this->getListeners().
				"&bitrate=".DIR_BITRATE."&ip=".IP.
				"&charset=utf-8";

            $this->request->get($url);
		}
	}
?>