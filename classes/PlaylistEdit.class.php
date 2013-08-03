<?php
	class PlaylistEdit {
	    
        public static function create() {
            return new self();
        }
        		private function __construct() {
			$this->request = Request::create();
			$this->db = MySql::create();		}

		public function handler() {
			if ($this->request->hasPostVar('submit_and_add')) {
				if ($this->request->hasGetVar('playlist_id')) {
					$this->update();
				} else {
					$id = $this->insert();
				}
				$id = !empty($id) ? $id : $this->request->getGetVar('playlist_id');
				header("Location: meneger.php?playlist_id=".$id);
				exit;
			}			if ($this->request->hasPostVar('submit')) {
				if ($this->request->hasGetVar('playlist_id')) {					$this->update();				} else {					$id = $this->insert();				}
				$id = !empty($id) ? $id : $this->request->getGetVar('playlist_id');
				header("Location: playlist_edit.php?playlist_id=".$id);
				exit;
			}		}

		private function insert() {
			if ($this->request->getPostVar('playmode') == 3){
				$allow_zakaz = 0;
			} else {
				$allow_zakaz = $this->request->getPostVar('playmode');
			}

			if ($this->request->hasPostVar('event') == 1) {
				$event1 = $this->toEvent1String($this->request->getPostVar('event1'));
				$last_time = time();
				$event2 = "";
			}

			if ($this->request->getPostVar('event') == 2) {
				$event2 = $this->toEvent2String($this->request->getPostVar('event2'));
				$last_time = 0;
				$event1 = "";
			}

			$query = "
				INSERT INTO `playlist`
					(
						`name` ,
						`sort` ,
						`playmode` ,
						`enable` ,
						`event1` ,
						`event2` ,
						`last_time` ,
						`show` ,
						`allow_zakaz`
					)
				VALUES
					(
						'".addslashes($this->request->getPostVar('name'))."',
						'".addslashes($this->request->getPostVar('sort'))."',
						'".addslashes($this->request->getPostVar('playmode'))."',
						'".addslashes($this->request->getPostVar('enable'))."',
						'$event1',
						'$event2',
						'$last_time',
						'".addslashes($this->request->getPostVar('show'))."',
						".addslashes($this->request->getPostVar('allow_zakaz'))."
					)";

			$this->db->queryNull($query);

			$query = "SELECT `id` FROM `playlist` ORDER BY `id` DESC LIMIT 1";
			return $this->db->getColumn($query, 'id');
		}

		private function update() {			if ($this->request->getPostVar('playmode') == 3){				$allow_zakaz = 0;
			} else {				$allow_zakaz = $this->request->getPostVar('allow_zakaz');
			}

			if ($this->request->getPostVar('event') == 1) {				$event1 = $this->toEvent1String($this->request->getPostVar('event1'));
				$event2 = "";			}

			if ($this->request->getPostVar('event') == 2) {
				$event2 = $this->toEvent2String($this->request->getPostVar('event2'));
				$event1 = "";
			}

			$query = "
				UPDATE
					`playlist`
				SET
					`name` = '".addslashes($this->request->getPostVar('name'))."',
					`sort` = '".addslashes($this->request->getPostVar('sort'))."',
					`playmode` = '".addslashes($this->request->getPostVar('playmode'))."',
					`enable` = '".addslashes($this->request->getPostVar('enable'))."',
					`event1` = '$event1',
					`event2` = '$event2',
					`show` = '".addslashes($this->request->getPostVar('show'))."',
					`allow_zakaz` = '$allow_zakaz'
				WHERE
					`id` = ".$this->request->getPostVar('playlist_id').";";

			$this->db->queryNull($query);		}

		private function toEvent2String($event) {
			$i = 0;
			$string = "";
			foreach ($event as $line) {
				$days = array();
				if (!empty($line['days'])) {
					foreach ($line['days'] as $day) {
                		$days[] = $day;
					}
				}
				$i++;
				$string .=
               		implode(",", $days)."-".
               		$line['start1']['h'].":".$line['start1']['m']."-".
               		$line['start2']['h'].":".$line['start2']['m']."-".
               		$line['start3']['h'].":".$line['start3']['m'];

               	if ($i < 3) $string .= ";";
			}

			return $string;
		}

		private function toEvent1String($event) {
			$i = 0;
			$string = "";			foreach ($event as $line) {
				$days = array();
				if (!empty($line['days'])) {
					foreach ($line['days'] as $day) {                		$days[] = $day;					}
				}
				$i++;
				$string .=
               		implode(",", $days)."-".
               		$line['start']['h'].":".$line['start']['m']."-".
               		$line['end']['h'].":".$line['end']['m']."-".
               		$line['interval']['h'].":".$line['interval']['m'];

               	if ($i < 3) $string .= ";";			}

			return $string;		}

		public function getEvent2() {
			$event2 = array();
			if (!empty($this->playlist['event2'])) {
                $rows = explode(";", $this->playlist['event2']);
                foreach ($rows as $index=>$row) {
                 	$el = explode("-", $row);
                 	$days = explode(",", $el[0]);
                 	foreach($days as $day) {
                 		$day = trim($day);
                 		if (!empty($day)) {
                 			$event2[$index]['days'][trim($day)] = 1;
                 		}
                 	}

                 	$start = explode(":", $el[1]);
                 	$event2[$index]['start1']['h'] = $start[0];
                 	$event2[$index]['start1']['m'] = $start[1];

                 	$end = explode(":", $el[2]);
                 	$event2[$index]['start2']['h'] = $end[0];
                 	$event2[$index]['start2']['m'] = $end[1];

                 	$interval = explode(":", $el[3]);
                 	$event2[$index]['start3']['h'] = $interval[0];
                 	$event2[$index]['start3']['m'] = $interval[1];
                 }
                 return $event2;			} else {
				return false;
			}		}

		public function getEvent1() {			$event1 = array();			if (!empty($this->playlist['event1'])) {                 $rows = explode(";", $this->playlist['event1']);
                 foreach ($rows as $index=>$row) {                 	$el = explode("-", $row);
                 	$days = explode(",", $el[0]);
                 	foreach($days as $day) {                 		$day = trim($day);
                 		if (!empty($day)) {                 			$event1[$index]['days'][trim($day)] = 1;
                 		}                 	}

                 	$start = explode(":", $el[1]);
                 	$event1[$index]['start']['h'] = $start[0];
                 	$event1[$index]['start']['m'] = $start[1];

                 	$end = explode(":", $el[2]);
                 	$event1[$index]['end']['h'] = $end[0];
                 	$event1[$index]['end']['m'] = $end[1];

                 	$interval = explode(":", $el[3]);
                 	$event1[$index]['interval']['h'] = $interval[0];
                 	$event1[$index]['interval']['m'] = $interval[1];                 }
                 return $event1;			} else {				return false;			}		}

		public function isEvent2() {			if (isset($this->playlist['event2'])) {
				if (!empty($this->playlist['event2'])) {					return true;				} else {					return false;				}
			} else {
				return true;
			}		}

		public function isEvent1() {
			if (isset($this->playlist['event1'])) {
				if (!empty($this->playlist['event1'])) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getPlaymode() {
			if (isset($this->playlist['playmode'])) {
				return $this->playlist['playmode'];
			} else {
				return 1;
			}
		}

		public function isShow() {
			if (isset($this->playlist['show'])) {
				if ($this->playlist['show'] == 1) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		}

		public function isAllowOrder() {
			if (isset($this->playlist['allow_zakaz'])) {
				if ($this->playlist['allow_zakaz'] == 1) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		}

		public function isEnabled() {
			if (isset($this->playlist['enable'])) {
				if ($this->playlist['enable'] == 1) {					return true;				} else {					return false;				}
			} else {
				return true;
			}
		}

		public function getSort() {
			if (isset($this->playlist['sort'])) {
				return $this->playlist['sort'];
			} else {
				return false;
			}
		}

		public function getName() {			if (!empty($this->playlist['name'])) {				return $this->playlist['name'];			} else {				return false;			}		}

		public function prepare() {			$this->playlist = $this->getPlaylist();		}

		private function getPlaylist() {			$query = "SELECT * FROM `playlist` where `id` = ".$this->id;
			return $this->db->getLine($query);		}

		public function getNextSort() {
			$query = "SELECT * FROM `playlist` ORDER BY `sort` DESC LIMIT 0,1";
	    	return $this->db->getColumn($query, 'sort')+1;		}

		public function setId($id) {			$this->id = (int) $id;		}

		public function getId() {			if (!empty($this->id)) {				return $this->id;			} else {				return false;			}		}	}
?>