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
	class Dj {
	    
        public static function create() {
            return new self();
        }
        		private function __construct() {        	$this->db = MySql::create();
        	$this->request = Request::create();
        	$this->user = Autentification::create();		}

		public function handler() {			$user = $this->user->getUser();			if ($this->request->hasGetVar('del') and $user['admin'] == 1) {
				$this->deleteDj();
			}
            if ($this->request->hasPostVar('dj') and $this->request->hasPostVar('djpass') and $user['admin'] == 1) {
				$this->insertDj();
			}		}

		public function getDjList() {			$query = "SELECT * FROM `dj` ORDER BY `id`";
			return $this->db->getLines($query);		}

		public function getError() {
		    if (!empty($this->error)) {				return $this->error;
			} else {				return false;			}		}

		public function insertDj() {
			$query = "SELECT * FROM `dj` ORDER BY `id`";
			foreach ($this->db->getLines($query) as $line) {
				if ($this->request->getPostVar('dj') == $line['dj']) {
					$this->error = "Такой пользователь уже есть";
				}
			}

			$admin = addslashes($this->request->getPostVar('admin'));
			$dj = addslashes($this->request->getPostVar('dj'));
			$password  = addslashes($this->request->getPostVar('djpass'));
			$description = addslashes($this->request->getPostVar('djdescription'));

			if (empty($this->error)) {
				$query="INSERT INTO `dj` ( `description` , `dj` , `password` ,`admin` )
					VALUES ('$description', '$dj','$password','$admin');";
				$this->db->queryNull($query);
			}		}

		public function deleteDj() {			$delete = intval($this->request->getGetVar('del'));
			$query = "DELETE FROM `dj` WHERE `id` = ".$delete;
			$this->db->queryNull($query);		}	}
?>