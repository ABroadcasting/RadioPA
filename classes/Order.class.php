<?php
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