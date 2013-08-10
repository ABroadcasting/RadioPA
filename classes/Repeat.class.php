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
	class Repeat {
	    
        public static function create() {
            return new self();
        }
        		public function __construct() {			$this->db = MySql::create();
			$this->request = Request::create();
			$this->dateTime = Date::create();		}

		public function handler() {   			if ($this->request->hasGetVar('povtor_start')) {        		$this->getRepeatSongList();
        	}

        	if ($this->request->hasGetVar('delete_song')) {
        		$this->deletePosikSong();
        	}

        	if ($this->request->hasGetVar('delete_song2')) {
        		$this->deletePoiskSongAndFile();
        	}

        	if ($this->request->hasGetVar('delete_song3')) {
        		$this->deleteNotExistingSong();
        	}		}

		public function getRepeat() {			$return = array();			$query = "SELECT * FROM `poisk` ORDER BY `title`";
			foreach ($this->db->getLines($query) as $line) {
    			$line['filename'] = str_replace($root_path, "", $line['filename']);
    			$playlist = $this->getPlaylistBySong($line['id']);
     			$line['playlistName'] = $playlist['name'];
     			$line['duration'] = $this->dateTime->toMinSec($line['duration']);
     			$return[] = $line;			}
			return $return;
		}

		public function getNotExisting() {			$query = "SELECT * FROM `songlist` ORDER BY `sort` ASC";
   			$lines = $this->db->getLines($query);
 			$return = array();
   			foreach ($lines as $line) {
   				if (!file_exists($line['filename'])) {
     				$line['filename'] = str_replace($this->request->getMusicPath(), "", $line['filename']);
     				$playlist = $this->getPlaylistBySong($line['id']);
     				$line['playlistName'] = $playlist['name'];
     				$return[] = $line;
     			}			}
			return $return;
		}

		public function deleteNotExistingSong() {            $del_id = $_GET['delete_song3'];
   			$query2 = "DELETE FROM `songlist` WHERE `idsong` = '$del_id'";
    		$this->db->queryNull($query2);
		}

		public function deletePoiskSongAndFile() {			$query = "SELECT * FROM `poisk` WHERE `idsong` = ".$_GET['delete_song2'];
    		$poisk = $this->db->getLine($query);
    		$del_filename = $poisk['filename'];
    		$del_title = $poisk['title'];
    		$del_artist = $poisk['artist'];

  			$query = "SELECT * FROM `poisk` WHERE `title` = '".addslashes($del_title)."' and `artist` = '".addslashes($del_artist)."'";
    		foreach ($this->db->getLines($query) as $line) {
		    	$del_id = $line['idsong'];
   				$query2 = "DELETE FROM `poisk` WHERE `idsong` = '$del_id'";
    			$this->db->queryNull($query2);
    		}

    		$query = "DELETE FROM `songlist` WHERE `filename` = '".addslashes($del_filename)."'";
    		$this->db->queryNull($query);

    		unlink($del_filename);		}

		public function deletePosikSong() {
			$query = "SELECT * FROM `poisk` WHERE `idsong` = ".$_GET['delete_song'];
   			$poisk = $this->db->getLine($query);
    		$del_title  = $poisk['title'];
   			$del_artist  = $poisk['artist'];

  			$query = "SELECT * FROM `poisk` WHERE `title` = '".addslashes($del_title)."' and `artist` = '".addslashes($del_artist)."'";
    		foreach ($this->db->getLines($query) as $line) {
	   			$del_id = $line['idsong'];
   				$query2 = "DELETE FROM `poisk` WHERE `idsong` = '$del_id'";
    			$this->db->queryNull($query2);
    		}
		}

		public function getRepeatSongList() {			$this->deleteOldResult();
			$this->getPrepeat();
			header('Location:?povtor=yes');		}

		public function getPrepeat() {
			$k = 0;
			$query = "SELECT * FROM `songlist` order by `title`";
  			foreach ($this->db->getLines(($query)) as $line) {
    			if ($k == 0) {
        			$title0 = strtolower($line['title']);
    				$artist0 = strtolower($line['artist']);
    				$title_0 = $line['title'];          $dur_0 = $line['duration'];
    				$artist_0 = $line['artist'];        $ids_0 = $line['idsong'];
    				$id_0 = $line['id'];                $fl_0 = $line['filename'];
    			}

    			if ($k == 1) {
    				$title1 = $title0;
    				$artist1 = $artist0 ;
    				$title_1 = $title_0;          $dur_1 = $dur_0;
	    			$artist_1 = $artist_0;        $ids_1 = $ids_0;
    				$id_1 = $id_0;                $fl_1 = $fl_0;

        			$title0 = strtolower($line['title']);
    				$artist0 = strtolower($line['artist']);
    				$title_0 = $line['title'];          $dur_0 = $line['duration'];
    				$artist_0 = $line['artist'];        $ids_0 = $line['idsong'];
    				$id_0 = $line['id'];                $fl_0 = $line['filename'];
    			}

    			if ($k == 2) {
    				$title2 = $title1;
    				$artist2 = $artist1 ;
    				$title_2 = $title_1;          $dur_2 = $dur_1;
    				$artist_2 = $artist_1;        $ids_2 = $ids_1;
    				$id_2 = $id_1;                $fl_2 = $fl_1;

    				$title1 = $title0;
    				$artist1 = $artist0 ;
    				$title_1 = $title_0;          $dur_1 = $dur_0;
	    			$artist_1 = $artist_0;        $ids_1 = $ids_0;
    				$id_1 = $id_0;                $fl_1 = $fl_0;

        			$title0 = strtolower($line['title']);
    				$artist0 = strtolower($line['artist']);
    				$title_0 = $line['title'];          $dur_0 = $line['duration'];
    				$artist_0 = $line['artist'];        $ids_0 = $line['idsong'];
    				$id_0 = $line['id'];                $fl_0 = $line['filename'];
    			}

    			if ($k == 3) {
    				$title3 = $title2;
    				$artist3 = $artist2 ;
	    			$title_3 = $title_2;          $dur_3 = $dur_2;
    				$artist_3 = $artist_2;        $ids_3 = $ids_2;
    				$id_3 = $id_2;                $fl_3 = $fl_2;

	    			$title2 = $title1;
    				$artist2 = $artist1 ;
    				$title_2 = $title_1;          $dur_2 = $dur_1;
    				$artist_2 = $artist_1;        $ids_2 = $ids_1;
    				$id_2 = $id_1;                $fl_2 = $fl_1;

    				$title1 = $title0;
    				$artist1 = $artist0 ;
    				$title_1 = $title_0;          $dur_1 = $dur_0;
    				$artist_1 = $artist_0;        $ids_1 = $ids_0;
    				$id_1 = $id_0;                $fl_1 = $fl_0;

	        		$title0 = strtolower($line['title']);
	    			$artist0 = strtolower($line['artist']);
    				$title_0 = $line['title'];          $dur_0 = $line['duration'];
    				$artist_0 = $line['artist'];        $ids_0 = $line['idsong'];
    				$id_0 = $line['id'];                $fl_0 = $line['filename'];
        			$k = 0;
    			}
    			$k++;

				//Первая проверка
    			if ( ($title0 == $title1) and ($artist0 == $artist1) and ($id_0 == $id_1) )  {
    				$query3 = "INSERT INTO `poisk` ( `title` , `artist` , `id`, `filename`, `idsong`, `duration` )
    					VALUES ( '".addslashes($title_0)."', '".addslashes($artist_0)."', '$id_0', '".addslashes($fl_0)."', '$ids_0', '$dur_0' )";
					$this->db->queryNull($query3);
					$query4 = "INSERT INTO `poisk` ( `title` , `artist` , `id`, `filename`, `idsong`, `duration` )
    					VALUES ( '".addslashes($title_1)."', '".addslashes($artist_1)."', '$id_1', '".addslashes($fl_1)."', '$ids_1', '$dur_1' )";
					$this->db->queryNull($query4);
    			}

    			//Вторая проверка
    			if ( ($title0 == $title2) and ($artist0 == $artist2) and ($id_0 == $id_2) )  {
    				$query3 = "INSERT INTO `poisk` ( `title` , `artist` , `id`, `filename`, `idsong`, `duration` )
    					VALUES ( '".addslashes($title_0)."', '".addslashes($artist_0)."', '$id_0', '".addslashes($fl_0)."', '$ids_0', '$dur_0' )";
					$this->db->queryNull($query3);
					$query4 = "INSERT INTO `poisk` ( `title` , `artist` , `id`, `filename`, `idsong`, `duration` )
    					VALUES ( '".addslashes($title_2)."', '".addslashes($artist_2)."', '$id_2', '".addslashes($fl_2)."', '$ids_2', '$dur_2' )";
					$this->db->queryNull($query4);
    			}

    			//Третяя проверка
	    		if ( ($title0 == $title3) and ($artist0 == $artist3) and ($id_0 == $id_3) )  {
    				$query3 = "INSERT INTO `poisk` ( `title` , `artist` , `id`, `filename`, `idsong`, `duration` )
    					VALUES ( '".addslashes($title_0)."', '".addslashes($artist_0)."', '$id_0', '".addslashes($fl_0)."', '$ids_0', '$dur_0' )";
					$this->db->queryNull($query3);
					$query4 = "INSERT INTO `poisk` ( `title` , `artist` , `id`, `filename`, `idsong`, `duration` )
    					VALUES ( '".addslashes($title_3)."', '".addslashes($artist_3)."', '$id_3', '".addslashes($fl_3)."', '$ids_3', '$dur_3' )";
					$this->db->queryNull($query4);
    			}
    		}
		}

		public function getPlaylistBySong($id) {			$query = "SELECT * FROM `playlist` WHERE ".$id." = `id`";
			return $this->db->getLine($query);		}

		public function deleteOldResult() {
			$query = "DELETE FROM `poisk`";
			$this->db->queryNull($query);
		}	}
?>