<?php
	class PlaylistAll {
	    
        public static function create() {
            return new self();
        }
        
		private function __construct() {			$this->request = Request::create();
			$this->filter = Filter::create();
			$this->playlist = Playlist::create();
			$this->db = MySql::create();
			include($this->request->getRadioPath().'_system.php');
			$this->allowTime = $allow_time;		}

		public function handler() {
			$notice = array();
			$this->clean();
			$zakaz = $this->getZakaz();
			if ($zakaz) {				$notice['zakaz'] = $this->zakaz($zakaz);			}
			return $notice;		}

		public function getSongList() {			$search = $this->getSearch();
			$letter = $this->getLetter();
			if (!empty($search)) {				return $this->getSongListWitchSearch();			}
			if (!empty($letter)) {
				return $this->getSongListWitchLetter();
			}

			return $this->getSongListWitchNoFilter();		}

		private function getSongListWitchNoFilter() {
			$ne_pokazivat = $this->playlist->getNePokazivat();
			$sortArray = $this->getSortArray();			$query = "SELECT * FROM `songlist` WHERE $ne_pokazivat ORDER BY `".$sortArray['value']."` ".$sortArray['obr'];
			$this->vsegoPesen = $this->db->getCountRow($query);
			return $this->db->getLines($query." LIMIT ".$this->getStart().",".$this->getLimit());		}

		private function getSongListWitchLetter() {
			$ne_pokazivat = $this->playlist->getNePokazivat();        	$letter = $this->getLetter();
        	$sortArray = $this->getSortArray();
        	if ($letter == "0-9") {        		$query = "SELECT * FROM `songlist` WHERE ($ne_pokazivat) and `artist` LIKE '0%' or `artist` LIKE '1%' or `artist` LIKE '2%' or `artist` LIKE '3%' or `artist` LIKE '4%' or `artist` LIKE '5%' or `artist` LIKE '6%' or `artist` LIKE '7%' or `artist` LIKE '8%' or `artist` LIKE '9%' ORDER BY `".$sortArray['value']."` ".$sortArray['obr'];
        		$this->vsegoPesen = $this->db->getCountRow($query);
        		return $this->db->getLines($query." LIMIT ".$this->getStart().",".$this->getLimit());        	} else {        		$query = "SELECT * FROM `songlist` WHERE ($ne_pokazivat) and `artist` LIKE '".$letter."%' ORDER BY `".$sortArray['value']."` ".$sortArray['obr'];
                $this->vsegoPesen = $this->db->getCountRow($query);
                return $this->db->getLines($query." LIMIT ".$this->getStart().",".$this->getLimit());        	}
		}

		private function getSongListWitchSearch() {
			$search = $this->getSearch();
			$sortArray = $this->getSortArray();
			$ne_pokazivat = $this->playlist->getNePokazivat();
			
			if ($this->request->hasGetVar('sort')) {
                $sort = "ORDER BY `".$sortArray['value']."` ".$sortArray['obr'];
            } else {
                $sort = "";
            }
						$query = "SELECT * FROM `songlist` WHERE ($ne_pokazivat) and MATCH (`artist`, `title`) AGAINST ('$search') $sort";			$this->vsegoPesen = $this->db->getCountRow($query);
			
			return $this->db->getLines($query." LIMIT ".$this->getStart().",".$this->getLimit());
		}

		public function getSortArray() {
			if ($this->request->hasGetVar('sort')) {
				$sort = array();
				$sortString = $this->request->getGetVar('sort');
				$sortString = str_replace('%21', '!', $sortString);
				if ($sortString[0] == "!"){
					$sort['obr'] = "DESC";
					$sort['value'] = substr($sortString, 1);
					$sort['string'] = $sortString;
				} else {
					$sort['obr'] = "ASC";
					$sort['value'] = $sortString;
					$sort['string'] = $sortString;
				}
			} else {
				$sort['obr'] = "DESC";
				$sort['value'] = "zakazano";
				$sort['string'] = "!zakazano";
			}
			
			$sort['value'] = addslashes($sort['value']);
			
			return $sort;
		}

		public function getVsegoPesen() {
			return $this->vsegoPesen;		}

		public function zakaz($zakaz) {
			$return = array();        	$query = "SELECT * FROM `playlist` WHERE `now` = 1 ";
			$now_play = $this->db->getColumn($query, 'now');
			$allow_zakaz = $this->db->getColumn($query, 'allow_zakaz');
			$on_air = $this->getStatus();

			if ( $allow_zakaz != 1 or $on_air == "2" or $on_air == "0" ) {
				if ($allow_zakaz != 1) {					$return[] = "Сейчас сервис заказов недоступен, пожалуйста попробуйте ещё раз в рабочее время.";
				}
				if ($on_air == "2") {					$return[] = "Во время прямого эфира заказывать песни нельзя.";
				}
				if ($on_air == "0") {					$return[] = "К сожалению, радио сейчас не работает.";
				}
			} else {
				$proverka_realip = $this->request->getServerVar('REMOTE_ADDR');
				$proverka_gettime_now = date('U');
				$proverka_gettime = date('U')+900;

				$query = " SELECT * FROM `user_ip` WHERE `ip` = '$proverka_realip' and `nomer` >= 1 ";
				if ($this->db->getColumn($query, 'ip') == $proverka_realip) {
					$return[] = "Нельзя заказать более одной песни в течение 15 минут, пожалуйста подождите.";
				}

				// Запрос на проверку одинаковых песен
				$query = " SELECT * FROM `zakaz` WHERE `idsong` = $zakaz ";
				$odinakovie_pesni = $this->db->getColumn($query, 'idsong');
				if (($odinakovie_pesni != "") and ($odinakovie_pesni == $zakaz)) {					$return[] = 'Эту песню уже заказали.';
				}

				// Считаем количество заказов
				$query = " SELECT * FROM `zakaz`";
			    if ($this->db->getCountRow($query) >= LIMIT_ZAKAZOV) {
			        if ($this->getAllowTime() > date("U")) {
			    		$return[] = "Приём заявок завершён, пожалуйста попробуйте после ".$this->getPosle()." по Москве.";
			    	}

			    }

			    // Вытаскивает Артист - Титл
			    $query = " SELECT * FROM `songlist` WHERE `idsong` = $zakaz ";
				$proverka_full = $this->db->getColumn($query, 'artist')." - ".$this->db->getColumn($query, 'title');

			 	// Проверяем наличие в игравших
			 	$query = " SELECT * FROM `tracklist` WHERE `title` = '".addslashes($proverka_full)."'";

				if ($this->db->getColumn($query, 'title')) {					$return[] = "Эта песня играла недавно и поэтому её сейчас нельзя заказать.";
				}

				if (empty($return)) {
					// Добавляем заказ в массивы из songlist
					$query = " SELECT * FROM `songlist` WHERE `idsong` = $zakaz ";
					$line = $this->db->getLine($query);

				    $zakaz_track = $line['artist']." - ".$line['title'];
				    $query = "SELECT * FROM `zakaz`";
					$status_zakazov_imeetsa = $this->db->getCountRow($query)+1;

					// заносим заказ в zakaz
					$query = "INSERT INTO `last_zakaz` (`track` , `time` , `skolko`  , `ip` , `idsong`, `id` )
						VALUES (
							'".addslashes($zakaz_track)."',
							'$proverka_gettime_now',
							'$status_zakazov_imeetsa',
							'$proverka_realip',
							'".$line['idsong']."',
							'".$line['id']."'
						)";
					$this->db->queryNull($query);	
				
					$query = "SELECT * FROM `last_zakaz`";
					$status_zapisei = $this->db->getCountRow($query);
					$query = "DELETE FROM `last_zakaz` WHERE $status_zapisei>100 ORDER BY `time` LIMIT 2;";
					$this->db->queryNull($query);

					$query = "INSERT INTO `zakaz` (`idsong` ,`filename` , `artist` , `title` , `album` , `duration` )
						VALUES (
							'".$line['idsong']."',
							'".addslashes($line['filename'])."',
							'".addslashes($line['artist'])."',
							'".addslashes($line['title'])."',
							'".addslashes($line['album'])."',
							'".$line['duration']."'
						)";
					$this->db->queryNull($query);
					$return[] =  "Заказ принят и будет исполнен в течение 20 минут после ".$this->getPosle()." по Москве.";

					$query = " UPDATE `songlist` SET `zakazano` = `zakazano`+1 WHERE `filename` = '".addslashes($line['filename'])."' ";
					$this->db->queryNull($query);

					$query = " SELECT * FROM `user_ip` WHERE `ip` = '$proverka_realip' ";
					if (!$this->db->getLine($query)) {
						$query = "INSERT INTO `user_ip` (`ip` , `time` , `nomer` ) VALUES ( '$proverka_realip', '$proverka_gettime', '1' )";
						$this->db->queryNull($query);
					}
				}
			}

			return $return;		}

		public function getPosle() {			$posle =  date("H:i", $this->getAllowTime()+120);
			if (date("U") > $this->getAllowTime()) {
				$posle =  date("H:i", date("U")+120);
			}
			return $posle;		}

		public function getAllowTime() {			return $this->allowTime;		}

		public function getStatus() {			$query = "SELECT * FROM `settings` WHERE `name` = 'online' LIMIT 1 ";
			return $this->db->getColumn($query, 'value');		}

		private function getZakaz() {
			$zakaz = false;			for ($k=0; $k<$this->getLimit(); $k++) {
				$zakaz_proverka = "zakaz_".$k."_x";
				$zakaz_nomer = "zakaz_".$k;
				if (!empty($_POST[$zakaz_proverka])) {
					$zakaz = intval($_POST[$zakaz_nomer]);
				}
			}
			return $zakaz;		}

		private function clean() {
			$query = "SELECT * FROM `user_ip`";
			foreach ($this->db->getLines($query) as $line) {
				if ($line['time'] < date('U')) {
					$query = " DELETE FROM `user_ip` WHERE `user_ip`.`id` = '".$line['id']."' LIMIT 1 ";
					$this->db->queryNull($query);
				}
			}		}

		public function setUrlStart($url) {        	$this->urlStart = $url;		}

		public function getUrlStart() {
        	return "http://".$this->request->getServerVar('HTTP_HOST').$this->request->getServerVar('PHP_SELF');
		}

		public function getStart() {			if ($this->request->hasGetVar('start')) {				return (int) $this->request->getGetVar('start');			} else {				return 0;			}		}

		public function getLimit() {
			if ($this->request->hasGetVar('limit')) {
				return (int) $this->request->getGetVar('limit');
			} else {
				return 25;
			}
		}

		public function getSort() {
			if ($this->request->hasGetVar('sort')) {
			    $sortString = $this->request->getGetVar('sort');
			    $sortString = str_replace('%21', '!', $sortString);
				return $sortString;
			} else {
				return "!zakazano";
			}
		}

		public function getSearch() {
			if ($this->request->hasGetVar('search')) {				$search = $this->request->getGetVar('search');
				$search = htmlspecialchars($search, ENT_QUOTES, "utf-8");

				if (TRANSLIT == "on") {
					$search = $this->filter->translit($search);
				}

				return $search;
			} else {
				return "";
			}
		}
        
        public function getSearchString() {
            if ($this->request->hasGetVar('search')) {
                return str_replace('"', '', $this->request->getGetVar('search'));
            } else {
                return "";
            } 
        }       

		public function getLetter() {
			if ($this->request->hasGetVar('letter')) {
				return addslashes($this->request->getGetVar('letter'));
			} else {
				return "";
			}
		}	}
?>