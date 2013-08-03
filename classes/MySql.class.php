<?php
	class MySql {
		
		public static $object;
		
		public static function create() {
			if (self::$object === null) {
				self::$object = new self();
			}
			
			return self::$object;
		}
		
		private function __construct() {
			$this->link = mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD)
		  		or die ("Could not connect to MySQL");
		
			mysql_select_db(DB_NAME)
		  		or die ("Could not select database");
		
			mysql_query("SET NAMES 'utf8'", $this->link);
            
            $this->request = Request::create();
		}
		
		public function queryNull($query) {			mysql_query($query, $this->link) or die($this->debug());		}

		public function getLine($query) {
			$result = mysql_query($query, $this->link) or die($this->debug());
			return mysql_fetch_array($result, MYSQL_ASSOC);
		}

		public function getColumn($query, $column) {
			$result = mysql_query($query, $this->link) or die($this->debug());
			$line =  mysql_fetch_array($result, MYSQL_ASSOC);
			return $line[$column];
		}

		public function getLines($query) {
			$result = mysql_query($query, $this->link) or die($this->debug());
			$lines = array();
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    			$lines[] = $line;
			}

			return $lines;
		}

		public function getCountRow($query) {			$result = mysql_query($query, $this->link) or die($this->debug());        	return mysql_num_rows($result);		}
		
		public function debug() {
			include($this->request->getRadioPath().'tpl/debug.tpl.html');
			exit;
		}	}
?>