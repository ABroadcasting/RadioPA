<?php
	$root_url = "http://informer.gismeteo.ru/xml/";
	$local_dir = $_SERVER["DOCUMENT_ROOT"]."/radio/files/";

	$xml_file = array();

	$xml_file[0]['name'] = "30823_1.xml"; //id или index города + "_1.xml"
	$xml_file[0]['city_name'] = "Улан-Удэ";
	$xml_file[0]['time_diff'] = 5;
	$xml_file[1]['name'] = "27612_1.xml"; //id или index города + "_1.xml"
	$xml_file[1]['city_name'] = "Москва";
	$xml_file[1]['time_diff'] = 5;

	$xml_file_city = $local_dir.$xml_file[0]['name'];

	if (file_exists($xml_file_city)) {
		if ((time() - filemtime($xml_file_city)) > 60*60*6) {
			for ($i=0;$i<sizeof($xml_file);$i++) {

				$xml_file_city = $root_url.$xml_file[$i]['name'];

				$content = GetFilePars($xml_file_city);

				$file = fopen($local_dir.$xml_file[$i]['name'], "w");
				fwrite($file, $content);
				fclose($file);
			}
		}
	} else {
		for ($i=0;$i<sizeof($xml_file);$i++) {

			$xml_file_city = $root_url.$xml_file[$i]['name'];

			$content = GetFilePars($xml_file_city);

			$file = fopen($local_dir.$xml_file[$i]['name'], "w");
			fwrite($file, $content);
			fclose($file);
		}
	}

	$forecast_final = "";

	for ($i=0;$i<sizeof($xml_file);$i++) {
		$xml_file_city = $local_dir.$xml_file[$i]['name'];

		$file = fopen($xml_file_city, "r");
		$content = fread($file, filesize($xml_file_city));
		fclose($file);

		$content = firstReplace($content);

		preg_match_all("/<FORECAST(.*?day=\"(\d+)\"\smonth=\"(\d+)\"\syear=\"(\d+)\"\shour=\"(\d+)\".*?)<\/FORECAST>/ism", $content, $sm);

		if (empty($sm)) {			continue;		}
		$forecast_now = $sm[1][0];

		$time_in_city = time() + 60*60*$xml_file[$i]['time_diff'];

		for ($j = 0; $j < 4; $j++) {
			$forecast_time = strtotime($sm[4][$j]."-".$sm[3][$j]."-".$sm[2][$j]." ".$sm[5][$j].":00:00");

			if (60*60*(-3) < ($time_in_city - $forecast_time) && ($time_in_city - $forecast_time) < 60*60*3) {
				$forecast_now = $sm[1][$j];
			}
		}

		preg_match("/tod=\"(\d+)\".*?cloudiness=\"(\d+)\".*?precipitation=\"(\d+)\".*?rpower=\"(\d+)\".*?\<TEMPERATURE\smax=\"([^\"]+)\"\smin=\"([^\"]+)\"\/\>/ism", $forecast_now, $sm);

		$tod = $sm[1];
		$cloudiness = $sm[2];
		$precipitation = $sm[3];
		$rpower = $sm[4];
		$temperature_max = $sm[5];
		$temperature_min = $sm[6];

		$icon_image = "";

		if (($cloudiness == 1 || $cloudiness == 0) && $precipitation == 10) {
			$icon_image = "/radio/images/p1.png";
		}
		elseif ($cloudiness == 2 || $cloudiness == 3) {
			$icon_image = "/radio/images/p3.png";
		}
		elseif ($precipitation > 3 && $precipitation < 9) {
			$icon_image = "/radio/images/p2.png";
		}
		else {
			$icon_image = "/radio/images/p1.png";
		}
	    /*
		if ($tod == "0") {
			$icon_image = "";
		}
		*/


		$t_min = strpos($temperature_min, "-");
		$t_max = strpos($temperature_max, "-");
		if ($t_min === false) $temperature_min = "+".$temperature_min;
		if ($t_max === false) $temperature_max = "+".$temperature_max;
		$temperature = $temperature_min."/".$temperature_max;

		$forecast_text = "";

		if ($cloudiness == 0 || $cloudiness == 1) {
			$forecast_text = "Ясно";
		}
		elseif ($cloudiness == 2) {
			$forecast_text = "Облачно";
		}
		elseif ($cloudiness == 3) {
			$forecast_text = "Пасмурно";
		}
		elseif ($precipitation == 3 || $precipitation == 5) {
			$forecast_text = "Дождь";
		}
		elseif ($precipitation == 6 || $precipitation == 7) {
			$forecast_text = "Снег";
		}
		elseif ($precipitation == 8) {
			$forecast_text = "Гроза";
		}
		else {
			$forecast_text = "Нет данных";
		}

		$forecast_text2 = "";

		if ($rpower == 0) {
			$forecast_text2 = ", возможен дождь";
		}
		elseif ($precipitation == 10) {
			$forecast_text2 = ", без осадков";
		}

			$ico_pogoda[$i] =  "<img src=$icon_image border=0 width=54 height=46>";
			$text_pogoda[$i] =  $temperature."&nbsp;&nbsp;&nbsp;&nbsp;".$forecast_text.$forecast_text2;

	}

	echo $forecast_final;


	function GetFilePars($uri) {

		$uri_body = "Не выполнен запрос!<br />";

		ob_start();
		$ch = curl_init();

		curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Accept: text/xml, application/xml, application/xhtml+xml, text/html;q=0.9, text/plain;q=0.8, image/png, */*;q=0.5", "Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7", "Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3"));

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.8) Gecko/20071008 Firefox/2.0.0.8");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiefile");
		curl_setopt($ch, CURLOPT_COOKIEJAR, "cookiefile");

		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		$okay = curl_exec($ch);
		curl_close($ch);
		if (1 == $okay) {
			$uri_body = ob_get_contents();
		}
		ob_end_clean();

		return $uri_body;
	}

	function firstReplace($v_file) {		$search = array ("'\n'", "'\r'");
		$replace = array ("", "");
		$v_file = preg_replace($search, $replace, $v_file);
		return ($v_file);
	}

?>