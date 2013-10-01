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
	class AddTracks {
	    
        public static function create(Song $song) {
            return new self($song);
        }
        
		private function __construct(Song $song) {			$this->request = Request::create();
			$this->db = MySql::create();
			$this->ssh = Ssh::create();
			$this->manager = Meneger::create();
			$this->song = $song;
			$this->filter = Filter::create();		}

		public function setPlaylist($id) {			$this->id = $id;		}

		public function addFolder($folder) {        	$folder = $this->getRealPath($folder);
        	$tracks_array = $this->getAllFilesFromDirectory($folder);
         	foreach ($tracks_array as $filename) {
         		$this->addTrack($filename);         	}		}

		public function addTrack($filename) {			$playlistId = $this->request->getGetVar('playlist_id');			$filenameTemp = $this->getRealPath($filename);

			if (!$this->manager->isMp3($filenameTemp)) {				return false;			}
			$filename = $this->filter->cleanFileName($filenameTemp);

			if ($this->isAlreadyExists($filename)) {				return false;			}

            if ($filenameTemp != $filename) {            	rename($filenameTemp, $filename);            }
			
			if (!$TagData = $this->getTagDataFromOftherPlaylist($filename)) {
				$this->song->analyze($filename);

				$artist = $this->song->getFormTags('artist');
				$title = $this->song->getFormTags('title');
				$album = $this->song->getFormTags('album');
				$genre = $this->song->getFormTags('genre');
				$year = $this->song->getFormTags('year');
	
				$dur = $this->song->getDuration();
	
				if ($artist == "???" and $title == "???") {
					$tags = $this->getTagsFrom($filename);
					$artist = $tags['artist'][0];
					$title = $tags['title'][0];
				}
                
                $TagData_id3v1['title'][0] = $this->filter->tagForId3v1($title);
                $TagData_id3v1['artist'][0] = $this->filter->tagForId3v1($artist);
                $TagData_id3v1['album'][0] = $this->filter->tagForId3v1($album);
                $TagData_id3v1['genre'][0] = $this->filter->tagForId3v1($genre);
                $TagData_id3v1['year'][0] = $year;
	
				$TagData_id3v2['title'][0] = $this->filter->cleanTag($title);
				$TagData_id3v2['artist'][0] = $this->filter->cleanTag($artist);
				$TagData_id3v2['album'][0] = $this->filter->cleanTag($album);
				$TagData_id3v2['genre'][0] = $this->filter->cleanTag($genre);
				$TagData_id3v2['year'][0] = $year;
                
                $TagData = $TagData_id3v2;
				
				$this->song->setSettingsIdv1();
                $this->song->setTagData($TagData_id3v1);
				$this->song->writeData($filename);
				
				$this->song->setSettingsIdv2();
                $this->song->setTagData($TagData_id3v2);
				$this->song->writeData($filename);
			}
			
			if (!empty($TagData['dur'])) {
				$dur = $TagData['dur'];
				unset($TagData['dur']);
			}

			if ( !empty($this->tagwriter->errors)) {
				if (!empty($this->tagwriter->errors)) {
					echo '<p>Ошибка чтения mp3-файла:<br>'.implode('<br><br>', $tagwriter->errors)."</p>";
				}
				exit;
			}

			// Проверяем на наличие песени в других плейлистах, что бы у одинаковых песен были одинаковые zakazano значения
			$query_zakazano = "SELECT * FROM `songlist` WHERE `filename`='".addslashes($filename)."'";
			$zakazano = $this->db->getColumn($query_zakazano, 'zakazano');

			$query = "INSERT INTO `songlist`
					( `id` , `filename` , `artist` , `title` , `album` , `genre` , `albumyear`, `duration` , `sort` , `zakazano`)
						VALUES
					(
						'".$playlistId."',
						'".addslashes($filename)."',
						'".addslashes($TagData['artist'][0])."',
						'".addslashes($TagData['title'][0])."',
						'".addslashes($TagData['album'][0])."',
						'".addslashes($TagData['genre'][0])."',
						'".addslashes($TagData['year'][0])."',
						'".$dur."',
						'".$this->song->getNextSort($playlistId)."',
						'".$zakazano."'
					)";

			$this->db->queryNull($query);

			$query = "UPDATE `songlist` SET `artist` = '".addslashes($TagData['artist'][0])."', `title` = '".addslashes($TagData['title'][0])."' WHERE `filename`= '".addslashes($filename)."'";
	    	$this->db->queryNull($query);		}

		private function getTagDataFromOftherPlaylist($filename) {
			$query = "SELECT * FROM `songlist` WHERE `filename`='".addslashes($filename)."'";
			$line = $this->db->getLine($query);
			
			if (empty($line)) {
				return false;
			}
			
			$TagData['title'][0] = $line['title'];
			$TagData['artist'][0] = $line['artist'];
			$TagData['album'][0] = $line['album'];
			$TagData['genre'][0] = $line['genre'];
			$TagData['year'][0] = $line['albumyear'];
			$TagData['dur'] = $line['duration'];
			
			return $TagData;
		}

		private function getTagsFrom($filename) {
			$name = substr($filename, strrpos($filename, "/"));
			$name = str_replace(".mp3", "", $name);
			$name = str_replace("/", "", $name);

			preg_match("/^([^$]+) - ([^$]+)$/", $name, $matches);

			if (!empty($matches[1])) {
				$artist = $matches[1];
			}
			if (!empty($matches[2])) {
				$title = $matches[2];
			}

			if (empty($artist) and empty($title)) {
				preg_match("/^([^$]+)_-_([^$]+)$/", $name, $matches);
				if (!empty($matches[1])) {
					$artist = $matches[1];
				}
				if (!empty($matches[2])) {
					$title = $matches[2];
				}
			}
			if (empty($artist) and empty($title)) {
				preg_match("/^([^$]+)-([^$]+)$/", $name, $matches);
				if (!empty($matches[1])) {
					$artist = $matches[1];
				}
				if (!empty($matches[2])) {
					$title = $matches[2];
				}
			}

			if (empty($if0artist) and empty($title)) {
				$title = $name;
				$artist = "???";
			}

			$return = array();
			$return['artist'][0] = $artist;
			$return['title'][0] = $title;

			return $return;		}

		public function isAlreadyExists($filename) {			$query = "SELECT * FROM `songlist` "
				." WHERE `id`=".$this->id." "
				." AND `filename`='".addslashes($filename)."' ";
			$line = $this->db->getLine($query);
			if (!empty($line)) {				return true;			} else {				return false;			}		}

		public function getRealPath($filename) {			return realpath(stripslashes($filename));		}

		public function setChmod() {			if ($this->request->hasGetVar('filename')) {
				$folder_chmod = $this->request->getGetVar('filename');
				$pos_vhoh = strrpos($folder_chmod, "/");
				$folder_chmod = substr($folder_chmod, 0, $pos_vhoh);
			}
			if ($this->request->hasGetVar('add_directory')) {
				$folder_chmod = $this->request->getGetVar('add_directory');
				$folder_chmod = str_replace("%2", "/", $folder_chmod);
			}
			if (file_exists($folder_chmod) and !empty($folder_chmod)) {
				$this->ssh->getResponse("chmod -R 777 '".$folder_chmod."'");
			}		}

		public function getAllFilesFromDirectory($root_dir) {
			$folders_array = array();
			$files_array = array();

			if (is_dir($root_dir)) {
			    if ($dir = opendir($root_dir)) {
			        while (false !== ($file = readdir($dir))) {
			            if ($file != "." && $file != "..") {
			                if (is_dir($root_dir."/".$file)) {
			                    $folders_array[] = $root_dir."/".$file;
			                } else {
			                    $files_array[] = $root_dir."/".$file;
			                }
			            }

			        }
			    }
			    closedir($dir);
			}

			if ($folders_array) {
				foreach ($folders_array as $folder) {
					$files_array = array_merge($files_array, $this->getAllFilesFromDirectory($folder));
				}
			}

			return $files_array;
		}	}
?>