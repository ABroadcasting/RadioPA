<?php
	class Event {
	    
        public static function create() {
            return new self();
        }
        
		private function __construct() {			$this->request = Request::create();
			$this->ssh = Ssh::create();
			$this->db = MySql::create();		}

		public function isAllowZakaz() {
			$query = "SELECT * FROM `playlist` WHERE `now` = 1 AND `allow_zakaz` = 0 ";
			$line = $this->db->getLine($query);

			if (!empty($line)) {
				return false;
			} else{            	return true;			}		}

		public function updateEzstream() {
			if ($jobid = $this->getEzstreamJobId()) {
				$this->ssh->sshExec("kill -s HUP ".$jobid);
			}		}

		private function getEzstreamJobId() {			$data = $this->ssh->getResponse("ps ax | grep ezstream");
        	$data1 = explode("\n", $data);

	        foreach ($data1 as $value) {
	        	$data2 = explode("?",$value);
	        	$data3 = "";

	        	if (!empty($data2[1])) {
	        		$data3 .= $data2[1];
	        	}
	        	if (!empty($data2[2])) {
	        		$data3 .= $data2[2];
	        	}

	        	if (
	        		(!strpos($data3,"bash -c")) and
	        		(!strpos($data3,"csh -c")) and
	        		(strpos($data3, CF_EZSTREAM))
	        	) {
	        		$jobid = $data2[0];
	        		return $jobid;
	        	}
	        }

	        return false;		}

		public function getEvens($event_id = 1, $get_auto = 0) {
			$now_time = time();

			$query = "SELECT * FROM `playlist` WHERE `enable`=1 AND `event".$event_id."` REGEXP '[^+]' ";
			if ($get_auto) {
				$query .= " AND `auto`=1 ";
			}

			$day_now = date("l");
			$day_now = $this->strDateToNumber($day_now);

			$time_of_day = strtotime(date("Y-m-d "));

			$all_events = array();
			
			foreach ($this->db->getLines($query) as $line) {
				if ($get_auto) {
					$all_events[]['time'] = $now_time - 1;
					$all_events[sizeof($all_events)-1]['id'] = $line['id'];
					continue;
				}

				$event = explode(";", $line['event'.$event_id]);

				foreach($event as $value) {
					if (preg_match('/^([^\d]+)-(\d+):(\d+)(-(\d+):(\d+)(-(\d+):(\d+))?)?$/', $value, $time)) {
						$days_array = explode(",", $time[1]);
						$times_array=array($time[2]*60+$time[3],$time[5]*60+$time[6],$time[8]*60+$time[9]);

						foreach ($days_array as $event_day) {
							if (trim($event_day) == '') {
								continue;
							}

							$event_day = $this->strDateToNumber($event_day);
							$event_day = $event_day - $day_now;

							if ($event_day < 0) {
								$event_day = $event_day + 7;
							}

							$time_of_event_day = $time_of_day + $event_day*24*60*60;

							if ($event_id == 1) {
								$next_time = $times_array[0];
								while ($next_time <= $times_array[1] && $times_array[2] > 0) {
									$event_time = $time_of_event_day + $next_time*60;
									if (($now_time - PERIOD*60) < $event_time) {
										$all_events[]['time'] = $event_time;
										$all_events[sizeof($all_events)-1]['id'] = $line['id'];
									}

									$next_time = $next_time + $times_array[2];
								}
							} else {
								foreach ($times_array as $time) {
									$event_time = $time_of_event_day + $time*60;
									if ($time > 0 && ($now_time - PERIOD*60) < $event_time) {
										$all_events[]['time'] = $event_time;
										$all_events[sizeof($all_events)-1]['id'] = $line['id'];
									}
								}
							}
						}
					}
				}
			}

			usort($all_events, 'cmp');

			return $all_events;
		}

		private function strDateToNumber($day) {
			$day = str_replace("Monday", "1", $day);
			$day = str_replace("Tuesday", "2", $day);
			$day = str_replace("Wednesday", "3", $day);
			$day = str_replace("Thursday", "4", $day);
			$day = str_replace("Friday", "5", $day);
			$day = str_replace("Saturday", "6", $day);
			$day = str_replace("Sunday", "7", $day);

			return $day;
		}	}

	function cmp($a, $b) {
		$cmp = 0;

		if ($a["time"] > $b["time"]) {
			$cmp = 1;
		}

	    return $cmp;
	}
?>