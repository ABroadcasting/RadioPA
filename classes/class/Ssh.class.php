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