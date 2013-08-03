<?php
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