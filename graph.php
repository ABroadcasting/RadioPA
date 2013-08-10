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
	ob_start();
	include('include.php');

	$stat = Statistic::create();

	$gr_val = array();

	$query = "SELECT * FROM `statistic` WHERE `type` = 'graph' ORDER BY `time` DESC";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());

	$s = mysql_num_rows($result);


	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($_GET['type'] == "client") {
			$client = $stat->getClient($line['client']);
			if (empty($client)) {
				$client = "neizvestno";
			}

			$client = trim(preg_replace("/[^a-zA-Z0-9\s]+/", "", $client));

			if (!isset($gr_val[$client])) {
				$gr_val[$client] = 1;
			} else {
    			$gr_val[$client]++;;
    		}
		} else {
			$time = $line['time'];
			$name = "< 1 min";
			if ( $time < 60 ){
				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}
			$name = "1-10 min";
			if ( ($time >= 60) and ($time < 600) ){
				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}
			$name = "10-60 min";
			if ( ($time >= 600) and ($time < 3600) ){
				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}
			$name = "1-7 hour";
			if ( ($time >= 3600) and ($time < 25200) ) {
				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}
			$name = "> 7 hour";
			if ( $time >= 25200){
				if (!isset($gr_val[$name])) {
					$gr_val[$name] = 1;
				} else {
					$gr_val[$name]++;
				}
			}

		}
	}

# Creating the diagram
	GraphPie($gr_val);

	function GraphPie($ar) {
    	global $s;
    	# Diagram size
    	$diagramWidth = 450;
    	$diagramHeight = 250;
    	$legendOffset = 50;

    	# Sorting down, saving keys
    	if ($_GET['type'] == "client") {arsort($ar);}

    	# our script will summurize all elements, less then 1% itself
    	# Summurizing(you may use function array_sum() )
    	$sum = 0;
    	foreach ($ar as $name => $val) {
    	    $sum += $val;
    	}

        if ($sum == 0) {
            $sum = 1;
        }

    	# Need to know how many less then 1%
    	$sumless1 = 0; // and its summary
    	$countless1=$countgreater1=0;
    	foreach ($ar as $name => $val) {
    	    if ($val/$sum<0.01) {
    	        $sumless1 += $val;
                $countless1++;
    	    } else {
    	        $countgreater1++;
            }
    	}

    	 # Creating Image
    	$image = imageCreate($diagramWidth, $diagramHeight);

    	# Text and background  colours
    	$colorBackgr = imageColorAllocate($image, 255,255,255);
    	$colorText = imageColorAllocate($image, 76, 76, 76);
    	$colorWhite = imageColorAllocate($image, 255,255,255);
    	# Sectors' colours
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

    	# Filling image with background colour
    	imageFilledRectangle($image, 0, 0, $diagramWidth - 1, $diagramHeight - 1, $colorBackgr);

    	# Start angle of the sector
    	$startAngle = 0;
    	$perc =360/$sum; // if grad 1%
    	$i=0; // for printing element in legend and choose colour
    	foreach ($ar as $name => $val) {
    	# if current element more then 1%
    	  if ($val/$sum<0.01) // out of the cycle
    	    break;

    	$font = "files/arial.ttf";

		# End angle of the sector
    	  $endAngle=$startAngle+$val*$perc;
    	  // Ammount % of the current element
    	  $percents=round(100*($val/$sum),2);

    	  # Legend square
    	  imagefilledrectangle($image,250,$legendOffset+$i*15-9,260,$legendOffset+$i*15,$colors[$i]);
    	  # Legend text
    	  ImageString($image , 2, 268, $legendOffset+$i*15-11, ($i+1).". ".$name." (".$percents."%)", $colorText);
    	  //imagettftext ($image, 10, 0, 265, $legendOffset+$i*15, $colorText, $font, ($i+1).". ".$name." (".$percents."%)");
    	  # Sector
    	  imagefilledarc($image, $diagramWidth/2-110, $diagramHeight/2, 200, 200, $startAngle, $endAngle, $colors[$i++], IMG_ARC_PIE);

    	# Calculating coordinates of subscription
    	  $tochka  = $endAngle-4;
    	  if ($percents > 6) {
    	  	$name = substr($name,0,13);
    	    $pr = 360-$tochka; $tochka = $tochka+$pr*2;
    	    if ($_GET['type']=="client") {
    	    	//ImageString($image , 2, 9, $tochka, "      ".$name, $colorText);
    	    	imagettftext($image, 9, $tochka, 115, 125, $colorText, $font, "      ".$name);
    	  	} else {
    	  		//ImageString($image , 2, 9, $tochka, "            ".$name, $colorText);
    	    	imagettftext($image, 9, $tochka, 115, 125, $colorText, $font, "            ".$name);
    	    }
    	  }

    	  # Next sector will use End angle of the previous sector as its Start angle
    	  $startAngle=$endAngle;
    	}

    	# For elements less than 1%
    	if ($countless1) {
    	 $endAngle=360;
    	  $percents=round(100*($sumless1/$sum),2);
    	  # Legend square
    	  imagefilledrectangle($image,250,$legendOffset+$i*15-9,260,$legendOffset+$i*15,$colors[$i]);
		  # Legend text
    	  ImageString($image , 2, 268, $legendOffset+$i*15-11, ($i+1).". "."Other"." (".$percents."%)", $colorText);
    	  //imagettftext ($image, 10, 0, 265, $legendOffset+$i*15, $colorText, $font, ($i+1).". "."Other"." (".$percents."%)");
    	  # Sector "Other"
    	  imagefilledarc($image, $diagramWidth/2-110, $diagramHeight/2, 200, 200, $startAngle, $endAngle, $colors[$i++], IMG_ARC_PIE);
    	}

    	ImageString($image , 2, 268, $diagramHeight-20, "Vsego: ".$s, $colorText);
    	//imagettftext ($image, 10, 0, 250, $diagramHeight-10, $colorText, $font, "Vsego: ".$s);

    	# Printing image
    	header("Content-type:  image/png");
    	imagepng($image);
    	imageInterlace($image, 1);
    	imageColorTransparent($image, $colorBackgr);
    	return;
	}
?>