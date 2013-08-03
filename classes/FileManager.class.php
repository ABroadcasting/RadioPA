<?php
	class FileManager {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
		private function __construct() {			$this->request = Request::create();
			$this->ssh = Ssh::create();		}
		
		public function handler() {
			if ($this->request->hasGetVar('del_install')) {
				$file = $this->request->getRadioPath()."install.php";
				if (file_exists($file)) {
					@unlink($file);
                    $this->ssh->sshExec("rm $file");
				}	
			}
		}

  		public function getDiskInfo() {
  			$disk = array();  			$hdd_info = $this->requestDiskInfo();
            $comp_space = 0;
			$comp_free = 0;
            if (!empty($hdd_info)) {
				foreach($hdd_info as $key => $data) {
					$pic = str_replace('/','-',$key);
					array_shift($data);
					foreach($data as $data_key => $data_params) {
						$part = $data_params[0];
						$total = $this->byteToMb($data_params[1], 0);
						$comp_space = $comp_space + $data_params[1];
						$used = $this->byteToMb($data_params[2], 2);
						$comp_free = $comp_free + $data_params[3];
						$free = $this->byteToMb($data_params[3], 2);
						$percent = $data_params[4];
						$mount = $data_params[5];
					}
				}
			}	

			if ($comp_space != 0) {
				$comp_zan = $comp_space - $comp_free;
				$comp_zan_proc = ($comp_zan*100)/$comp_space;
				$comp_free_proc = ($comp_free*100)/$comp_space;
				$comp_zan_proc = intval($comp_zan_proc);
				$comp_free_proc = intval($comp_free_proc);
				$comp_zan_proc = $comp_zan_proc + 1;
				if($comp_free_proc==0 and $comp_zan_proc==1) {					$comp_zan_proc = 100;
				}
				$comp_space = $this->byteToMb($comp_space,0,1024);
				$comp_free = $this->byteToMb($comp_free,0,1024);
				$comp_zan = $this->byteToMb($comp_zan,0,1024);

				$disk['zan']['proc'] = $comp_zan_proc;
				$disk['free']['proc'] = $comp_free_proc;
				$disk['zan']['mb'] = $comp_zan;
				$disk['free']['mb'] = $comp_free;
			} else {
				$disk['zan']['proc'] = 100;
				$disk['free']['proc'] = 0;
				$disk['zan']['mb'] = 0;
				$disk['free']['mb'] = 0;
			}

			return $disk;  		}

  		public function byteToMb($size, $round) {
			$stor_mb = round($size/1024, $round);
			$size = $stor_mb;
			$size .= ' Мб';
			return $size;  		}

  		public function requestDiskInfo() {
  			$hdd_info = array();
			$hdds = $this->ssh->getResponse("df -l | grep -o '^/dev/.*'");

			$hdds = preg_split('/\n/', $hdds, -1, PREG_SPLIT_NO_EMPTY);

			foreach($hdds as $hdd) {
				$hdd_stat = preg_split('/ /', $hdd, -1, PREG_SPLIT_NO_EMPTY);
				$label = rtrim(substr($hdd_stat[0], 0, strlen($hdd_stat[0])-1));
				$temp = $this->ssh->getResponse("sudo hddtemp -n $label");
				if ( strlen($temp) >= 3 ) {
					$temp = 'error';
				}
				$hdd_info[$label]['temp'] = $temp;
				$hdd_info[$label][] = $hdd_stat;
				unset($name);
			}

			return $hdd_info;
  		}

		function getCountTempFiles() {   			$upload_dir = $this->request->getMusicPath().TEMP_UPLOAD;
			if (is_dir($upload_dir)) {
				$handle = opendir($upload_dir);
				$files = array();
				$count = 0;

				while (($file = readdir($handle))!== false) {
					$string = strpos($file, ".mp3");
    				$string2 = strpos($file, ".MP3");
    				if (
    					$file != "." and $file != ".." and
    					($string != "" or $string2 != "")
    				) {
    					$files[] = $file;
  					}
    			}
    			closedir($handle);
				return count($files);
			}		}	}
?>