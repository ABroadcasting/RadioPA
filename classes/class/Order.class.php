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
	class Order {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
	            
		private function __construct() {			$this->db = MySql::create();
			$this->request = Request::create();		}

		public function handler() {
			$this->clearZakaz();		}

		public function clearZakaz() {			$user = Autentification::create()->getUser();
			if (!empty($user) and $user['admin'] == 1 and $this->request->hasGetVar('clear_zakaz')) {
				$query = "UPDATE `songlist` SET `zakazano` = 0";
    			$this->db->queryNull($query);
			}		}

		public function getLastOrders($limit) {			$query = "SELECT * FROM `last_zakaz` ORDER BY `time` DESC LIMIT $limit";
			return $this->db->getLines($query);		}

		public function getTopOrders($limit) {
			$query = "SELECT * FROM `playlist` ORDER BY `sort` ASC";
			$lines = $this->db->getLines($query);

			$id_hidden = array();
			foreach ($lines as $index=>$line) {
				if ($line['show'] == 0) {
					$id_hidden[] = $line['id'];
				}
			}

			$chislo_i = count($id_hidden)-1;

			for ($i=0; $i<=$chislo_i; $i++) {
				if ($i == 0) {
					$ne_pokazivat = "WHERE ";
					$ne_pokazivat2 = "";
				}
				if ($i == $chislo_i) {
					$ne_pokazivat = $ne_pokazivat.$id_hidden[$i]." != `id`"; $ne_pokazivat2 = $ne_pokazivat2.$id_hidden[$i]." != `id`";
				} else {
					$ne_pokazivat = $ne_pokazivat.$id_hidden[$i]." != `id` and "; $ne_pokazivat2 = $ne_pokazivat2.$id_hidden[$i]." != `id` and ";
				}
			}
			
			if (empty($ne_pokazivat)) {
				$ne_pokazivat = "";
			}

			$query = "SELECT * FROM `songlist` $ne_pokazivat ORDER BY `zakazano` DESC LIMIT $limit";

			return $this->db->getLines($query);
		}

		public function getPlaylistBySong($id) {			$query = "SELECT * FROM `playlist` WHERE `id` = '$id'";
			return $this->db->getLine($query);		}	}
?>