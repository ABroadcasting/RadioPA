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
	include('top.php');
    ob_end_flush();
	/* Доступ к модулю */
    if (!empty($user) and $user['admin'] != 1) {
    	$security->denied();
	}

	$req_pl_id = intval($request->getGetVar('playlist_id'));

	$playlist = Playlist::create();
	$song = Song::create();
	$add = AddTracks::create($song);
	$add->setChmod();
	$add->setPlaylist($req_pl_id);
	$manager = Meneger::create();
?>
	<div class="body">
		<div class="title">Добавление песен в «<?=$playlist->getTitle($req_pl_id)?>»
		</div>
		<div class="border">
			<br><br><br>
<?php
	$ch = 0;
	$chs = 0;
	if ($request->hasGetVar('add_directory')) {
		$security->accessCheck($request->getGetVar('add_directory'));
    	$folder = $add->getRealPath($request->getGetVar('add_directory'));
        $tracks_array = $add->getAllFilesFromDirectory($folder);

	    foreach ($tracks_array as $filename) {
	    	$add->addTrack($filename);
		    echo    "<div style=\"visibility:hidden; display: none; position:absolute;left:-5000px\">".$filename."</div>";
		    $vsego_liniy = count($tracks_array);
		    $start = (int)((100*$ch)/$vsego_liniy);
		    $end = (int)(100-$start);
		    $skolko = 10;
		    if ($vsego_liniy > 100) $skolko = 15;
		    if ($vsego_liniy > 500) $skolko = 25;
		    if ($vsego_liniy > 1000) $skolko = 50;
		    if ($chs < $skolko) {
		    	$chs = $chs +1;
		    } else {
		    	$chs = 0;
?>
			<div style="position: absolute; top:190px; left: 16px;">
			 	<table border="0" width="400" cellspacing="0" cellpadding="0">
					<tr>
						<td class="graph_g2_2" width="<?php echo $start ?>%" align="center"></td>
						<td width="1"><img src="images/blank.gif" border="0"></td>
						<td class="graph_g2_1" width="<?php echo $end ?>%" align="center"></td>
					</tr>
				</table>
				<table style="position:relative; top:-19px; left:0px;" border="0" width="400" cellspacing="0" cellpadding="0">
					<tr>
						<td><div class="minitext">Выполнено: <?php echo $start; ?>%</div></td>

					</tr>
				</table>
			</div>
<?php
		}
    	$ch++;
    }
?>
		    <div style="position: absolute; top: 190px; left: 16px;">
			 	<table border="0" width="400" cellspacing="0" cellpadding="0">
					<tr>
						<td class="graph_g2_2" width="10%" align="center"></td>
					</tr>
				</table>
				<table style="position:relative; top:-19px; left:0px;" border="0" width="400" cellspacing="0" cellpadding="0">
					<tr>
						<td>
						<div class="minitext">Выполнено: 100%</div>
						</td>
					</tr>
				</table>
			</div>
<?php
    }

	if ($request->hasGetVar('filename')) {
		$security->accessCheck($request->getGetVar('filename'));
		$add->addTrack($request->getGetVar('filename'));

		$fold = $manager->getFileFolder($request->getGetVar('filename'));
		$location = "manager.php?start=".$manager->getStart()."&search=".$manager->getSearch()."&fold=".$fold."&playlist_id=".$req_pl_id;
        echo "<b>Файл успешно добавлен в плейлист</b>.";
	}
    
    if ($request->hasGetVar('add_directory')) {
        echo "<b>Каталог успешно добавлен в плейлист</b>.";
        $location = "manager.php?start=".$manager->getStart()."&search=".$manager->getSearch()."&fold=".$manager->getFold()."&playlist_id=".$req_pl_id;
    }    
?>
			
			<br><br>
			<input class="button" type="button" value="Вернуться назад" name="back" onClick="location.href='<?=$location?>'">
			&nbsp;&nbsp;
			<input class="button" type="button" value="Завершить добавление" name="back" onClick="location.href='playlist_view.php?playlist_id=<?=$req_pl_id?>'">
		</div>
	</div>

