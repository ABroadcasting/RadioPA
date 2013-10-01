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
	class Playlist {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
		private function __construct() {			$this->db = MySql::create();
			$this->request = Request::create();
			$this->date = Date::create();
			$this->filter = Filter::create();		}

		public function handler() {
			$notices = array();			if ($this->request->hasGetVar('delete_playlist')) {				$this->deletePlaylist(intval($this->request->getGetVar('delete_playlist')));			}

			if ($this->request->hasPostVar('playlist_sort')) {
				$this->updatePlaylistSort();
			}

			if ($this->request->hasGetVar('play')) {				$notices['playSong'] =  $this->playSong($this->request->getGetVar('play'));			}

			if ($this->request->hasGetVar('del_all')) {
				$notices['deleteAllSongs'] =  $this->deleteAllSongs($this->request->getGetVar('playlist_id'));
			}

			if ($this->request->hasGetVar('delete_song')) {
				$notices['deleteSong'] =  $this->deleteSong($this->request->getGetVar('delete_song'));
			}

			if ($this->request->hasGetVar('delete_song_2')) {
				$notices['deleteSongFromAllPlaylist'] =  $this->deleteSongFromAllPlaylist($this->request->getGetVar('delete_song_2'));
			}

			if ($this->request->hasPostVar('song_sort')) {
				$this->updateSongSort();
			}

			return $notices;		}

		public function getPlaylistId() {			return intval($this->request->getGetVar('playlist_id'));		}

		public function getSongLocalPath($filename) {
			$filename = str_replace($this->request->getMusicPath(), "", $filename);
			return $this->filter->wordWrap($filename);
		}

		public function getDuration($second) {			$duration = floor($second/60).":";
			$dur_minutes = fmod($second, 60);
			if($dur_minutes < 10){				$duration .= "0".$dur_minutes;
			} else {
				$duration .= $dur_minutes;
			}

			return $duration;		}

		public function getSongs($playlistId) {			$sortArray = $this->getSortArray();
			$search = $this->getSearch();
			$start = $this->getStart();
			$limit = $this->getLimit();

            if (!empty($search)) {
				$searchWhere = "MATCH (`artist`, `title`) AGAINST ('$search') and";
                if ($this->request->hasGetVar('sort')) {
                    $sort = "ORDER BY `".$sortArray['value']."` ".$sortArray['obr'];
                } else {
                    $sort = "";
                }
			} else {				$searchWhere = "";
                $sort = "ORDER BY `".$sortArray['value']."` ".$sortArray['obr'];			}
            
			$query = "SELECT * FROM `songlist` WHERE $searchWhere `id`= $playlistId $sort LIMIT $start, $limit";
    		return $this->db->getLines($query);		}

		public function getLimit() {
			if ($this->request->hasGetVar('limit')) {
				return (int) $this->request->getGetVar('limit');
			} else {
				return 50;
			}
		}

		public function getStart() {			if ($this->request->hasGetVar('start')) {
				return (int) $this->request->getGetVar('start');
			} else {
				return 0;
			}		}

		public function getSortArray() {
			if ($this->request->hasGetVar('sort')) {
				$sort = array();				$sortString = $this->request->getGetVar('sort');
				$sortString = htmlspecialchars($sortString, ENT_QUOTES, "utf-8");
				if ($sortString[0] == "!"){
					$sort['obr'] = "DESC";
					$sort['value'] = substr($sortString, 1);
					$sort['string'] = $sortString;				} else {					$sort['obr'] = "ASC";
					$sort['value'] = $sortString;
					$sort['string'] = $sortString;				}			} else {				$sort['obr'] = "ASC";
				$sort['value'] = "sort";
				$sort['string'] = "sort";			}
			$sort['value'] = addslashes($sort['value']);
			
			return $sort;		}

		public function isSortShow($playlistId) {			$query = "SELECT * FROM `playlist` WHERE `id`= ".$playlistId;
			$playmode = $this->db->getColumn($query, 'playmode');
			if ($playmode !=1) {
				return true;
			} else {				return false;			}		}

		public function updateSongSort() {   			foreach ($this->request->getPostVar('song_sort') as $idsong => $value) {
				$query = "UPDATE `songlist` SET `sort` = ".intval($value)." WHERE `idsong`= ".intval($idsong);
	    		$this->db->queryNull($query);
			}		}

		public function deleteSongFromAllPlaylist($songid) {
			$query = "SELECT * FROM `songlist` WHERE `idsong`= ".intval($songid);
			$filename = $this->db->getColumn($query, 'filename');

			$query = "DELETE FROM `songlist` WHERE `filename` = '".addslashes($filename)."'";
			$this->db->queryNull($query);
			return "Песня удалена из всех плейлистов";
		}

		public function deleteSong($songid) {
			$query = "DELETE FROM `songlist` WHERE `idsong` = ".intval($songid);
			$this->db->queryNull($query);
			return "Песня удалена из плейлиста";
		}

		public function deleteAllSongs($playlistId) {
			$query = "DELETE FROM `songlist` WHERE `id`=".intval($playlistId);
	    	$this->db->queryNull($query);
	    	$query = "UPDATE `playlist` SET `now`=0 WHERE `id`=".intval($playlistId);
	    	$this->db->queryNull($query);
	    	return "Все треки удалены";		}

        public function playSong($id) {
			$query = " SELECT * FROM `songlist` WHERE `idsong` = ".intval($id);
			$songlist = $this->db->getLine($query);

			$query = "SELECT * FROM `zakaz`";
			$ordersCount = $this->db->getCountRow($query);
			$songName = $songlist['artist']." - ".$songlist['title']." (через админку)";
			$ip = $this->request->getServerVar('REMOTE_ADDR');

			$query = " SELECT * FROM `zakaz` WHERE `idsong` = ".intval($id);
			$line = $this->db->getLine($query);

			if (!empty($line) and $line['idsong'] == $id) {
				return "Уже добавлена в список воспроизведения.";
			}

			// заносим заказ в last_zakaz
			$query = "INSERT INTO `last_zakaz` (`track` , `time` , `skolko`  , `ip` , `idsong`, `id` )
				VALUES (
					'".addslashes($songName)."',
					'".date('U')."',
					'$ordersCount',
					'$ip',
					'".$songlist['idsong']."',
					'".$songlist['id']."'
				)";
			$this->db->queryNull($query);

			// заносим заказ в zakaz
			$query = "INSERT INTO `zakaz` (`idsong` ,`filename` , `artist` , `title` , `album` , `duration` , `admin` )
				VALUES (
					'".$songlist['idsong']."',
					'".addslashes($songlist['filename'])."',
					'".addslashes($songlist['artist'])."',
					'".addslashes($songlist['title'])."',
					'".addslashes($songlist['album'])."',
					'".$songlist['duration']."',
					'1'
				)";
			$this->db->queryNull($query);
   			return "Песня добавлена в список воспроизведения.";
        }

		public function getSearchString() {
			if ($this->request->hasPostVar('search')) {
				$search = $this->request->getPostVar('search');
			}
			if ($this->request->hasGetVar('search')) {
				$search = $this->request->getGetVar('search');
			}
			if (!empty($search)) {
				$search = htmlspecialchars($search, ENT_QUOTES, "utf-8");
				$search = str_replace("_", " ", $search);
				if (TRANSLIT == "on") {
					$search = $this->filter->translit($search);
				}
				return $search;
			} else {
				return "";
			}
		}

		public function getSearch() {
			if ($this->request->hasPostVar('search')) {				$search = $this->request->getPostVar('search');
			}
			if ($this->request->hasGetVar('search')) {
				$search = $this->request->getGetVar('search');
			}
			if (!empty($search)) {
				$search = htmlspecialchars($search, ENT_QUOTES, "utf-8");
				if (TRANSLIT == "on") {
					$search = $this->filter->translit($search);
				}
				return $search;			} else {				return "";			}		}

		public function getTitle($id) {        	$query = "SELECT * FROM `playlist` WHERE `id`=".intval($id);
			$line = $this->db->getLine($query);
			return $line['name'];		}

		public function getTimes($playlist) {			if (!empty($playlist['event1'])) {				$timesString = $this->getTimesForDjingl($playlist['event1']);			}
			if (!empty($playlist['event2'])) {
				$timesString = $this->getTimesForPlaylist($playlist['event2']);
			}
			return $timesString;		}

		private function getTimesForPlaylist($event2) {
			$returnString = "";        	$e1_res = explode(";", $event2);
				for ($k=0; $k<=2; $k++) {
					$time = explode("-", $e1_res[$k]);
				    $daysString = $this->date->toRussian($time[0]);

					$times = array();
					if ($time[1] != "00:00") {
						$times[] = "в ".$time[1];
					}
					if ($time[2] != "00:00") {
						$times[] = "в ".$time[2];
					}
					if ($time[3] != "00:00") {
						$times[] = "в ".$time[3];
					}

					$timesString = "";
					foreach ($times as $string) {						if (empty($timesString)) {							$timesString = $string;						} else {							$timesString .= ", ".$string;						}					}

				    if ((!empty($daysString)) and (!empty($timesString))) {
				    	$returnString .= "- ".$daysString.", ".$timesString."<br>";
				    }
				}
				if (!empty($returnString)) {
                	return $returnString;
                } else {
                	return "- Время не задано";
                }
		}

		private function getTimesForDjingl($event1) {
			$returnString = "";			$e1_res = explode(";", $event1);
				for ($k=0; $k<=3; $k++) {
					if (empty($e1_res[$k])) {
				        continue;
				    }
					$time = explode("-", $e1_res[$k]);
					$daysString = $this->date->toRussian($time[0]);

					$vr_1 = explode(":", $time[1]);
				    $vr_2 = explode(":", $time[2]);

				    $vr1 = ($vr_1[0]*60)+$vr_1[1];
				    $vr2 = ($vr_2[0]*60)+$vr_2[1];

				    if ($time[1] == "00:00" and $time[2] == "23:55") {				    	$timesString = "весь день";
				    } else {				    	$timesString = "с ".$time[1]." до ".$time[2];
				    }

				    $intervalString = $time[3];

				    if ( !empty($daysString) and !empty($timesString) and $intervalString != "00:00" and $vr1 < $vr2 ) {
				    	$returnString .= "- ".$daysString.", ".$timesString.", каждые ".$intervalString."<br>";
				    }
				}
				if (!empty($returnString)) {
                	return $returnString;
                } else {                	return "- Время не задано";                }		}

		public function getCountSongs($playlistId) {
			$search = $this->getSearch();
			if (empty($search)) {
				$query = "SELECT * FROM `songlist` WHERE `id` = ".$playlistId;
				return $this->db->getCountRow($query);
			} else {
				$query = "SELECT * FROM `songlist` WHERE MATCH (`artist`, `title`) AGAINST ('$search') and `id`= ".$playlistId;
				return $this->db->getCountRow($query);			}		}

		public function getPlaymode($mode) {
			switch($mode) {
				case 1: return "В случайном порядке";
				case 0: return "По порядку";
				case 2: return "Случайно один";
				case 3: return "Программа";
			}		}

		public function getList() {			$query = "SELECT * FROM `playlist` ORDER BY `sort` ASC";
			return $this->db->getLines($query);		}

		public function getCountAllSongs() {
			$ne_pokazivat = $this->getNePokazivat();        	$query = "SELECT * FROM `songlist` WHERE $ne_pokazivat";

	    	return $this->db->getCountRow($query);		}

		public function getSongsDuration($plailistId) {        	$esearch = "";
        	$search = $this->getSearch();
			if (!empty($search)) {
				$esearch = "MATCH (`artist`, `title`) AGAINST ('$search') and";
			}

			$query = "SELECT SUM(`duration`) as sum FROM `songlist` WHERE $esearch `id`=".$plailistId;
			$sum = $this->db->getColumn($query, 'sum');

			$min_dur = (int) ($sum/60);
			$ch_dur = (int) ($min_dur/60);
			$min_dur = $min_dur-($ch_dur*60);
			$minfull_dur = (int) ($sum/60);
			$sec_dur = $sum-($minfull_dur*60);

			$vsego_time = "";
			if ($ch_dur > 0) {				$vsego_time .= "$ch_dur часов ";
			}
			if ($min_dur > 0) {				$vsego_time .= "$min_dur минут";
			}
			if ($min_dur == 0 and $ch_dur ==0) {				$vsego_time .= "$sec_dur секунд";
			}
			return $vsego_time;		}

		public function getAllSongsDuration() {			$ne_pokazivat = $this->getNePokazivat();

			$query = "SELECT SUM(`duration`) as sum FROM `songlist` WHERE $ne_pokazivat";
			$sum = $this->db->getColumn($query, 'sum');

			$min_dur = (int) ($sum/60);
			$ch_dur = (int) ($min_dur/60);
			$min_dur = $min_dur-($ch_dur*60);
			$minfull_dur = (int) ($sum/60);
			$sec_dur = $sum-($minfull_dur*60);

			$vsego_time = "";
			if ($ch_dur > 0) {				$vsego_time .= "$ch_dur часов ";
			}
			if ($min_dur > 0) {				$vsego_time .= "$min_dur минут";
			}
			if ($min_dur == 0 and $ch_dur ==0) {				$vsego_time .= "$sec_dur секунд";
			}

			return $vsego_time;		}

		public function getNePokazivat() {
			$query = "SELECT * FROM `playlist` ORDER BY `sort` ASC";
			$lines = $this->db->getLines($query);

			$dont_show = array();
			foreach ($lines as $index=>$line) {
				if ($line['show'] == 0) {
					$dont_show[] = $line['id'];				}			}

			$count = count($dont_show)-1;
	
			for ($i=0; $i<=$count; $i++) {
				if (empty($ne_pokazivat)) {
					$ne_pokazivat = $dont_show[$i]." != `id`";
				} else {
					$ne_pokazivat = $ne_pokazivat." and ".$dont_show[$i]." != `id`";
				}
			}

			if (!empty($ne_pokazivat)) {
				return $ne_pokazivat;
			} else {
				return "1=1";
			}			}

		public function updatePlaylistSort() {			foreach ($this->request->getPostVar('playlist_sort') as $playlist_id => $value) {
				$query = "UPDATE `playlist` SET `sort` = ".intval($value)." WHERE `id`= ".intval($playlist_id);
		    	$this->db->queryNull($query);
			}		}
		
		public function noNowCheck() {
			$check = true;
			$query = "SELECT `now` FROM `playlist`";
			if ($this->db->getCountRow($query) == 0) {
				return false;
			}
			foreach ($this->db->getLines($query) as $line) {
				if ($line['now'] == 1) {
					$check = false;
				} 
			}
			
			return $check;
		}

		public function deletePlaylist($id) {
			$query = "DELETE FROM `playlist` WHERE `id` = ".$id;
    		$this->db->queryNull($query);
    		$query = "DELETE FROM `songlist` WHERE `id` = ".$id;
    		$this->db->queryNull($query);		}	}
?>