<?php
	ob_start();
	include('include.php');

	$stat = Statistic::create();

	$gr_val = array();

	$query = "SELECT * FROM `statistic` WHERE `type` = 'graph' ORDER BY `time` DESC";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());

	$s = mysql_num_rows($result);


	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {		if ($_GET['type'] == "client") {			$client = $stat->getClient($line['client']);
			if (empty($client)) {				$client = "neizvestno";
			}

			$client = trim(preg_replace("/[^a-zA-Z0-9\s]+/", "", $client));

			if (!isset($gr_val[$client])) {				$gr_val[$client] = 1;			} else {    			$gr_val[$client]++;;
    		}
		} else {			$time = $line['time'];
			$name = "< 1 min";
			if ( $time < 60 ){
				if (!isset($gr_val[$name])) {					$gr_val[$name] = 1;				} else {					$gr_val[$name]++;				}
			}
			$name = "1-10 min";
			if ( ($time >= 60) and ($time < 600) ){				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}
			$name = "10-60 min";
			if ( ($time >= 600) and ($time < 3600) ){				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}
			$name = "1-7 hour";
			if ( ($time >= 3600) and ($time < 25200) ) {				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}
			$name = "> 7 hour";
			if ( $time >= 25200){				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}

		}
	}




	// постоение диаграмы
	GraphPie($gr_val);


	function GraphPie($ar) {    	global $s;
    	// размеры диаграмы
    	$diagramWidth = 450;
    	$diagramHeight = 250;
    	$legendOffset = 50;

    	// отсортируем по убыванию, сохраняя ключи
    	if ($_GET['type'] == "client") {arsort($ar);}

    	// наш скрипт будет объединять в один сектор все элементы, которые по отдельности не превыщают 1%
    	// Суммируем (можете использовать также функцию array_sum() )
    	$sum = 0;
    	foreach ($ar as $name => $val) {
    	    $sum += $val;
    	}

        if ($sum == 0) {
            $sum = 1;
        }

    	//узнаем сколько меньше 1%
    	$sumless1 = 0; // и их сумму
    	$countless1=$countgreater1=0;
    	foreach ($ar as $name => $val) {
    	    if ($val/$sum<0.01) {
    	        $sumless1 += $val;
                $countless1++;
    	    } else {
    	        $countgreater1++;
            }
    	}

    	 // создаем ихображение
    	$image = imageCreate($diagramWidth, $diagramHeight);

    	// цвета для фона и текста
    	$colorBackgr = imageColorAllocate($image, 255,255,255);
    	$colorText = imageColorAllocate($image, 76, 76, 76);
    	$colorWhite = imageColorAllocate($image, 255,255,255);
    	// цвета для наших секторов
    	$colors[0] = imagecolorallocate($image, 171, 203, 203);
    	$colors[1] = imagecolorallocate($image, 214, 179, 140);
    	$colors[2] = imagecolorallocate($image, 221, 221, 153);
    	$colors[3] = imagecolorallocate($image, 153, 174, 177);
    	$colors[4] = imagecolorallocate($image, 212, 199, 199);
    	$colors[5] = imagecolorallocate($image, 158, 151, 138);
    	$colors[6] = imagecolorallocate($image, 143, 179, 187);
    	$colors[7] = imagecolorallocate($image, 199, 184, 183);
    	$colors[8] = imagecolorallocate($image, 192, 205, 220);
    	$colors[9] = imagecolorallocate($image, 197, 164, 170);
    	$colors[10] = imagecolorallocate($image, 198, 120, 201);
    	$colors[11] = imagecolorallocate($image, 188, 130, 201);
    	$colors[12] = imagecolorallocate($image, 178, 140, 201);
    	$colors[13] = imagecolorallocate($image, 168, 150, 201);
    	$colors[14] = imagecolorallocate($image, 158, 160, 201);
    	$colors[15] = imagecolorallocate($image, 148, 170, 201);
    	$colors[16] = imagecolorallocate($image, 194,255,255);
    	$colors[17] = imagecolorallocate($image, 90,9,255);
    	$colors[18] = imagecolorallocate($image, 109,255,110);
    	$colors[19] = imagecolorallocate($image, 255,133,22);


    	// заполняем изображение цветом фона
    	imageFilledRectangle($image, 0, 0, $diagramWidth - 1, $diagramHeight - 1, $colorBackgr);

    	// начальный угол для сектора
    	$startAngle = 0;
    	$perc =360/$sum; // соотвествие градусов 1 проценту
    	$i=0; // для вывода порядка элемента в легенде и выбора цвета
    	foreach ($ar as $name => $val) {
    	// если текущий элемент больше 1%
    	  if ($val/$sum<0.01) // выходим из цикла
    	    break;

    	$font = "files/arial.ttf";

    	// конечный угол сектора
    	  $endAngle=$startAngle+$val*$perc;
    	  // сколько % у нашего элемента
    	  $percents=round(100*($val/$sum),2);

    	  // цветной квадратик в легенде
    	  imagefilledrectangle($image,250,$legendOffset+$i*15-9,260,$legendOffset+$i*15,$colors[$i]);
    	  // текст легенды
    	  ImageString($image , 2, 268, $legendOffset+$i*15-11, ($i+1).". ".$name." (".$percents."%)", $colorText);
    	  //imagettftext ($image, 10, 0, 265, $legendOffset+$i*15, $colorText, $font, ($i+1).". ".$name." (".$percents."%)");
    	  // сектор
    	  imagefilledarc($image, $diagramWidth/2-110, $diagramHeight/2, 200, 200, $startAngle, $endAngle, $colors[$i++], IMG_ARC_PIE);


    	// Вычисляем координаты подписи
    	  $tochka  = $endAngle-4;
    	  if ($percents > 6) {    	  	$name = substr($name,0,13);
    	    $pr = 360-$tochka; $tochka = $tochka+$pr*2;
    	    if ($_GET['type']=="client") {
    	    	//ImageString($image , 2, 9, $tochka, "      ".$name, $colorText);
    	    	imagettftext($image, 9, $tochka, 115, 125, $colorText, $font, "      ".$name);
    	  	} else {
    	  		//ImageString($image , 2, 9, $tochka, "            ".$name, $colorText);
    	    	imagettftext($image, 9, $tochka, 115, 125, $colorText, $font, "            ".$name);
    	    }
    	  }

    	  // следующий сектор в качестве начального угла будет использовать конечный угол текущего
    	  $startAngle=$endAngle;
    	}


    	// если есть элементы менее 1%
    	if ($countless1) {
    	 $endAngle=360;
    	  $percents=round(100*($sumless1/$sum),2);
    	  // цветной квадратик в легенде
    	  imagefilledrectangle($image,250,$legendOffset+$i*15-9,260,$legendOffset+$i*15,$colors[$i]);
    	 // текст в легенде
    	  ImageString($image , 2, 268, $legendOffset+$i*15-11, ($i+1).". "."Other"." (".$percents."%)", $colorText);
    	  //imagettftext ($image, 10, 0, 265, $legendOffset+$i*15, $colorText, $font, ($i+1).". "."Other"." (".$percents."%)");
    	  // сектор "Other"
    	  imagefilledarc($image, $diagramWidth/2-110, $diagramHeight/2, 200, 200, $startAngle, $endAngle, $colors[$i++], IMG_ARC_PIE);
    	}

    	ImageString($image , 2, 268, $diagramHeight-20, "Vsego: ".$s, $colorText);
    	//imagettftext ($image, 10, 0, 250, $diagramHeight-10, $colorText, $font, "Vsego: ".$s);

    	// выводим картинку
    	header("Content-type:  image/png");
    	imagepng($image);
    	imageInterlace($image, 1);
    	imageColorTransparent($image, $colorBackgr);
    	return;
	}


?>