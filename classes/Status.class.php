<?php
	class Status {
		public $cmd_ps = "ps ax | grep";
        
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }

		private function __construct() {
        	$this->db = MySql::create();
        	$this->ssh = Ssh::create();
        	$this->request = Request::create();

        	if ($this->ssh->connected) {
				$this->update();
        	}		}

		public function handler() {
        	if ($this->request->hasPostVar('next_x')) {
        		$this->nextTrack();
        	}

        	if ($this->request->hasPostVar('on_x')) {        		$this->updateSetting(1);
        		if (!$this->isIcecastRunned()) {
        			$this->startIcecast();
        		}
        		if (!$this->isEzstreamRunned()) {
        			$this->startEzstream();
        		}
        		$this->firstStartCheck();
                sleep(1);
        	}

        	if ($this->request->hasPostVar('on_air_x')) {
        		$this->updateSetting(2);
        		$this->stopEzstream();
        		if (!$this->isIcecastRunned()) {
        			$this->startIcecast();
        		}
                sleep(1);
        	}

            $user = Autentification::create()->getUser();
        	if ($this->request->hasPostVar('off_x') and $user['admin'] == 1) {
        		$this->updateSetting(0);
        		$this->stopEzstream();
        		$this->stopIcecast();
        	}
		}

		public function update() {
        	$this->pocess_ezstream = $this->getEzstreamProcess();
			$this->icecastRunned = $this->icecastRunCheck();
			$this->cmd_ice = $this->getIcecastCommand();
		}

		public function updateSetting($value) {
			$query = " UPDATE `settings` SET `value` = $value WHERE `name` = 'online'  ";
			$this->db->queryNull($query);
		}

		public function firstStartCheck() {
			if (filesize(PLAYLIST) == 0) {
				$query = "SELECT * FROM `songlist` LIMIT 200";

				$play_list_text = "";
				foreach ($this->db->getLines($query) as $line) {
					if ( file_exists($line['filename']) ) {
						$play_list_text .= $line['filename']."\n";
					}
				};

				$file = fopen(PLAYLIST, "w");
				fwrite($file, $play_list_text);
				fclose($file);
			}
		}

		public function startEzstream() {
			$data = $this->ssh->sshExec("ezstream -c ".CF_EZSTREAM." > /dev/null &");
		}

		public function startIcecast() {
			$data = $this->ssh->getResponse($this->cmd_ice." -b -c ".CF_ICECAST." > /dev/null &");
		}

		public function stopEzstream() {
			$data = $this->ssh->getResponse("kill ".$this->pocess_ezstream);
		}

		public function stopIcecast() {
			$data = $this->ssh->getResponse("killall ".$this->cmd_ice);
			sleep(2);
		}

		public function nextTrack() {
			$data = $this->ssh->getResponse("kill -USR1 ".$this->pocess_ezstream);
			sleep(1);
		}

		public function isIcecastRunned() {
			return $this->icecastRunned;
		}

		public function isEzstreamRunned() {
			return $this->pocess_ezstream;
		}

		public function icecastRunCheck() {
        	$data = $this->ssh->getResponse($this->cmd_ps." icecast");
        	$poverka = strpos($data,"-b -c");
    		if ($poverka !== false) {
    			$zapusk_icecast = true;
    		} else {
    			$zapusk_icecast = false;
    		}

    		return $zapusk_icecast;
		}

		public function getEzstreamProcess() {
			$pocess_ezstream = false;
			$data = $this->ssh->getResponse($this->cmd_ps." ezstream");
			$data1 = explode("\n", $data);
        	foreach ($data1 as $value) {
        		$data2 = explode("?",$value);
        		$data3 = "";
        		if (!empty($data2[1])) {
        		 	$data3 .=$data2[1];
        		}
        		if (!empty($data2[2])) {
        		 	$data3 .=$data2[2];
        		}
        		if (
        			(strpos($data3,"bash -c") === false) and
        			(strpos($data3,"csh -c") === false) and
        			(strpos($data3, CF_EZSTREAM) !== false)
        		) {
        			$zapusk_ezstream = "on";
        			$pocess_ezstream = $data2[0];
        		}
        	}
        	return $pocess_ezstream;
		}

		public function getIcecastCommand() {
			$data = $this->ssh->getResponse("icecast2");
			if (strpos($data, "usage: icecast") > 1) {
        		$cmd_ice = "icecast2";
			} else {
        		$cmd_ice = "icecast";
        	}
        	return $cmd_ice;
		}

		public function getStreams() {			$tochka = Statistic::create()->getEzstreamPoint();			$query = "SELECT * FROM  `settings` WHERE `name`='stream' LIMIT 1";
 			$line = $this->db->getLine($query);
			$system_stream = $line['value'];

			$system_stream = explode(",", $system_stream);
			$system_stream[] = $tochka;

			$stream = array("");
			foreach ($system_stream as $v) {
				$stream[] = trim($v);
			}

			$stream = array_unique($stream);

			$query = "SELECT * FROM  `settings` WHERE `name`='listeners' LIMIT 1";
            $line = $this->db->getLine($query);
			$listeners = $line['value'];
 			$listeners = explode("||",$listeners);

			$return = array();
			if (!empty($listeners))
 			foreach ($listeners as $value) {
 				$value = explode(",", $value);
                if (!empty($value[5]))
 				foreach ($stream as $v) {
					if ($value[0] == $v) {
						$index = $v;						$return[$index]['tochka'] = $value[0];
						$return[$index]['listeners'] = $value[3];
						$status_cur_song = Tracklist::create()->extractCurrentSong($value);
                        if (!empty($status_cur_song)) {
                            preg_match("/([^$]*?) - ([^$]+) - ([^$]+)/", $status_cur_song, $matches);
                        }
                        
                        if (!empty($matches)) {
                            $status_cur_song = $matches[2]." - ".$matches[3];
                        }    

						if (strpos(URL, "http://") === false) {
							$adres = "http://".URL;
						} else {
						    $adres = URL;
						}

						$link = $adres.':'.PORT."/".$tochka.'.m3u';
						$return[$index]['link'] = str_replace("\n","",$link);

						if (empty($status_cur_song)) {							$status_cur_song = "Нет данных";
						}
						$return[$index]['cur_song'] = $status_cur_song;
					}
				}
			}
			return $return;		}	}
?>