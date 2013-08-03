<?php
	class Tracklist {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
		private function __construct() {			$this->db = MySql::create();		}

		public function getCurrentSong() {			$current = $this->getCurrentArtistAndTitle();			if (!empty($current['artist']) and !empty($current['title'])) {				return $current['artist']." - ".$current['title'];			} else {				return "Нет данных";			}		}

		public function getLastTrackList($number) {			$query = "SELECT * FROM `tracklist` ORDER BY `id` DESC LIMIT $number";
			$last = array();
			foreach ($this->db->getLines($query) as $track) {
				$title = $track['title'];            	if (iconv_strlen($title, 'UTF-8') > TRACK_CUT_LENGTH) {                	$last[] = iconv_substr($title, 0, TRACK_CUT_LENGTH, 'UTF-8')."..";            	} else {
            		if (empty($title)) {            			$title = "Нет данных";            		}            		$last[] = $title;            	}			}
			return $last;		}

		public function cleaningTracklist() {			$query = "SELECT * FROM `tracklist`";
			$status_zapisei = $this->db->getCountRow($query);

			$status_vivod = 7; // сколько выводить
			if (NO_REPEAT > $status_vivod) {				$status_vivod = NO_REPEAT;
			} // но не меньше чем нужно для плейлиста на главной
			
			if ($status_zapisei > $status_vivod) {                $query = "DELETE FROM `tracklist` ORDER BY `id` ASC LIMIT 2";
                $this->db->queryNull($query);
            }    		}

		public function getLastTrack() {			$query = "SELECT * FROM `tracklist` ORDER BY `id` DESC LIMIT 0,1";
			$line = $this->db->getLine($query);
			return $line['title'];		}

		public function infoFromPoint($point) {			$this->infoFrom = $point;		}

		public function getNoRepeatArray() {			$net_povtorov_array = array();
			$query = "SELECT * FROM `tracklist` ORDER BY `id` DESC LIMIT ".NO_REPEAT;
			foreach ($this->db->getLines($query) as $line) {
				$net_povtorov_array[] = $line['filename'];
			}
			return $net_povtorov_array;		}

		public function deleteRepeat() {
			$query = "SELECT * FROM `tracklist` ORDER BY `id` DESC LIMIT 0,1";
			$line1 = $this->db->getLine($query);

			$query = "SELECT * FROM `tracklist` ORDER BY `id` DESC LIMIT 1,1";
			$line2 = $this->db->getLine($query);

			if (
			     $line1['idsong'] == $line2['idsong'] or 
			     $line1['title'] == $line2['title']			     
            ) {
				$query = "DELETE FROM `tracklist` WHERE `id` = '".$line1['id']."'";
				$this->db->queryNull($query);
			}		}

		public function getCurrentArtistAndTitle() {
   			$query = "SELECT * FROM  `settings` WHERE `name` = 'listeners' LIMIT 1";
			$line = $this->db->getLine($query);
 			$full_numbers = explode("||", $line['value']);
            $status_cur_song = "";
            
 			foreach ($full_numbers as $value) {
 				$arr = explode(",", $value);
 				if (trim($arr[0]) == trim(Statistic::create()->getEzstreamPoint())) {
 					$mount_song = $this->extractCurrentSong($arr);
                    if (!empty($mount_song) and $mount_song != " - ") {
                        $status_cur_song = $mount_song;
                    }
 				}
 			}

 			if (!empty($this->infoFrom)) { 				foreach ($full_numbers as $value) {
 					$arr = explode(",", $value);
					if (trim($arr[0]) == $this->infoFrom) {
						$mount_song = $this->extractCurrentSong($arr);
                        if (!empty($mount_song) and $mount_song != " - ") {
                            $status_cur_song = $mount_song;
                        }
      	  			}
    			} 			}

			if (!empty($status_cur_song)) {
 				preg_match("/^([^$]*?) - ([^$]+) - ([^$]+)$/", $status_cur_song, $matches);
 			}

			$current = array();
			$current['artist'] = "";
			$current['title'] = "";
			if (!empty($matches)) {
				$current['artist'] = $matches[2]; // артист
				$current['title'] = $matches[3]; // исполнитель
			}
            
 			return $current;
		}
		
		public function extractCurrentSong($arr) {
            for ($i=5; $i<12; $i++) {
                if (isset($arr[$i]) and $arr[$i]!="") {
                    if (!isset($name)) {
                        $name = $arr[$i];
                    } else {
                        $name .= ",".$arr[$i];
                    }
                }    
            }
            
            return $name;
        }

		public function getNowFilename() {
		    if (!Ssh::create()->checkEzstreamCompatibility()) {
                $likeChar = "%";
            } else {
                $likeChar = "";
            } 
            
			$current = $this->getCurrentArtistAndTitle();
            if (!empty($current)) {
    			$status_cur_song = $current['artist']." - ".$current['title'];
    			$query = "SELECT * FROM `songlist` WHERE `artist` LIKE '".addslashes($current['artist'])."$likeChar' and `title` LIKE '".addslashes($current['title'])."$likeChar'";
    			$line = $this->db->getLine($query);
            }
            
			if (!empty($line)) {
				return $line['filename'];
			} else {				return "";			}
		}

		public function getRandFilename($limit) {			$query = "SELECT * FROM `playlist` WHERE `now` = 1";
			$nfid = $this->db->getColumn($query, 'id');
			if (!empty($nfid)) {
				$query = "SELECT * FROM `songlist` WHERE `id`=$nfid LIMIT $limit";
				foreach ($this->db->getLines($query) as $line) {
					if ( file_exists($line['filename']) ) {						$net_faila_a[] = $line['filename'];
					}
				}
				$cnf = count($net_faila_a)-1;
				$cnf = rand(0, $cnf);
				return $net_faila_a[$cnf];
			} else {				return "";			}		}

		public function update() {
		    $this->updateTracklist();
            $this->deleteRepeat();
            $this->cleaningTracklist();
            $this->updatePlayed();		}
		
		public function updatePlayed() {
            $nowplaylist = $this->db->getLine("select * from playlist where now = 1");
            $nowfilename = $this->getNowFilename();
            
            if (empty($nowplaylist) or empty($nowplaylist)) {
                return false;
            }
            
            $nowTrack = $this->db->getLine("
                select * from songlist
                where
                    filename = '".addslashes($nowfilename)."' and id = ".$nowplaylist['id']
            );
            
            $allTracks = $this->db->getLines("
                select idsong, sort, played from songlist
                where
                    id = ".$nowplaylist['id']."
                order by    
                    sort asc
            ");
            
            if (is_array($allTracks) and !empty($nowTrack)) {
                foreach ($allTracks as $index=>$track) {
                    if ($track['idsong'] == $nowTrack['idsong']) {
                        if (!empty($allTracks[$index-1])) {
                            $prevTrack = $allTracks[$index-1];
                        } else {
                            $prevTrack = "";
                        }
                    }
                }
            }            
            
            $access = false;
            if (empty($prevTrack)) {
                $access = true;
            }
            if (!empty($prevTrack) and $prevTrack['played'] == 1) {
                $access = true;
            }
            
            if (
                isset($nowplaylist['playmode']) and 
                !empty($nowfilename) and
                $nowplaylist['playmode'] == "0" and
                $access
            ) {
                $this->db->queryNull("update songlist set played = 1 where id = ".$nowplaylist['id']." and filename = '".addslashes($nowfilename)."'");
            }
		}
        
        public function updateTracklist() {
            if (!Ssh::create()->checkEzstreamCompatibility()) {
                $likeChar = "%";
            } else {
                $likeChar = "";
            }     
            
            $current = $this->getCurrentArtistAndTitle();
            if (!empty($current)) {
                $query = "SELECT * FROM `songlist` WHERE `artist` LIKE '".addslashes($current['artist'])."$likeChar' and `title` LIKE '".addslashes($current['title'])."$likeChar'";
                $line = $this->db->getLine($query);
            }  
                        
            if (!empty($line)) {
                $filename = $line['filename'];
                $idong =  $line['idsong'];
            } else {
                $filename = "";
                $idong = "";
            }   
    
            if (
                $this->getCurrentSong() != $this->getLastTrack() and
                strpos(strtolower($this->getCurrentSong()), strtolower(SYSTEM_SYMVOL)) === false
            ) {
                $query = "INSERT INTO `tracklist` ( `title`, `filename`,  `idsong`,  `time`)
                        VALUES ('".addslashes($this->getCurrentSong())."', '".addslashes($filename)."', '$idong', '".date("U")."')";

                $this->db->queryNull($query);
            }
        }	}
?>