<?php
	class Ssh {
	    
	    public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        		private function __construct() {
			$this->request = Request::create();
			if ($this->connection = ssh2_connect(IP, 22)) {
				$this->connected = true;
			} else {
				$this->connected = false;
			}
			if(
				$this->connected and
				ssh2_auth_password($this->connection, SSH_USER, SSH_PASS)
			) {				$this->connected = true;
			} else {				$this->connected = false;			}		}

		public function getResponse($command) {
		 	if (!$stream = ssh2_exec($this->connection, $command )){
            	echo "fail: unable to execute command\n";
        	} else{				stream_set_blocking( $stream, true );
            	$data = "";
    			while( $buf = fread($stream,4096) ){
                	$data .= $buf;
            	}
       			fclose($stream);
       		}

       		return $data;		}

		public function sshExec($command) {			if (!$this->connected) {				return false;
			}

			ssh2_exec($this->connection, $command);		}

		public function isConnected() {			if ($this->connected) {				return true;			} else {				return false;			}		}

		public function getWgetCommand() {
			$data = $this->getResponse("uname -a");

        	if (strpos($data, "freebsd") > 1) {
         		return "fetch";
        	} else {
          		return "wget";
        	}
		}
        
        public function checkEzstreamCompatibility() {
            $data = $this->getResponse("ezstream -V");

            if (strpos($data, "radiocms") > 1) {
                return true;
            } else {
                return false;
            }
        }
        
        public function noFirstTrack() {
            $data = $this->getResponse("ezstream -V");

            if (strpos($data, "--no-first-track") > 1) {
                return true;
            } else {
                return false;
            }
        }	}
?>