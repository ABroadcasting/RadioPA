<?php
	class Date {		public $authMinutes = 15;
        
        public static function create() {
            return new self();
        }

		private function __construct() {			$this->now = date("U");		}

		public function toRussian($text) {        	$text = str_replace("Monday", " Понедельник", $text);
			$text = str_replace("Tuesday", " Вторник", $text);
			$text = str_replace("Wednesday", " Среда", $text);
			$text = str_replace("Thursday", " Четверг", $text);
			$text = str_replace("Friday", " Пятница", $text);
			$text = str_replace("Saturday", " Суббота", $text);
			$text = str_replace("Sunday", " Воскресенье", $text);
			$text = str_replace("Понедельник, Вторник, Среда, Четверг, Пятница, Суббота, Воскресенье", "Каждый день", $text);

			return $text;		}
		public function getNow() {
			return $this->now;		}

		public function minutes($minutes) {			return $minutes*60;		}

		public function getAuthTime() {			return $this->now+($this->authMinutes*60);		}

		public function setTime($time) {			$this->time = $time;
			return $this;		}

		public function toFormatString($format) {        	return date($format, $this->time);		}

		public function toMinSec($second) {			$return =  floor($second/60).":";
			$dur_minutes= fmod($second, 60);
        	if($dur_minutes < 10) {				$return .= "0".$dur_minutes;
        	} else {        		$return .= $dur_minutes;
        	}

        	return $return;		}	}
?>