<?php
	require_once('Include.php');
	require_once("_system.php");

	$statistic = Statistic::create();
	$statistic->updateAll();

	$tracklist = Tracklist::create();
	$tracklist->update();
	$ssh = Ssh::create();
    $request = Request::create();
	$event = Event::create();

	if ( (DIR_SHOW == "on") and (rand(1,5) == 1) ) {		$statistic->updateDirectory();
	}

	echo "<br>Отладочная информация <br>";

	$net_povtorov_array = $tracklist->getNoRepeatArray();
    $update_filename = $tracklist->getNowFilename();
    
    if (empty($update_filename)) {
        $update_filename = $tracklist->getRandFilename(50);
    }   

	if ($event->isAllowZakaz()) {
		$allow_zakaz = 1;
	} else {
		$allow_zakaz = 0;
	}

	$now_time = time();
	$vremya_trecka = 5;
	$allow_time_tmp = $now_time;
	$tek_time = date("Y-m-d H:i", $now_time);

	$play_list_text = '';
	$play_list_text_log = "";

	// Составим "график" предстоящих запусков

	$all_event2 = $event->getEvens(2);
	$all_event1 = $event->getEvens(1);
	$all_event1_auto = $event->getEvens(1, 1);

	// Обработаем EVENT1  //////////////////////////////////////////////////////////////////////////////////////////////

	$event1_count = 0;
	$i_auto = 0;
	$play_list_array = array();
	$play_list_array_temp = array();
	$allow_time2 = $allow_time;
	$id_event1_auto[1] = "";
    
    echo date("Y-m-d H:i")." -now<br />";
	echo date("m.d.y H:i", $allow_time)." -allowtime<br>";

	if ($all_event1) {
		while ($all_event1[$event1_count]['time'] < $now_time && $event1_count < sizeof($all_event1) && ($allow_time_tmp + $vremya_trecka*60) < $all_event2[0]['time']) {
			if ($all_event1[$event1_count]['id'] && $now_time > $allow_time2) {
			    echo "zapusk event1<br>";
				$query = "SELECT SUM(`duration`) as sum FROM `songlist` WHERE `id`=".$all_event1[$event1_count]['id'];
				$result = mysql_query($query) or die("Query failed2 : " . mysql_error());
				$line = mysql_fetch_array($result, MYSQL_ASSOC);
				$event1_duration = $line['sum'];

				$query = "SELECT * FROM `playlist` WHERE `id`=".$all_event1[$event1_count]['id'];
				$result = mysql_query($query) or die("Query failed3 : " . mysql_error());
				$line = mysql_fetch_array($result, MYSQL_ASSOC);
				$play_mode = $line['playmode'];

				$query = "SELECT * FROM `songlist` WHERE `id`=".$all_event1[$event1_count]['id']." ORDER BY `sort`";
				$result = mysql_query($query) or die("Query failed4 : " . mysql_error());

				// Случайно один
				if ($play_mode == "2") {
					while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
						if ( file_exists($line['filename']) ) {
							$play_list_array_temp[] = $line['filename']."\n";
						}
					}

					$sem = array_rand($play_list_array_temp, 1);
					$sem = $play_list_array_temp[$sem];
					$play_list_array_temp = array();
					$play_list_array[] = $sem;

					// Время
					$allow_time_tmp = $allow_time_tmp + $vremya_trecka*60;
					$allow_time = $allow_time_tmp;
					echo $allow_time." -allow_time_odin<br>";
				}

				// Вперемешку
				if ($play_mode == "1") {
					while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
						if ( file_exists($line['filename']) ) {
							$play_list_array[] = $line['filename']."\n";
						}
					}

					shuffle($play_list_array);
					// Время
					if ($event1_duration < $vremya_trecka*60) {						$event1_duration = $event1_duration+$vremya_trecka*60;
					}
					$allow_time_tmp = $allow_time_tmp + $event1_duration;
					$allow_time = $allow_time_tmp;
				}

				// По порядку
				if ($play_mode == "0") {
					while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
						if ( file_exists($line['filename']) ) {
							$play_list_array[] = $line['filename']."\n";
						}
					}
					// Время
					if ($event1_duration < $vremya_trecka*60) {						$event1_duration = $event1_duration+$vremya_trecka*60;
					}
					$allow_time_tmp = $allow_time_tmp + $event1_duration;
					$allow_time = $allow_time_tmp;
				}

				// Программа
				if ($play_mode == "3") {
					while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
						if ( file_exists($line['filename']) ) {
							$play_list_array[] = $line['filename']."\n";
						}
					}
					// Время
					if ($event1_duration < $vremya_trecka*60) {						$event1_duration = $event1_duration+$vremya_trecka*60;
					}
					$allow_time_tmp = $allow_time_tmp + $event1_duration;
					$allow_time = $allow_time_tmp;
				}

			} else {
				$query = "UPDATE `playlist` SET `auto`=1 WHERE `id`=".$all_event1[$event1_count]['id'];
				$result = mysql_query($query) or die("Query failed5 : " . mysql_error());
			}

			$event1_count++;
		}

		//Уникальный
		$play_list_array = array_unique($play_list_array);
		$i_auto = $i_auto + 1;
		foreach ($play_list_array as $line) {
			$play_list_text .= $line;
			$play_list_text_log .= "$tek_time (event1) ".$line;
		}
	}


	// Обработаем EVENT1_AUTO //////////////////////////////////////////////////////////////////////////////////////////////

	$event1_count = 0;
	$i_auto = 0;
	$play_list_array = array();
	$play_list_array_temp = array();
	$id_event1_auto[1] = "";

	echo date("m.d.y H:i", $allow_time)." -allowtime<br>";

	if ($now_time > $allow_time) {
		while ($event1_count < sizeof($all_event1_auto)) {
			echo date('m.d.y H:i', $now_time)." - now_time , ".date('m.d.y H:i', $allow_time)." - alow_time<br>";

			if ($all_event1_auto[$event1_count]['time'] < $now_time && $all_event1_auto[$event1_count]['id']) {
		        echo "zapusk event1_auto<br>";
				$query = "SELECT SUM(`duration`) as sum FROM `songlist` WHERE `id`=".$all_event1_auto[$event1_count]['id'];
				$result = mysql_query($query) or die("Query failed6 : " . mysql_error());
				$line = mysql_fetch_array($result, MYSQL_ASSOC);
				$event1_duration = $line['sum'];
				echo $event1_duration." -allduration_1_auto<br>";

				if (($allow_time_tmp + $vremya_trecka*60) < $all_event2[0]['time']) {

					$query = "SELECT * FROM `playlist` WHERE `id`=".$all_event1_auto[$event1_count]['id'];
					$result = mysql_query($query) or die("Query failed78 : " . mysql_error());
					$line = mysql_fetch_array($result, MYSQL_ASSOC);

					$play_mode = $line['playmode'];

					$query = "SELECT * FROM `songlist` WHERE `id`=".$all_event1_auto[$event1_count]['id']." ORDER BY `sort`";
					$result = mysql_query($query) or die("Query failed8 : " . mysql_error());

					// Случайно один авто
					if ($play_mode == "2") {
						while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		           			// Добавляем файлы в массив
							if ( file_exists($line['filename']) ) {
								$play_list_array_temp[] = $line['filename']."\n";
							}
						}

						$sem = array_rand($play_list_array_temp, 1);
						$sem = $play_list_array_temp[$sem];
						$play_list_array_temp = array();
						$play_list_array[] = $sem;

						// Время
						$allow_time_tmp = $allow_time_tmp + $vremya_trecka*60;
						$allow_time = $allow_time_tmp;
						echo $allow_time." -allow_time_odin<br>";
					}

					// Вперемешку авто
					if ($play_mode == "1") {
						while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
							$play_list_array[] = $line['filename']."\n";
						};

						shuffle($play_list_array);
						// Время
						if ($event1_duration < $vremya_trecka*60) {							$event1_duration = $event1_duration+$vremya_trecka*60;
						}
						$allow_time_tmp = $allow_time_tmp + $event1_duration;
						$allow_time = $allow_time_tmp;
					}

					// По порядку авто
					if ($play_mode == "0") {
						while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
							$play_list_array[] = $line['filename']."\n";
						}
						// Время
						if ($event1_duration < $vremya_trecka*60) {							$event1_duration = $event1_duration+$vremya_trecka*60;
						}
						$allow_time_tmp = $allow_time_tmp + $event1_duration;
						$allow_time = $allow_time_tmp;
					}

					// Программа авто
					if ($play_mode == "3") {
						while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
							$play_list_array[] = $line['filename']."\n";
						}
						// Время
						if ($event1_duration < $vremya_trecka*60) {							$event1_duration = $event1_duration+$vremya_trecka*60;
						}
						$allow_time_tmp = $allow_time_tmp + $event1_duration;
						$allow_time = $allow_time_tmp;
					}

					// цикл с указанимем конкретного id
					$id_event1_auto[$i_auto] = $all_event1_auto[$event1_count]['id'];
					$i_auto = $i_auto + 1;
				}
			}
			$event1_count++;
		}
		//Уникальный
		$play_list_array = array_unique($play_list_array);
		foreach ($play_list_array as $line) {
			$play_list_text .= $line;
			$play_list_text_log .= "$tek_time (event1_auto) ".$line;
		};
	}

	// Обработаем EVENT1_AUTO КОНЕЦ  //////////////////////////////////////////////////////////////////////////////////////////////

	if (!empty($id_event1_auto[0])) {
		$razov = count($id_event1_auto);
		for ($i_auto = 0; $i_auto <= $razov; $i_auto++) {
			if (!empty($id_event1_auto[$i_auto])) {
				$query = "UPDATE `playlist` SET `auto`=0 WHERE `id`=$id_event1_auto[$i_auto]";
				$result = mysql_query($query) or die("Query failed9 : " . mysql_error());
			}
		}
	}

	$play_list_array = array();
	$play_list_array_temp = array();

	// Обработаем ZAKAZ  //////////////////////////////////////////////////////////////////////////////////////////////

	$query = "SELECT * FROM `zakaz` ORDER BY `id` ASC";
	$result = mysql_query($query) or die("Query failed10 : " . mysql_error());
	$est_zakaz = mysql_fetch_array($result, MYSQL_ASSOC);
	if  (!$est_zakaz) {		echo "Нет заказов<br>";
	} else {
		echo "Есть заказы<br>";
	}

	if (
		$est_zakaz && $allow_zakaz &&
		(!$all_event2 || ($now_time + $vremya_trecka*60) < $all_event2[0]['time']) &&
		$now_time > $allow_time2
	) {
		$query = "SELECT * FROM `playlist` WHERE `now`=1";
		$result = mysql_query($query) or die("Query failed77 : " . mysql_error());
		$line = mysql_fetch_array($result, MYSQL_ASSOC);
		$play_mode = $line['playmode'];

	    // Только если сейчас играет не программа
	    if ($play_mode != 3) {
			$query = "SELECT * FROM `zakaz` ORDER BY `id` ASC";
			$result = mysql_query($query) or die("Query failed10 : " . mysql_error());

			// Запись заказов в плейлист
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				for ( $ips=0; $ips<NO_REPEAT; $ips++)  {
					if (
						!empty($net_povtorov_array[$ips]) and
						$net_povtorov_array[$ips] != $line['filename'] and
						file_exists($line['filename'])
					) {
						$play_list_array[] = $line['filename']."\n";
					}
				}
			}

			$play_list_array = array_unique($play_list_array);

			$query = "SELECT SUM(`duration`) as sum FROM `zakaz` ";
			$result = mysql_query($query) or die("Query failed11 " . mysql_error());
			$line = mysql_fetch_array($result, MYSQL_ASSOC);

			$zakaz_duration = $line['sum'];
			$allow_time_tmp = $allow_time_tmp + $zakaz_duration + $vremya_trecka*60;;
			$allow_time = $allow_time_tmp;

			foreach ($play_list_array as $line) {
				$play_list_text .= $line;
				$play_list_text_log .= "$tek_time (zakaz) ($zakaz_duration) ".$line;
			};

			$query = "DELETE FROM `zakaz` ";
			$result = mysql_query($query) or die("Query failed12 : " . mysql_error());
		}
	}


	$allow_time = $allow_time_tmp;

	$play_list_array = array();
	$play_list_array_temp = array();

	// Обрабатываем EVENT2 //////////////////////////////////////////////////////////////////////////////////////////////

	$playlist_id_now = 0;

	$query = "SELECT * FROM `playlist` WHERE `now`=1";
	$result = mysql_query($query) or die("Query failed13 : " . mysql_error());
	$line = mysql_fetch_array($result, MYSQL_ASSOC);

	if ($line) {
		$playlist_id_now = $line['id'];
	}

	$new_playlist = 0;


	if (isset($all_event2[0]) && $now_time > $all_event2[0]['time']) {
		$query = "UPDATE `playlist` SET `now`=0 WHERE `id`=".$playlist_id_now;
		$result = mysql_query($query) or die("Query failed14 : " . mysql_error());

	    // Запоминаем id старый
		$playlist_id_old = $playlist_id_now;
		$playlist_id_now = $all_event2[0]['id'];

		$query = "UPDATE `playlist` SET `now`=1 WHERE `id`=".$playlist_id_now;
		$result = mysql_query($query) or die("Query failed : " . mysql_error());

		$new_playlist = 1;
	}

	$query = "SELECT * FROM `playlist` WHERE `id`=".$playlist_id_now;
	$result = mysql_query($query) or die("Query failed15 : " . mysql_error());
	$line = mysql_fetch_array($result, MYSQL_ASSOC);

	$play_mode = $line['playmode'];

	if ($play_list_text || $new_playlist) {
        if ($new_playlist and $play_mode == "0") {
            $query = "UPDATE `songlist` SET `played` = 0 WHERE `id`=".$playlist_id_now;
            $result = mysql_query($query) or die("Query failed16 : " . mysql_error());   
        }
        
        if ($play_mode == "0") {
            $and_where = "and `played` = 0";
        } else {
            $and_where = "";
        }
        
		$query = "SELECT * FROM `songlist` WHERE `id`=".$playlist_id_now." $and_where ORDER BY `sort`";
		$result = mysql_query($query) or die("Query failed16 : " . mysql_error());
		if ($play_mode == "2") {
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				for ( $ips=0; $ips<NO_REPEAT; $ips++)  {
					if (
						!empty($net_povtorov_array[$ips]) and
						$net_povtorov_array[$ips] != $line['filename'] and
						file_exists($line['filename'])
					) {
						$play_list_array_temp[] = $line['filename']."\n";
					}
				}
			}

			$sem = array_rand($play_list_array_temp, 1);
			$sem = $play_list_array_temp[$sem];
			$play_list_array_temp = array();
			$play_list_array[] = $sem;
			$play_list_array = array_unique($play_list_array);

			$allow_time_tmp = $allow_time_tmp + $vremya_trecka*60;
			$allow_time = $allow_time_tmp;
			echo $allow_time." -allow_time_odin<br>";
		}

		// Случайно
		if ($play_mode == "1") { echo "1<br>";
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (file_exists($line['filename'])) {
					$play_list_array[] = $line['filename']."\n";
				}
			}

    		//  Удаляем недавно игравшие  (используем этот алгоритм если файлов очень много)
      		$play_list_array_skolko = count($play_list_array);
			for ( $ips=0; $ips<NO_REPEAT; $ips++ )  {
				for ( $ipn=0; $ipn<$play_list_array_skolko; $ipn++ )  {
                   	if (empty($net_povtorov_array[$ips]) or empty($play_list_array[$ipn])) {                    	continue;                    }
                    $poisk = strpos($play_list_array[$ipn], $net_povtorov_array[$ips]);
                    if ($poisk !== false) {
                    	unset($play_list_array[$ipn]);
                    }
    			}
			}

			shuffle($play_list_array);
		}

		// По порядку
		if ($play_mode == "0") { echo "0<br>";
   			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (file_exists($line['filename'])) {
					$play_list_array[] = $line['filename']."\n";
				}
			}
		}

		// Программа
		if ($play_mode == "3") {
			echo "3<br>";
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if ( file_exists($line['filename']) ) {
					$play_list_array[] = $line['filename']."\n";
					echo "play_mode 3 srabotalo<br>";
				}
			}

			// Добавляем массив в файл
			foreach ($play_list_array as $line) {
				$play_list_text .= $line;
			}

			$play_list_array = array();

			// Плюс файлы из старого id
    		$query_old = "SELECT * FROM `playlist` WHERE `id`=".$playlist_id_old;
			$result_old = mysql_query($query_old) or die("Query failed16 : " . mysql_error());

			while ($line_old = mysql_fetch_array($result_old, MYSQL_ASSOC)) {
				$play_mode_old = $line_old['playmode'];
			}
			// Плюс файлы из старого id
    		$query_old = "SELECT * FROM `songlist` WHERE `id`=".$playlist_id_old;
			$result_old = mysql_query($query_old) or die("Query failed16 : " . mysql_error());

    		// По порядку и фикс остального
			if ($play_mode_old != "1") {
				while ($line_old = mysql_fetch_array($result_old, MYSQL_ASSOC)) {
					$play_list_array[] = $line_old['filename']."\n";
				}

    			//  Удаляем недавно игравшие  (используем этот алгоритм если файлов очень много)
       			$play_list_array_skolko = count($play_list_array);
				for ( $ips=0; $ips<$net_povtorov; $ips++ )  {
	            	for ( $ipn=0; $ipn<$play_list_array_skolko; $ipn++ )  {
	                   	if (empty($net_povtorov_array[$ips]) or empty($play_list_array[$ipn])) {
	                   		continue;
	                   	}
	                   	$poisk = strpos($play_list_array[$ipn], $net_povtorov_array[$ips]);
	                   	if ($poisk !== false) {	                   		unset($play_list_array[$ipn]);
	                   	}
	               	}
				}
			}

			// Случайно
			if ($play_mode_old == "1") {
				while ($line_old = mysql_fetch_array($result_old, MYSQL_ASSOC)) {
					$play_list_array[] = $line_old['filename']."\n";
				}

				//  Удаляем недавно игравшие  (используем этот алгоритм если файлов очень много)
				$play_list_array_skolko = count($play_list_array);
				for ( $ips=0; $ips<NO_REPEAT; $ips++ )  {
	            	for ( $ipn=0; $ipn<$play_list_array_skolko; $ipn++ )  {
	                	if (empty($net_povtorov_array[$ips]) or empty($play_list_array[$ipn])) {	               			continue;	               		}
	               		$poisk = strpos($play_list_array[$ipn], $net_povtorov_array[$ips]);
	                	if ($poisk !== false) {	                   		unset($play_list_array[$ipn]);
	                   	}
	                }
				}

				shuffle($play_list_array);
			}

			// Запрос времени
			$query = "SELECT SUM(`duration`) as sum FROM `songlist` WHERE `id`=".$playlist_id_now;
			$result = mysql_query($query) or die("Query failed11 " . mysql_error());
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
     		// Запись времени
			$zakaz_duration = $line['sum'];
			$allow_time_tmp = $allow_time_tmp + $zakaz_duration + $vremya_trecka*60;;
			$allow_time = $allow_time_tmp;

			$query = "UPDATE `playlist` SET `now`=0 WHERE `id`=".$playlist_id_now;
			$result = mysql_query($query) or die("Query failed : " . mysql_error());
			$query = "UPDATE `playlist` SET `now`=1 WHERE `id`=".$playlist_id_old;
			$result = mysql_query($query) or die("Query failed : " . mysql_error());
		}


		// Обрезаем массив до заданной длины
    	array_splice($play_list_array, LIMIT_EVENT);
    	$play_list_array = array_unique($play_list_array);

		// Добавляем массив в файл
		foreach ($play_list_array as $line) {
			$play_list_text .= $line;
			$play_list_text_log .= "$tek_time (event2) ".$line;
		}

		if (!$ssh->noFirstTrack()) {
            $play_list_text = $update_filename."\n".$play_list_text;
        }    

		$file = fopen(PLAYLIST, "w");
		fwrite($file, $play_list_text);
		fclose($file);

		// Записываем $allow_time_tmp в файл
		if ($allow_time_tmp != $now_time) {
			$system_text = '<?php $allow_time = '.$allow_time_tmp.'; ?>';
			// Алов Тайм
			$file= fopen($request->getRadioPath()."_system.php", "w");
			fwrite($file, $system_text);
			fclose($file);

			// Лог
			/*
			$file_adres_log = $file_adres."log_event.txt";
			$file= fopen($file_adres_log, "w");
			fwrite($file, $play_list_text_log);
			fclose($file);
			*/
		}

		$event->updateEzstream();
	}

?>