<?php
	class Song {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
		private function __construct() {			$this->db = MySql::create();
			$this->request = Request::create();
			$this->filter = Filter::create();
			include($this->request->getRadioPath().'getid3/getid3.php');
			$this->getid3 = new getID3();
			include($this->request->getRadioPath().'getid3/write.php');
			$this->tagwriter = new getid3_writetags();		}
        	
		public function setSettingsIdv1() {
			$this->getid3->option_tag_apetag = false;
			$this->getid3->option_tag_lyrics3 = false;
			$this->getid3->option_tags_process = false; 
			$this->getid3->option_tags_html = false; 
			$this->getid3->encoding        = "UTF-8";
			$this->getid3->encoding_id3v1  = "ISO-8859-1";
			$this->tagwriter->tag_encoding = "ISO-8859-1";
			$this->tagwriter->tagformats   = array('id3v1');
		}
		
		public function setSettingsIdv2() {
			$this->getid3->option_tag_apetag = false;
			$this->getid3->option_tag_lyrics3 = false;
			$this->getid3->option_tags_process = false; 
			$this->getid3->option_tags_html = false; 
			$this->getid3->encoding        = "UTF-8";
			$this->getid3->encoding_id3v2  = "UTF-8";
			$this->tagwriter->tag_encoding = "UTF-8";
			$this->tagwriter->tagformats   = array('id3v2.4');
		}

		public function handler() {
			$notice = array();			if ($this->request->hasPostVar('submit')) {
                $notice['error'] = $this->saveSong();			}
			if ($this->request->hasPostVar('submit_and_save')) {
				$notice['error'] = $this->saveSong();
				if (empty($notice['error'])) {
					$this->goToPlaylist();
				}
			}
			return $notice;		}

        public function saveSong() {
            $filename = $this->filterFilename($this->request->getPostVar('filename'));
            $new_filename = $this->renameFilename($filename);
                
            $TagData_id3v2 = $this->getTagData();
                
            $TagData_id3v1['title'][0] = $this->filter->tagForId3v1($TagData_id3v2['title'][0]);
            $TagData_id3v1['artist'][0] = $this->filter->tagForId3v1($TagData_id3v2['artist'][0]);
            $TagData_id3v1['album'][0] = $this->filter->tagForId3v1($TagData_id3v2['album'][0]);
                
            $this->setSettingsIdv1();
            $this->setTagData($TagData_id3v1);
            $this->writeData($new_filename);
                
            $this->setSettingsIdv2();
            $this->setTagData($TagData_id3v2);
            $this->writeData($new_filename);
            
            $this->updateSong();
                
            return $this->getError();
        }

		public function getFormTags($tagName) {
			$info = $this->getid3->info;
			
			if (!empty($info['id3v2']['comments'])) {
				$tags = $info['id3v2']['comments'];
				if(!empty($tags[$tagName]))
        		foreach ($tags[$tagName] as $tag) {
        			if (empty($v2)) {
        				$v2 = $tag;
        			} else {
        				$v2 = ", ".$tag;
        			}
        		}
			}
			
			if (!empty($info['id3v1']['comments'])) {
				$tags = $info['id3v1']['comments'];
				if(!empty($tags[$tagName]))
        		foreach ($tags[$tagName] as $tag) {
        			if (empty($v1)) {
        				$v1 = $tag;
        			} else {
        				$v1 = ", ".$tag;
        			}
        		}
			}
            
            if (defined('ID3V1_CHARSET')) {
                $v1 = @iconv(ID3V1_CHARSET, 'UTF-8', $v1);
            }
			
			if (!empty($v2)) {
				return $v2;
			}
			
			if (!empty($v1)) {
				return $v1;
			}		
			
			if ($tagName == 'artist' or $tagName == 'title') {				return "???";
			} else {
				return "";
			}		}
		
		public function getDuration() {
		    $duration = explode(":", $this->getid3->info['playtime_string']);
            
            if (count($duration) == 1) {
                return $duration[0];
            }
            
            if (count($duration) == 2) {
                return $duration[0]*60+$duration[1];
            }

            if (count($duration) == 3) {
                return $duration[0]*60*60+$duration[1]*60+$duration[2];
            } 		}
		
		private function getTags() {
			if (!empty($this->getid3->info['id3v2']['comments'])) {
		 		return $this->getid3->info['id3v2']['comments'];
			}
			if (!empty($this->getid3->info['id3v1']['comments'])) {
				return $this->getid3->info['id3v1']['comments'];	
			}							}

		public function analyze($filename) {			$this->getid3->Analyze($filename);		}

		private function goToPlaylist() {
		    if ($this->request->getGetVar('playlist_id') == "povtor") {                header("location: playlist_view.php?playlist_id=povtor");
		    } else {				header("location: playlist_view.php?playlist_id=".$this->request->getGetVar('playlist_id').
					"&sort=".$this->request->getGetVar('sort').
					"&start=".$this->request->getGetVar('start').
					"&search=".$this->request->getGetVar('search')
				);
			}		}

		private function getError() {
			$error = array();			if (!empty($tagwriter->errors)) {
				$error[] =  'There were some errors:<br>'.implode('<br><br>', $this->tagwriter->errors);
			}
			if (!empty($tagwriter->warnings)) {
				$error[] =  'There were some warnings:<br>'.implode('<br><br>', $this->tagwriter->warnings);
			}

			return $error;		}

		private function updateSong() {
			$tag_data = $this->tagwriter->tag_data;
        	$query = "UPDATE `songlist` SET
        		`title` = '".addslashes($tag_data['TITLE'][0])."',
        		`artist` = '".addslashes($tag_data['ARTIST'][0])."',
        		`album` = '".addslashes($tag_data['ALBUM'][0])."',
				`sort` = '".addslashes($this->request->getPostVar('sort'))."',
				`zakazano` = '".addslashes($this->request->getPostVar('zakazano'))."'
				WHERE `filename`= '".addslashes($this->tagwriter->filename)."'";
    		$this->db->queryNull($query);

			$query = "UPDATE `songlist` SET `id` = '".$this->request->getPostVar('position')."' WHERE `idsong`= ".$this->request->getGetVar('edit_song');
		    $this->db->queryNull($query);		}

		public function setTagData($TagData) {			$this->tagwriter->tag_data = $TagData;		}

		public function writeData($filename) {
			$this->tagwriter->filename = $filename;			$this->tagwriter->WriteTags();		}

		private function getTagData() {
			$title = trim($this->request->getPostVar('title'));
			$artist = trim($this->request->getPostVar('artist'));
			$album = trim($this->request->getPostVar('album'));

			$title = $this->filter->cleanTag($title);
			$artist = $this->filter->cleanTag($artist);
			$album = $this->filter->cleanTag($album);

			if (TRANSLIT == "on") {
				$title = $this->filter->translit($title);
				$artist = $this->filter->translit($artist);
				$album = $this->filter->translit($album);
			}
            
            if (empty($title)) {
                $title = "???";
            }
            
            if (empty($artist)) {
                $artist = "???";
            }
            
			return array(
				'title' => array($title),
				'artist' => array($artist),
				'album' => array($album)
			);		}

		private function filterFilename($new_filename) {			$new_filename = str_ireplace("'", "", $new_filename);
    		$new_filename = str_ireplace("\"", "", $new_filename);
    		$new_filename = str_ireplace("&", "and", $new_filename);
			$new_filename = str_ireplace("   ", " ", $new_filename);
			$new_filename = str_ireplace("  ", " ", $new_filename);

			if (TRANSLIT == "on") {				$new_filename = $this->filter->translit($new_filename);
			}

			$new_filename = htmlspecialchars($new_filename, ENT_QUOTES, "utf-8");

			return $new_filename;		}

		private function renameFilename($new_filename) {			$line = $this->getSong($this->request->getGetVar('edit_song'));
			$new_filename = $this->request->getMusicPath().$this->request->getPostVar('folder')."/".$new_filename;
			rename($line['filename'], $new_filename);

			$query = "UPDATE `songlist` SET `filename` = '".addslashes($new_filename)."' WHERE `filename`= '".addslashes($line['filename'])."'";
    		$this->db->queryNull($query);

    		return $new_filename;		}

		public function getPlaylistList() {			$query = "SELECT * FROM `playlist`";
    		return $this->db->getLines($query);		}

		public function getSong($songId) {			$query = "SELECT * FROM `songlist` WHERE `idsong` = ".$songId;
            return $this->db->getLine($query);		}

		public function getPlayerPath($filename) {			$player_filename = str_replace($this->request->getMusicPath(), "", $filename);
			$player_filename = "/music/".$player_filename;
			$player_filename = urlencode($player_filename);
			
			return $player_filename;		}

		public function getFilename($filename) {
			$pos_vhoh = strrpos($filename, "/")+1;
			$dlina = strlen($filename);
 			$put = substr($filename, 0, $pos_vhoh);
 			$imya_fayla = substr($filename, $pos_vhoh, $dlina);
 			return $imya_fayla;		}

		public function getFolder($filename) {
            $prv_a3 = str_replace($this->request->getMusicPath(), "", $filename);
            $arr = explode("/", $prv_a3);
            return $arr[count($arr)-2];
        }

		public function getFolderList() {			$hdl = opendir($this->request->getMusicPath());
        	while ($file = readdir($hdl)) {
				if (($file!="..")&&($file!=".")) {
					if ( is_dir($this->request->getMusicPath()."/".$file) === true ) {
  	    				$a3[]=$file;
  	    			}
  	    		}
 			}
 			sort($a3);
 			return $a3;		}

		public function getNextSort($playlistId) {
			if (empty($this->sort)) {				$query = "SELECT * FROM `songlist` WHERE `id`=".$playlistId." ORDER BY `sort` DESC LIMIT 0 , 1";
	    		$line = $this->db->getLine($query);
				$this->sort = $line['sort']+1;
				return $this->sort;
			} else {
				$this->sort++;	
	    		return $this->sort;
			}			}	}
?>