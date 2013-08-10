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
	class Nowplay {
	    
        public static $object;
        
        public static function create() {
            if (self::$object === null) {
                self::$object = new self();
            }
            
            return self::$object;
        }
        
        private function __construct() {
            $this->db = MySql::create();
            $this->request = Request::create();
            $this->array_playlist = $this->getPlaylistList();
        }
        
		public $bgVP = array(
			1 => "/radio/images/bg_time_1.jpg",
			2 => "/radio/images/bg_time_2.jpg",
			3 => "/radio/images/bg_time_3.jpg",
			4 => "/radio/images/bg_time_4.jpg",
			5 => "/radio/images/bg_time_5.jpg",
			6 => "/radio/images/bg_time_6.jpg"
		);
        
		public function getNextPlaylist() {
			$budet_val = date("U")*2;
			$budet_name = "";			foreach($this->array_playlist as  $name => $value) {
				if ( ($value < $budet_val) and ($value > date("U")-180) ) {
					if ($budet_val > $value and !strpos($name, SYSTEM_SYMVOL)) {						$budet_val = $value;
						$budet_name = $name;
					}
				}
			}

			$budet = explode("--psx", $budet_name);
			return $budet[0];		}

		public function getPlaylistList() {
			$event_id = 2;
			$now_time = 0;
			$is = 0;

			$day_now = date("l");
			$day_now = $this->strDateToNumber($day_now);
			$time_of_day = strtotime(date("Y-m-d "));
			$all_events = array();
			$array_playlist = array();

            $query = "SELECT * FROM `playlist` WHERE `enable`=1 AND `event".$event_id."` REGEXP '[^+]' ";
			foreach ($this->db->getLines($query) as $line) {
				$event = explode(";", $line['event'.$event_id]);
				$name = $line['name'];

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
									if (($now_time - $period*60) < $event_time) {
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
										$sled_time[$is] = $event_time;
										$sled_name[$is] = $name;
										if (!empty($array_playlist[$name])) {
											if (empty($w)) {
												$name2 = $name."--psx-";
											} else {
												$name2 = $name."--psx-".$w;
											}
											$array_playlist[$name2] = $sled_time[$is];
										} else {
											$array_playlist[$name] = $sled_time[$is];
										}
										if (empty($w)) {
											$w = 1;
										} else {
											$w++;
										}
										$is=$is+1;
										$mmii = ((24*60*60)*7);
										$sled_time[$is] = $event_time-$mmii;
										$sled_name[$is] = $name;
										if ($array_playlist[$name]) {
											$name2 = $name."--psx-".$w;
											$array_playlist[$name2] = $sled_time[$is];
										} else {
											$array_playlist[$name] = $sled_time[$is];
										}
										$w++;
										$is = $is+1;
									}
								}
							}
						}
					}
				}			}

			asort($array_playlist);
			return $array_playlist;
		}

		public function getCurrentPlaylist() {			$query = "SELECT * FROM `playlist` WHERE `now`=1";
			$playlist = $this->db->getLine($query);

			return $playlist['name'];		}
		
		public function getMusicLoadForm() {
		    ob_start();
		    include($this->request->getRadioPath().'tpl/musicLoadForm.tpl.html');
            $content = ob_get_contents();
            ob_end_clean();
                        
            if (TEMP_UPLOAD == "") {
                $errors[] = "В настройках RadioCMS не задана папка для загрузки.";
            } 
            
            if (TEMP_UPLOAD != "" and !file_exists($this->request->getMusicPath().TEMP_UPLOAD)) {
                $errors[] = "Папка для загрузки не существует.";
            } 
            
            if (file_exists($this->request->getMusicPath().TEMP_UPLOAD) and !is_writeable($this->request->getMusicPath().TEMP_UPLOAD)) {
                $errors[] = "Недостаточно прав для загрузки.";
            } 
            
            if ($this->letToInt(ini_get('upload_max_filesize')) < 8000000) {
                $errors[] = "Размер загружаемого файла слишко мал. Разрешите загружать минимум 8 мегабайт.";
            } 
            
            if (!empty($errors)) {
                $errorString = "";
                foreach ($errors as $error) {
                    $errorString .= "<p><i>$error</i></p>";
                }
                return $errorString;
            }
            
            return $content;
		}

		public function getVisualPlaylist() {
			$pred = $this->getPrevVP();
        	$add = $this->getAddProgramInVP($pred);
        	$array_playlist = $this->generateFullVP($add);
        	$artoday = $this->generateTodayArrayVP($array_playlist);
        	$array_vp = $this->getArrayVP($artoday);

        	$visual = $this->generateVP($artoday, $array_vp);

        	ob_start();
        	include($this->request->getRadioPath().'tpl/visualPlaylist.tpl.html');
        	$content = ob_get_contents();
        	ob_end_clean();
        	return $content;
        }

        public function generateVP($artoday, $array_vp) {        	$vp = 0;
			$x = 1;
			$t = 0;
			$proc_sum = 0;
			$visual = array();

			foreach ($artoday as $k => $v) {
				$nachalo = date("G:i",$v);
				if (!empty($array_vp[$vp])) {
					$t = $array_vp[$vp];
					$t = $t-$v;
					$sto = 24*60*60;
   	 				$proc = (100*$t)/$sto;
    				$proc = number_format($proc, 1, '.', '');
   					$proc =  round($proc);
				} else {
        			$proc = -1;
				}

    			if ($proc > 0) {    				$proc_sum = $proc_sum + $proc;
    			}
    			if ($proc < 0) {    				$proc = 100 - $proc_sum;
    			}
   				$pos = strpos($k,"--psx");
    			if ($pos > 0) {    				$k = substr($k,0,$pos);
    			}
    			$k = str_replace("\r\n","",$k);
    			$prod_m = $t/60;
    			if ($k == "") {    				$k = "Не задано";
    			}

    			$array['url'] = $this->bgVP[$x];
    			$array['proc'] = $proc;
    			$array['k'] = $k;
    			$array['nachalo'] = $nachalo;

    			$visual[] = $array;
				$vp++;
				$x++;
				if (count($this->bgVP) < $x) {					$x = 1;
				}
			}
			return $visual;        }

        public function getArrayVP($artoday) {        	$vp = -1;
        	$array_vp = array();
			foreach ($artoday as $k => $v) {
				$array_vp[$vp] = $v;
				$vp++;
			}

			return $array_vp;        }

        public function generateTodayArrayVP($array_playlist) {
       		$now = date("U");
			$today = mktime(0, 0, 0);
			$tomorrow = mktime(0, 0, 0, date("m")  , date("d") +1, date("Y"));
			$artoday = array();
			        	// Массив за сегодня
			foreach ($array_playlist as $k => $v) {
				if (($v < $tomorrow) and ($v > $today)) {
					$artoday[$k] = $v;
				}
			}

			//Добавляем последний вчера
			$v2 = 0;
			$k2 = "";
			foreach ($array_playlist as $k => $v) {
				if ($v < $today) {
					if ($v2 < $v) {						$v2 = $v; $k2 = $k;
					}
				}
			}

			$artoday[$k2] = $today;
            asort($artoday);
			return $artoday;        }

        public function generateFullVP($add) {
        	$array_playlist = $this->array_playlist;        	$count = count($add);
			for ($i=0; $i<$count; $i++) {
				$name = $add[$i]['name']."--psx-2-".$i;
				$val = $add[$i]['value'];
				$array_playlist[$name] = $val;
			}
			
			return $array_playlist;        }

        public function getPrevVP() {        	$i = 0;
        	$pred = array();
			foreach ($this->array_playlist as $k => $v) {
				$nopr = $this->zapros($k);
				if (empty($nopr)) {
    				$pred[$i]['name'] = $k;
    				$pred[$i]['value'] = $v;
    				$pos_name = $k;
   			 	} else {
    				$pred[$i]['name'] = $pos_name;
    				$pred[$i]['value'] = $v;
   				}
				$i++;
			}
			
			return $pred;        }

        public function getAddProgramInVP($pred) {        	$i = 0;
			$t = 0;
			$add = array();
			foreach ($this->array_playlist as $k => $v) {
    			$k_p_t = $this->zapros($k);
    			// если это программа
    			if ($k_p_t > 0) {
    				$ip = $i+1;
    				$im = $i-1;
    				$add_time = $pred[$i]['value'] + $k_p_t;
    				//если это время меньше чем следующее тогда добавляем
    				if ($add_time < $pred[$ip]['value']) {
    					$add[$t]['name'] = $pred[$im]['name'];
    					$add[$t]['value'] = $add_time;
    					$t++;
    				}
   				}
				$i++;
			}

			return $add;        }

        public function getDinamika() {        	$date = Date::create();
        	$dinamikaInfo = $this->getDinamikaInfo();
        	$dinamikaGraph = $this->getDinamikaGraphLine();
        	$dinamikaBottom = $this->getDinamikaBottomLine();
        	ob_start();        	include($this->request->getRadioPath().'tpl/dinamika.tpl.html');
        	$content = ob_get_contents();
        	ob_end_clean();
        	return $content;        }

		public function getDinamikaBottomLine() {
			$count = 0;
			$return = array();
		    for ($i=30; $i<=130;$i=$i+10) {
      			if ($this->vsego > $i) {
      				$count++;
      			}
		    }

		    for ($i=1; $i<=$count; $i++) {
		    	$time = $this->max_time - $this->min_time;
		    	$time = $time/($count);
				$return[$i] = $this->max_time - $time*$i;
		    }
			
            return $return;
		}

		public function getDinamikaGraphLine() {
			$now_time = date("U");
			$this->min_time = date("U");
			$this->max_time = 0;

            $return = array();
			$query = "SELECT * FROM `statistic` WHERE `type` = 'day' ORDER BY `time` DESC LIMIT 121";
			$this->vsego = $this->db->getCountRow($query);
			
			foreach ($this->db->getLines($query) as $line) {
				$now_listeners = $line['listeners'];
 				$proc_listeners =  ($now_listeners * 100) / $this->listenersMax;
 				$proc_listeners = intval($proc_listeners);
 				if ($proc_listeners==0) {
 					$proc_listeners = 2;
 				}

				$return[] = $proc_listeners;

    			if ($line['time'] < $this->min_time) {
    				$this->min_time = $line['time'];
    			}
    			if ($line['time'] > $this->max_time) {
    				$this->max_time = $line['time'];
    			}
			}

			return $return;
		}

		public function getDinamikaInfo() {
			$max_listeners = 0;
			$query = "SELECT * FROM `statistic` WHERE `type` = 'day' ORDER BY `time` DESC";

			foreach ($this->db->getLines($query) as $line) {
    			if ($line['listeners'] > $max_listeners) {
    				$max_listeners = $line['listeners'];
    			}
			}

			if ($max_listeners <= 5) {$max_listeners = 5;}

 			$l_pol = $max_listeners / 2;
 			$l_niz = $max_listeners / 4;
 			$l_centr = $max_listeners - $l_niz -$l_niz;
 			$l_verh = $max_listeners - $l_niz;

			$info['max']  = $max_listeners;
			$this->listenersMax = $max_listeners;
 			$info['top']  = intval($l_verh);
 			$info['bottom'] = intval($l_niz);
 			$info['center'] = intval($l_centr);

 			return $info;
		}

		public function zapros($name) {			$pos = strpos($name,"--psx");
    		if ($pos > 0) {    			$name = substr($name, 0, $pos);
    		}
			$query = "SELECT * FROM `playlist` WHERE `playmode` = '3' and `name` = '".addslashes($name)."' LIMIT 1 ";
			$line = $this->db->getLine($query);			$id_k = $line['id'];

			if (!empty($id_k)) {
				$query = "SELECT SUM(`duration`) as sum FROM `songlist` WHERE `id`=".$id_k;
				$line = $this->db->getLine($query);
				$k_plus_time = $line['sum'];

				if ($k_plus_time == "") {					$k_plus_time = 1;
				}
				return $k_plus_time;
			}		}

		public function strDateToNumber($day) {
			$day = str_replace("Monday", "1", $day);
			$day = str_replace("Tuesday", "2", $day);
			$day = str_replace("Wednesday", "3", $day);
			$day = str_replace("Thursday", "4", $day);
			$day = str_replace("Friday", "5", $day);
			$day = str_replace("Saturday", "6", $day);
			$day = str_replace("Sunday", "7", $day);

			return $day;
		}
        
        public function letToInt($v){
             $l = substr($v, -1);
             $ret = substr($v, 0, -1);
             switch(strtoupper($l)){
             case 'P':
                 $ret *= 1024;
             case 'T':
                 $ret *= 1024;
             case 'G':
                 $ret *= 1024;
             case 'M':
                 $ret *= 1024;
             case 'K':
                 $ret *= 1024;
                 break;
             }
             
             return $ret;
        }	}
?>