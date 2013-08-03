<?php
	class RequestFilter {
	    
        public static function create() {
            return new self();
        }
        
		public function __construct() {			$this->filter = Filter::create();
			$this->apply();		}
        		public function apply() {			foreach ($_POST as $key => $value) {
				if (!is_array($value)) {
					unset($_POST[$key]);
					$_POST[$key] = $this->filter->apply($value);
				}
			}
			foreach ($_GET as $key => $value) {
				if (!is_array($value)) {
					unset($_GET[$key]);
					$_GET[$key] = $this->filter->apply($value);
				}
			}
			foreach ($_COOKIE as $key => $value) {
				if (!is_array($value)) {
					unset($_COOKIE[$key]);
					$_COOKIE[$key] = $this->filter->apply($value);
				}
			}		}	}



?>