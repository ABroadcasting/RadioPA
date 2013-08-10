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
	class Meneger {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
		private function __construct() {			$this->request = Request::create();
			$this->db = MySql::create();
			$this->ssh = Ssh::create();		}

		public function handler() {
            $folder = $this->getFolder();
            $begin = $this->getBegin();
            $fl = $this->getPostDecode('fl');
            
			if ((strpos($folder, $begin)!=0)||(strpos($folder,"..")!==false)) {
				exit;
			}

			$this->setChmod();
        	if ($this->isUdal()) {
            	foreach ($this->getPostDecode('fl') as $i) {
                	$this->delfiles($folder."/".$i);
				}        	}

        	if ($this->isMakeDir()) {
        		$this->makeDir();
		    }

		    if ($this->isRename()) {
        		$this->rename();
		    }

		    if ($this->isCopy()) {
		    	$rd = $this->request->getPostVar('rd');		    	foreach ($fl as $i) {
					$this->copyfiles($folder, $i, $rd);
				}		    }

		    if ($this->isMove()) {
		   		$rd = $this->request->getPostVar('rd');		    	foreach ($fl as $i) {
					if (!$this->fileexits($rd."/",$i)) {
						$this->movefiles($folder, $i, $rd);
					}
				}		    }		}

		public function zaprosHandler() {			if ($this->request->hasPostVar('search_button')) {
				$search = $this->request->getPostVar('search');
				$folder = $this->getFolder();
				$playlist_id_get = $this->getPlaylistId();
				Header ("Location: manager.php?fold=$folder&search=$search&playlist_id=$playlist_id_get");
			}		}

		public function getStart() {			if ($this->request->hasGetVar('start')) {
				return $this->request->getGetVar('start');
			} else {
				return false;
			}		}

		public function getPlaylistId() {
			if ($this->request->hasGetVar('playlist_id')) {
				return (int) $this->request->getGetVar('playlist_id');
			} else {
				return false;
			}
		}

		public function getPostDecode($var) {			if ($this->request->hasPostVar($var)) {
				$fl = $this->request->getPostVar($var);
				if (!empty($fl)) {
				    foreach ($fl as $k=>$v) {
				        $fl[$k] = urldecode($v);
				    }
					return $fl;
				} else {					return array();				}
			} else {
				return array();
			}		}

		public function isUdal() {
			if ($this->request->hasPostVar('udal_x')) {
				return true;
			} else {
				return false;
			}
		}

		public function isCopy() {
			if ($this->request->hasPostVar('copy_x')) {
				return true;
			} else {
				return false;
			}
		}

		public function isMove() {
			if ($this->request->hasPostVar('move_x')) {
				return true;
			} else {
				return false;
			}
		}

		public function isRename() {
			if ($this->request->hasPostVar('ren_x')) {
				return true;
			} else {
				return false;
			}
		}

		public function isMakeDir() {
			if ($this->request->hasPostVar('md_x')) {
				return true;
			} else {
				return false;
			}
		}

		public function getFolder() {
			if ($this->request->hasGetVar('folder')) {
				$fold = $this->request->getGetVar('folder');
				return str_replace("%20", " ", $fold);
			} else {
				return false;
			}
		}

		public function getFold() {
			if ($this->request->hasGetVar('fold')) {
				$fold = $this->request->getGetVar('fold');
				return str_replace("%20", " ", $fold);
			} else {
				return false;
			}
		}

		public function getSearch() {
			if ($this->request->hasGetVar('search')) {
				$search = $this->request->getGetVar('search');
				$search = strtolower($search);
				return htmlspecialchars($search, ENT_QUOTES, "utf-8");
			} else {
				return false;
			}
		}

		public function getBegin() {			return substr($this->request->getMusicPath(), 0, -1);		}

		public function getDirct2() {			$dirct = $this->getDirct();
			if ($dirct) {				$dirct2 = "/music".$dirct;
  				return str_replace($this->getBegin(), "", $dirct2);
  			} else {  				return false;  			}		}

		public function getDirct() {
			$begin = $this->getBegin();
			$fold = $this->getFold();
			if ((strpos($fold, $begin) != 0) || (strpos($fold, "..") != false) ||($fold == "")) {
				$dirct = $begin;
			} else {
				$dirct = $fold;
			}

			if (strpos($fold, $begin) === false) {				$dirct = $begin;			}


			return $dirct;		}

		public function getBack() {			$dirct = $this->getDirct();			if ($dirct != $this->getBegin()) {
				return substr ($dirct, 0, strrpos($dirct, "/"));
			} else {				return false;			}
		}

		public function getPlaylistName($id) {			$query = "SELECT * FROM `playlist` WHERE `id`=".$id;
			return $this->db->getColumn($query, 'name');		}

		public function getList() {
			$dirct = $this->getDirct();
			$search = $this->getSearch();

			$hdl = opendir($dirct);

			$a3 = array();
			$a = array();
			if (!$search) {
				while ($file = readdir($hdl)) {
					if (($file!="..")&&($file!=".")) {
			  	      	if ($file != "index.html") {			  	      		$a3[] = $file;
			  	      	}
			  	    }
			 	}
			} else {
				while ($file = readdir($hdl)) {
					if (($file!="..")&&($file!=".")) {
						$file_search = strtolower($file);
			  	      	$naydeno = strpos($file_search,$search);
			  	      	if ( ($naydeno !== false) /* or ($dirct==$begin) */ ) {
			  	      		if ($file != "index.html") $a3[]=$file;
			  	      	}
			  	    }
			 	}
			}
			closedir($hdl);

			foreach ($a3 as $k=>$v) {
				if ($v!="") {
					if (is_dir($dirct."/".$v) === true) {
						$a3_folder[] = $v;
					} else {
						$a3_file[] = $v;
					}
				}
				unset($a3[$k]);
			}

			if (!empty($a3_folder)) {
				asort($a3_folder);
			}

			if (!empty($a3_file)) {
				asort($a3_file);
			}

            if (!empty($a3_folder)) {
				foreach ($a3_folder as $k=>$v) {
					if ($v!="") $a3[] = $v;
				}
			}
			if (!empty($a3_file)) {
				foreach ($a3_file as $k=>$v) {
					if ($v!="") $a3[] = $v;
				}
			}

			$a2vsego = count($a3);
			$a2start = 0;
			$a2end = 51;
			$a2limit = 51;

			if ($this->request->hasGetVar('start')) {
				$a2start = $this->request->getGetVar('start');
				$a2end = $a2start+$a2limit;
			}

			$ips = $a2start+1;
			$a2 = array();
			foreach ($a3 as $k) {
				$a2[] = $k;
			}
			$r = 0;
			for ($p=$a2start; $p<$a2end; $p++) {
				if (!empty($a2[$p])) {
					$a[$r] = $a2[$p];
				}
				$r++;
			}
			$list['list'] = $a;
			$list['start'] = $a2start;
			$list['end'] = $a2end;
			$list['limit'] = $a2limit;
			$list['vsego'] = $a2vsego;

			return $list;		}

		public function isMp3($filename) {
			if (strtolower(substr($filename, -4)) == ".mp3") {				return true;			} else {				return false;			}		}

		public function isTempUpload($fullPath) {			if (
				TEMP_UPLOAD != "" and 
				strpos($fullPath, "/music/".TEMP_UPLOAD) !== false
			) {
				return true;
			} else {				return false;			}		}

		public function getFileFolder($filename) {
			$pos_vhoh = strrpos($filename, "/");
			return substr($filename, 0, $pos_vhoh);		}

		public function getFilesize($filename) {			$k_size = filesize($filename);
    		return intval($k_size / 1024)." Кб";		}

		public function getUseIn($full) {			$query = "SELECT * FROM `songlist` WHERE `filename` = '".addslashes($full)."'";
		    foreach ($this->db->getLines($query) as $line) {
		        if (empty($playlist_id)) {
		        	$playlist_id = "`id` = ".$line['id'];
		        } else {
		        	$playlist_id .= " or `id` = ".$line['id'];
		        }
		    }

			if (!empty($playlist_id)) {
			    $query = "SELECT * FROM `playlist` WHERE $playlist_id";
			    foreach ($this->db->getLines($query) as $line) {
			        if (empty($playlist_name)) {
			        	$playlist_name = $line['name'];
			        } else {
			        	$playlist_name .= ", ".$line['name'];
			        }
			    }
			     return $playlist_name;
		    } else {		    	return "";		    }		}

		public function getTree($fld) {
			$return = array();
			$folder = $this->getFolder();
			$hdl = opendir($fld);
			while ($file = readdir($hdl)) {
				if (($file!=".")&&($file!="..")){
			  		$fllnm = $fld."/".$file;
			  		if (is_dir($fllnm) === true) {
			    		$no = 0;
			    		foreach ($this->getPostDecode('fl') as $i)  {
			    			if ($fllnm==$folder."/".$i) {
			       				$no = 1;
			       			}
			      		}
			      		if ($no == 0) {
			        		if ($fllnm != $folder) {
			         			$fllnm2 = str_replace($this->request->getMusicPath(), "", $fllnm);
			         			$return[$fllnm2] = $fllnm;
			        		}
			        	}
			    	}
				}
			}
			closedir($hdl);
			return $return;		}

		private function delfiles($fld) {
			if (!is_dir($fld)) {
				unlink ($fld);
				return true;
			}

			$hdl = opendir($fld);
			while ($file = readdir($hdl)) {
				if (($file != ".")&&($file != "..")) {
					if (is_dir($fld."/".$file)) {
			        	$this->delfiles($fld."/".$file);
						rmdir($fld."/".$file);
					} else {
						unlink($fld."/".$file);
					}
				}
			}
			closedir($hdl);
			rmdir($fld."/".$file);
		}

		private function fileexits($in, $in2) {
	 		$hdl = opendir($in);
			while ($file = readdir($hdl)) {
				if (strtolower($file) == strtolower($in2)) {
					return true;
				} else {					return false;				}
			}
		}

		private function makeDir() {
			$newname = $this->request->getPostVar('newname');			$folder = $this->getFolder();			$newname2 = strtr($newname, " []{},/\!@#$%^&*'\"", "____________________");
			$newname = $folder."/".strtr($newname, " []{},/\!@#$%^&*'\"", "____________________");
			if (!$this->fileexits($folder."/", $newname2)) {
				mkdir ($newname, 0777);
			}		}

		private function rename() {
			$afl = $this->getPostDecode('afl');
			$rfl = $this->request->getPostVar('rfl');
			$folder = $this->getFolder();			for ($i = 0; $i < sizeof($afl); $i++) {
				if (($rfl[$i] != "")&($rfl[$i] != $afl[$i])& (strpos($afl[$i], "..")==False)) {
					$file_name_tmp = $folder."/".$afl[$i];
					$new_filename = $folder."/".$rfl[$i];
					$prfl = false;
					$prfl = $this->fileexits($folder."/", $rfl[$i]);
					if (strtolower($file_name_tmp) == strtolower($new_filename)) {
						rename($file_name_tmp, $new_filename);
					}
					if ($prfl !== true) {
						rename($file_name_tmp, $new_filename);
					}
					$new_filename = addslashes($new_filename);
					$file_name_tmp = addslashes($file_name_tmp);
					$query = "UPDATE `songlist` SET `filename` = '$new_filename' WHERE `filename`= '".addslashes($file_name_tmp)."'";
					$this->db->queryNull($query);
				}
			}		}

		private function copyfiles($rt, $fld, $tgt) {
			$folder = $this->getFolder();
			$rd = $this->request->getPostVar('rd');
			if (!is_dir($folder."/".$fld)) {
				copy($rt."/".$fld, $rd."/".$fld);
				return true;
			}

			if (!file_exists($tgt."/".$fld)) {
				mkdir($tgt."/".$fld, 0777);
			}

			$hdl = opendir($rt."/".$fld);
			while ($file = readdir($hdl)) {
				if (($file != "..") && ($file != ".")) {
					if (is_dir($rt."/".$fld."/".$file)) {
						$this->copyfiles($rt."/".$fld, $file, $tgt."/".$fld);
					} else {
						copy ($rt."/".$fld."/".$file, $tgt."/".$fld."/".$file);
					}
				}
			}
			closedir($hdl);
		}

		function movefiles($rt, $fld, $tgt) {
		    if (!is_dir($rt."/".$fld)) {
		    	copy($rt."/".$fld, $tgt."/".$fld);
				unlink($rt."/".$fld);
		    }

			if (file_exists($tgt."/".$fld) !== true) {
				mkdir($tgt."/".$fld, 0777);
				$rmg = $rt."/".$fld;
			}
			$hdl = opendir($rt."/".$fld);
			while ($file = readdir($hdl)) {
			    if (($file!="..") && ($file!=".")) {
					if (is_dir($rt."/".$fld."/".$file) === true) {
						$this->copyfiles($rt."/".$fld, $file, $tgt."/".$fld);
						rmdir($rt."/".$fld."/".$file);
					} else {
						copy ($rt."/".$fld."/".$file, $tgt."/".$fld."/".$file);
						unlink($rt."/".$fld."/".$file);
					}
			}
			}
			if (isset($rmg)) {
				rmdir ($rmg);
			}
			closedir($hdl);
		}

		private function setChmod() {
			$folder = $this->getFolder();

			$arr = array();			if (!empty($folder)) {
				$arr[] = $folder;
			}
			if ($this->request->hasPostVar('rd')) {
				$arr[] = $this->request->getPostVar('rd');
			}
			foreach($arr as $file) {
				if (file_exists($file)) {
					$this->ssh->getResponse("chmod 777 ".$file);
				}
			}		}	}
?>